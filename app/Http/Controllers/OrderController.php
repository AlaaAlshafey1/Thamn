<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\RunAiEvaluationJob;
use App\Models\Order;
use App\Notifications\OrderEvaluated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ThamnEvaluationService;
use App\Notifications\ExpertEvaluatedOrderAdminNotification;
use App\Mail\ExpertValuationMail;
use App\Mail\ValuationResultMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\OrderAcceptedByExpertNotification;
use App\Http\Traits\FCMOperation;

class OrderController extends Controller
{
    use FCMOperation;
    public function index()
    {
        if (auth()->user()->hasRole('expert')) {
            // الطلبات النشطة: اللي متاح (expert_id null) أو اللي هو مسكه وشغال عليه
            $activeOrders = Order::where(function ($q) {
                $q->where('expert_id', Auth::id())
                    ->orWhereNull('expert_id');
            })
                ->whereIn('status', ['pending', 'orderReceived', 'beingEstimated', 'paid', 'beingReEstimated'])
                ->where(function ($q) {
                    $q->where('expert_evaluated', 0)->orWhereNull('expert_evaluated');
                })
                ->when(auth()->user()->category_id, function ($q) {
                    return $q->where(function ($sub) {
                        $sub->where('category_id', auth()->user()->category_id)
                            ->orWhereNull('category_id');
                    });
                })
                ->whereHas('details', function ($q) {
                    $q->whereHas('question', function ($q2) {
                        $q2->where('type', 'rateTypeSelection');
                    })->where(function ($q3) {
                        $q3->whereHas('option', function ($q4) {
                            $q4->whereIn('badge', ['expert', 'best', 'ai']);
                        })->orWhereIn('value', ['expert', 'best', 'ai']);
                    });
                })
                ->with('user')
                ->latest()
                ->get();

            // الطلبات السابقة: اللي هو خلصها
            $completedOrders = Order::where('expert_id', Auth::id())
                ->where(function ($q) {
                    $q->whereIn('status', ['estimated', 'evaluated', 'finished', 'completed'])
                        ->orWhere(function ($sub) {
                            $sub->whereIn('status', ['beingEstimated', 'beingReEstimated'])
                                ->where('expert_evaluated', 1);
                        });
                })
                ->with('user')
                ->latest()
                ->get();

            return view('orders.index', compact('activeOrders', 'completedOrders'));
        } else {
            $orders = Order::with('user')
                ->latest()
                ->paginate(20);
            return view('orders.index', compact('orders'));
        }
    }


    public function create()
    {

        return view('orders.create');
    }



    public function show(Order $order)
    {
        if (auth()->user()->hasRole('expert')) {
            // نحدد من يُسمح له بالدخول:
            $isAssignedExpert = $order->expert_id === auth()->id();
            $isOpenAiOrder = $order->expert_id === null
                && in_array($order->status, ['beingEstimated', 'orderReceived'])
                && $order->evaluation_type === 'ai';

            if (!$isAssignedExpert && !$isOpenAiOrder) {
                return redirect()->route('orders.index')
                    ->with('error', 'يجب عليك استلام الطلب أولاً من لوحة التحكم قبل التمكن من عرضه أو تقييمه.');
            }
        }

        $order->load(['details', 'files', 'user', 'payments']);
        
        $aiRatings = \App\Models\AiValuationRating::where('is_active', true)->get();

        return view('orders.show', compact('order', 'aiRatings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'details' => 'required|string',
            'total_price' => 'required|numeric|min:0',
            'evaluation_type' => 'required|string',
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'total_price' => $request->total_price,
            'evaluation_type' => $request->evaluation_type,
            'status' => 'pending',
        ]);


        return redirect()->route('orders.index')->with('success', 'تم إنشاء الطلب بنجاح');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'تم تحديث حالة الطلب');
    }


    public function expertEvaluate(Request $request, Order $order)
    {
        // التأكد إن المستخدم هو خبير
        $user = Auth::user();

        if (!$user->hasRole('expert')) {
            abort(403, 'غير مسموح لك بهذا الإجراء');
        }

        // منع الخبير من تقييم الأوردر مرة أخرى إذا تم تقييمه بالفعل وليس في وضع إعادة التقييم
        if (in_array($order->status, ['estimated', 'evaluated', 'finished', 'completed']) && $order->status !== 'beingReEstimated') {
            return back()->with('error', 'لقد قمت بتقييم هذا الطلب بالفعل ولا يمكن تعديله.');
        }

        if ($order->evaluation_type === 'ai') {
            $request->validate([
                'ai_valuation_rating_id' => 'required|exists:ai_valuation_ratings,id',
                'expert_reasoning' => 'required|string|max:5000',
            ]);
            
            $expertPrice = $order->ai_price ?? $order->total_price;
            $expertMinPrice = $order->ai_min_price ?? $expertPrice * 0.8;
            $expertMaxPrice = $order->ai_max_price ?? $expertPrice * 1.2;
            $aiValuationRatingId = $request->ai_valuation_rating_id;
        } else {
            $request->validate([
                'expert_price' => 'required|numeric|min:0',
                'expert_min_price' => 'nullable|numeric|min:0',
                'expert_max_price' => 'nullable|numeric|min:0',
                'expert_reasoning' => 'required|string|max:5000',
            ]);
            
            $expertPrice = $request->expert_price;
            $expertMinPrice = $request->expert_min_price ?? $request->expert_price * 0.8;
            $expertMaxPrice = $request->expert_max_price ?? $request->expert_price * 1.2;
            $aiValuationRatingId = null;
        }

        // تحديث الأوردر
        $order->update([
            'expert_id' => $user->id,
            'expert_price' => $expertPrice,
            'expert_min_price' => $expertMinPrice,
            'expert_max_price' => $expertMaxPrice,
            'expert_reasoning' => $request->expert_reasoning,
            'ai_valuation_rating_id' => $aiValuationRatingId,
            'expert_evaluated' => true,
            'total_price' => $expertPrice,
            'status' => in_array($order->evaluation_type, ['expert', 'ai']) ? 'estimated' : ($order->status === 'beingReEstimated' ? 'beingReEstimated' : 'beingEstimated'),
            'evaluated_at' => $order->evaluated_at ?? now(),
        ]);
        $commissionType = \App\Models\Setting::where('key', 'expert_commission_type')->value('value') ?? 'fixed';
        $commissionValue = \App\Models\Setting::where('key', 'expert_commission_value')->value('value') ?? 10;

        $expertEarnings = 10; // Default fallback
        if ($commissionType === 'percentage') {
            // Calculate percentage based on the order's total payment for this service.
            // Since it's usually fixed (e.g. 15 SAR), we can use total_price or the standard known rate.
            // But we will use the actual order payment (total_price) if it's set, else fallback.
            $orderPayment = $order->total_price > 0 ? $order->total_price : 15;
            $expertEarnings = ($orderPayment * $commissionValue) / 100;
        } else {
            $expertEarnings = $commissionValue;
        }

        $user->balance += $expertEarnings;
        $user->save();
        if ($order->evaluation_type === 'best') {
            // ─── حساب السعر النهائي الهجين تلقائياً ────────────────────────
            $aiPrice = $order->ai_price;
            $expertPrice = $request->expert_price;
            $thamnPrice = $aiPrice ? round(($aiPrice + $expertPrice) / 2, 2) : $expertPrice;
            $minPrice = round($thamnPrice * 0.93);
            $maxPrice = round($thamnPrice * 1.07);

            // الـ reasoning يكون من الـ AI (وصف احترافي أدق)
            $aiReasoning = $order->ai_reasoning ?? $request->expert_reasoning;

            $order->refresh();
            $order->update([
                'thamn_price' => $thamnPrice,
                'thamn_min_price' => $minPrice,
                'thamn_max_price' => $maxPrice,
                'thamn_reasoning' => $aiReasoning,
                'thamn_by' => null, // تلقائي (ليس بواسطة أدمن يدوياً)
                'thamn_at' => now(),
                'total_price' => $thamnPrice,
                'status' => $order->status === 'beingReEstimated' ? 'reEstimated' : 'estimated',
            ]);

            // Notify Customer — التثمين اكتمل
            $order->user->notify(new OrderEvaluated($order, 'thamn'));

            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('اكتمل تثمين منتجك 🎉', 'Your evaluation is ready! 🎉', request()),
                    lang("تم تثمين منتجك رقم #{$order->id} بنجاح. تفضل اطلع على النتيجة الآن.", "Your product #{$order->id} has been evaluated. Check the result now!", request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_evaluated_thamn']]
                );
            }

            // Notify Customer via WhatsApp
            try {
                if ($order->user->phone) {
                    $whatsapp = app(\App\Services\WhatsAppService::class);
                    $msg = \App\Services\WhatsAppService::getTemplate('order_ready_customer', ['id' => $order->id]);
                    $whatsapp->sendMessage($order->user->phone, $msg);
                }
            } catch (\Throwable $e) {
                \Log::error('Best-type Customer WhatsApp Failed: ' . $e->getMessage());
            }

            // Inform Admin (record only)
            try {
                $expertName = $user->first_name . ' ' . $user->last_name;
                $categoryName = $order->category->name_ar ?? 'القسم';
                Mail::to('thmmnapplic@gmail.com')->send(new \App\Mail\SystemNotificationMail(
                    "تم اعتماد التثمين الهجين للطلب #{$order->id} تلقائياً",
                    "يا مدير، قام الخبير {$expertName} من قسم {$categoryName} بتقييم الطلب رقم {$order->id}. تم احتساب السعر الهجين تلقائياً: {$thamnPrice} ريال وإبلاغ العميل.",
                    route('orders.show', $order->id)
                ));
            } catch (\Throwable $e) {
                \Log::error('Best-type Admin Info Email Failed: ' . $e->getMessage());
            }

            $successMsg = 'تم تقييم الأوردر بنجاح واحتساب السعر الهجين تلقائياً وبشرنا العميل!';
        } else {
            // Regular expert type or ai type (evaluated by expert) flow: notify user directly
            $evalTypeStr = $order->evaluation_type === 'ai' ? 'ai' : 'expert';
            $order->user->notify(new OrderEvaluated($order, $evalTypeStr));

            // إرسال إشعار للأدمن (Database)
            $admins = User::role('superadmin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new ExpertEvaluatedOrderAdminNotification($order, $user));
            }

            // Notify Customer via WhatsApp & Email with Invoice/Report
            try {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('order_ready_customer', ['id' => $order->id]);

                // إضافة الفاتورة / شهادة التثمين
                $invoiceLink = \Illuminate\Support\Facades\URL::signedRoute('valuation-order.pdf', ['order' => $order->id]);
                $msg .= "\n\nلتحميل الفاتورة يرجى زيارة الرابط التالي:\n" . $invoiceLink;

                $whatsapp->sendMessage($order->user->phone, $msg);

                // الإيميل للمستخدم مع النتيجة والفاتورة المعنية
                if ($order->user->email) {
                    Mail::to($order->user->email)->send(new ValuationResultMail($order, $evalTypeStr));
                }
            } catch (\Throwable $e) {
                \Log::error('Expert Valuation Notification Failed: ' . $e->getMessage());
            }

            // FCM Notification to Customer (Saudi Phrasing)
            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('تم تقييم منتجك بنجاح ✅', 'Your evaluation is ready! ✅', request()),
                    lang("تم تقييم منتجك رقم #{$order->id} بنجاح وتم إرسال التقرير/الفاتورة. تفضل اطلع عليه الآن.", "Your product #{$order->id} has been successfully evaluated and invoice sent. Check it now!", request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_evaluated_expert']]
                );
            }

            $successMsg = 'تم تقييم الأوردر بنجاح وبشرنا العميل بالنتيجة!';
        }

        // ─── شكر الخبير على إنجاز التثمين ────────────────────────────
        // WhatsApp
        try {
            if ($user->phone) {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $thankMsg = \App\Services\WhatsAppService::getTemplate('expert_order_done', ['id' => $order->id]);
                $whatsapp->sendMessage($user->phone, $thankMsg);
            }
        } catch (\Throwable $e) {
            \Log::error('Expert Thank-You WhatsApp Failed: ' . $e->getMessage());
        }

        // FCM push للخبير
        $expertTokens = $user->getFcmTokens();
        if (!empty($expertTokens)) {
            $this->notifyByFirebase(
                lang('أحسنت! 🎉 تم إرسال التثمين للعميل', 'Well done! 🎉 Valuation sent to client', request()),
                lang("أتممت تثمين الطلب رقم #{$order->id} بنجاح. شكراً على دقتك واحترافيتك.", "You've successfully completed evaluation for order #{$order->id}. Thank you for your professionalism.", request()),
                $expertTokens,
                ['data' => ['user_id' => $user->id, 'order_id' => $order->id, 'type' => 'expert_evaluation_done']]
            );
        }

        return back()->with('success', $successMsg);
    }

    public function thamnEvaluate(Request $request, Order $order, ThamnEvaluationService $evaluationService)
    {
        $request->validate([
            'thamn_reasoning' => 'nullable|string|max:5000',
        ]);

        $evaluationService->runThamnValuation($order);

        if (!$order->thamn_price) {
            return back()->with('error', 'يجب وجود تقييم AI وتقييم خبير أولاً');
        }

        $order->update([
            'thamn_reasoning' => $request->thamn_reasoning,
            'total_price' => $order->thamn_price, // السعر النهائي
            'status' => 'estimated', // Update status so customer can see it
        ]);

        $order->user->notify(new OrderEvaluated($order, 'thamn'));

        // Notify Customer via WhatsApp
        try {
            if ($order->user->phone) {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('order_ready_customer', ['id' => $order->id]);
                $whatsapp->sendMessage($order->user->phone, $msg);
            }
        } catch (\Exception $e) {
            \Log::error('Thamn Evaluation WhatsApp Failed: ' . $e->getMessage());
        }

        // FCM Notification to Customer (Saudi Phrasing)
        $tokens = $order->user->getFcmTokens();
        if (!empty($tokens)) {
            $this->notifyByFirebase(
                lang('تم اعتماد التقييم النهائي ⚖️', 'Final Evaluation Approved ⚖️', request()),
                lang("تم اعتماد التقييم النهائي لمنتجك رقم #{$order->id} بنجاح. تفضل اطلع عليه الآن.", "The final evaluation for your product #{$order->id} has been approved successfully. Check it now!", request()),
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_evaluated_thamn']]
            );
        }

        return back()->with('success', 'تم اعتماد تقييم ثمن بنجاح');
    }

    // OrderController.php
    public function assignExpert(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->expert_id && $order->expert_id != auth()->id()) {
            return response()->json(['status' => false, 'message' => 'هذا الطلب تم استلامه بالفعل من قبل خبير آخر']);
        }

        // تعيين الخبير الحالي على الأوردر
        $order->update([
            'expert_id' => auth()->id(),
            'status' => 'beingEstimated',
            'accepted_at' => now(),
        ]);

        // Notify Other Experts & Customer & Current Expert
        try {
            $whatsapp = app(\App\Services\WhatsAppService::class);

            // 1. Notify Customer
            $customerMsg = \App\Services\WhatsAppService::getTemplate('order_evaluating_customer', ['id' => $order->id]);
            $whatsapp->sendMessage($order->user->phone, $customerMsg);

            // 2. Notify Current Expert (Urgency)
            $expertMsg = \App\Services\WhatsAppService::getTemplate('order_accepted_expert', ['id' => $order->id]);
            $whatsapp->sendMessage(auth()->user()->phone, $expertMsg);

            // 3. Notify Other Experts in same category
            $others = \App\Models\User::role('expert')
                ->where('category_id', $order->category_id)
                ->where('id', '!=', auth()->id())
                ->get();

            $othersMsg = \App\Services\WhatsAppService::getTemplate('order_accepted_other', ['id' => $order->id]);
            foreach ($others as $other) {
                if ($other->phone) {
                    $whatsapp->sendMessage($other->phone, $othersMsg);
                }
                // Notify Other via Email
                Mail::to($other->email)->send(new \App\Mail\SystemNotificationMail(
                    'معوض خير.. الطلب استلمه غيرك',
                    "الطلب رقم {$order->id} استلمه خبير ثاني. خلك قريب للطلبات الجاية يا بطل.",
                    route('orders.index')
                ));
            }

            // Notify Customer via Notification
            $order->user->notify(new OrderAcceptedByExpertNotification($order));

            // Notify Customer via Email
            Mail::to($order->user->email)->send(new \App\Mail\SystemNotificationMail(
                'بدينا العمل على طلبك!',
                "جارى العمل على الطلب بتاعكم وسوف يتم الرد ف حد اقصى 24 ساعة للطلب رقم {$order->id}.",
                route('orders.show', $order->id)
            ));

        } catch (\Exception $e) {
            \Log::error('Assign Expert Notifications Failed: ' . $e->getMessage());
        }

        return response()->json(['status' => true, 'message' => 'تم تعيين الأوردر لك، شد حيلك بالتقييم!']);
    }

    public function updatePrice(Request $request, Order $order)
    {
        if (!auth()->user()->hasRole('expert')) {
            abort(403);
        }



        $request->validate([
            'total_price' => 'required|numeric|min:0'
        ]);

        $order->update([
            'total_price' => $request->total_price,
            'status' => 'estimated',
            'expert_id' => auth()->id()
        ]);

        return back()->with('success', 'تم تقييم السعر بنجاح');
    }




    public function aiEvaluate(Order $order)
    {
        // التأكد إن المستخدم أدمن أو سوبر أدمن
        if (!auth()->user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'غير مسموح لك بهذا الإجراء');
        }

        // زيادة عداد إعادة التقييم إذا كان هناك تقييم سابق
        if ($order->ai_price) {
            $order->increment('re_evaluation_count');
        }

        // تشغيل التقييم في الخلفية — لا ينتظر انتهاءه
        RunAiEvaluationJob::dispatch($order);

        return back()->with('success', '⏳ جاري تقييم الطلب بالذكاء الاصطناعي في الخلفية، ستظهر النتيجة خلال لحظات، يمكنك تحديث الصفحة بعد قليل.');
    }

    public function generateVirtualImage(Order $order)
    {
        // بناء وصف مبسط للصورة بناءً على تفاصيل الطلب
        $qaLines = [];
        foreach ($order->details as $detail) {
            $question = $detail->question->question_en ?? $detail->question->question_ar;
            $answer = $detail->option->option_en ?? $detail->option->option_ar ?? $detail->value;
            if ($question && $answer) {
                $qaLines[] = "{$question}: {$answer}";
            }
        }
        $qaText = implode(", ", $qaLines);
        $category = $order->category->name_en ?? 'product';

        // توجيه لإنشاء صورة بخلفية بيضاء
        $prompt = "A highly realistic, professional studio photograph of a {$category} with the following specifications: {$qaText}. Pure white background, centered, well lit, high quality.";

        try {
            $imageUrl = app(\App\Services\OpenAIService::class)->generateImage($prompt);
            if ($imageUrl) {
                $imageContents = file_get_contents($imageUrl);
                $filename = 'ai_generated_manual_' . \Illuminate\Support\Str::random(10) . '.png';
                $path = 'orders/images/' . $filename;

                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $imageContents);

                \App\Models\OrderFiles::create([
                    'order_id' => $order->id,
                    'file_path' => $path,
                    'file_name' => $filename,
                    'type' => 'image',
                ]);

                return back()->with('success', 'تم توليد الصورة الافتراضية بنجاح وإرفاقها بالطلب.');
            }

            return back()->with('error', 'تعذر توليد الصورة، حاول مرة أخرى.');
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء توليد الصورة: ' . $e->getMessage());
        }
    }
}
