<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
            ->where(function($q) {
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
                        $q4->whereIn('badge', ['expert', 'best']);
                    })->orWhereIn('value', ['expert', 'best']);
                });
            })
            ->with('user')
            ->latest()
            ->get();

            // الطلبات السابقة: اللي هو خلصها
            $completedOrders = Order::where('expert_id', Auth::id())
                ->where(function($q) {
                    $q->whereIn('status', ['estimated', 'evaluated', 'finished', 'completed'])
                      ->orWhere(function($sub) {
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
            $isAiOnly = $order->details()->whereHas('question', function($q) {
                $q->where('type', 'rateTypeSelection');
            })->where(function($q) {
                $q->whereHas('option', function($q2) {
                    $q2->where('badge', 'ai');
                })->orWhere('value', 'ai');
            })->exists();

            if ($isAiOnly) {
                return redirect()->route('orders.index')->with('error', 'هذا الطلب مخصص للتقييم بواسطة الذكاء الاصطناعي فقط ولا يمكنك الدخول إليه.');
            }

            if ($order->expert_id !== auth()->id()) {
                return redirect()->route('orders.index')->with('error', 'يجب عليك استلام الطلب أولاً من لوحة التحكم قبل التمكن من عرضه أو تقييمه.');
            }
        }

        $order->load(['details', 'files', 'user', 'payments']);

        return view('orders.show', compact('order'));
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

        $request->validate([
            'expert_price' => 'required|numeric|min:0',
            'expert_min_price' => 'nullable|numeric|min:0',
            'expert_max_price' => 'nullable|numeric|min:0',
            'expert_reasoning' => 'required|string|max:5000',
        ]);

        // تحديث الأوردر
        $order->update([
            'expert_id' => $user->id,
            'expert_price' => $request->expert_price,
            'expert_min_price' => $request->expert_min_price ?? $request->expert_price * 0.8,
            'expert_max_price' => $request->expert_max_price ?? $request->expert_price * 1.2,
            'expert_reasoning' => $request->expert_reasoning,
            'expert_evaluated' => true,
            'total_price' => $request->expert_price,
            'status' => $order->evaluation_type === 'expert' ? 'estimated' : ($order->status === 'beingReEstimated' ? 'beingReEstimated' : 'beingEstimated'),
            'evaluated_at' => $order->evaluated_at ?? now(),
        ]);
        $user->balance += 10;
        $user->save();
        if ($order->evaluation_type === 'best') {
            // Notify Admin (Email & WhatsApp)
            try {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                
                $expertName = $user->first_name . ' ' . $user->last_name;
                $categoryName = $order->category->name_ar ?? 'القسم';
                $productName = "الطلب رقم {$order->id}";
                $msg = "يا مدير، الخبير {$expertName} من قسم {$categoryName} قام بتقييم {$productName} والتثمين نوعه تثمين احترافي (هجين). ادخل قيموا ف اقرب وقت.";
                
                // Admin WhatsApp - specific numbers
                $adminPhones = ['+201021443985', '+966503955098'];
                foreach ($adminPhones as $phone) {
                    $whatsapp->sendMessage($phone, $msg);
                }

                // Admin Email
                $adminEmail = 'alaa.alshafey12345@gmail.com';
                Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                    "خبير قيم طلب تثمين احترافي رقم #{$order->id}",
                    $msg,
                    route('orders.show', $order->id)
                ));
            } catch (\Throwable $e) {
                \Log::error('Expert Valuation best-type Admin Notification Failed: ' . $e->getMessage());
            }

            // DB Notification to Admin
            $admins = User::role('superadmin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new ExpertEvaluatedOrderAdminNotification($order, $user));
            }

            // FCM Notification to Customer (Saudi Phrasing)
            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('أوشكنا على النهاية ⏳', 'Almost done ⏳', request()),
                    lang('أوشكنا على النهاية، أرجو منك الصبر. طلبك الآن في المراجعة النهائية.', 'We are almost done, please be patient. Your order is in final review.', request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_waiting_admin']]
                );
            }

            $successMsg = 'تم تقييم الأوردر بنجاح وبشرنا الأدمن بالنتيجة!';
        } else {
            // Regular expert type flow: notify user directly
            $order->user->notify(new OrderEvaluated($order, 'expert'));

            // إرسال إشعار للأدمن (Database)
            $admins = User::role('superadmin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new ExpertEvaluatedOrderAdminNotification($order, $user));
            }

            // Notify Customer via WhatsApp & Email
            try {
                $whatsapp = app(\App\Services\WhatsAppService::class);
                $msg = \App\Services\WhatsAppService::getTemplate('order_ready_customer', ['id' => $order->id]);
                $whatsapp->sendMessage($order->user->phone, $msg);
                
                // Email to Customer
                Mail::to($order->user->email)->send(new \App\Mail\SystemNotificationMail(
                    'بشرنااااك! تقييم طلبك صار جاهز',
                    "بشرى سارة! تقييم طلبك رقم {$order->id} صار جاهز الحين.\nتفضل شيك عليه بالمنصة وعطنا رايك.",
                    route('orders.show', $order->id)
                ));
            } catch (\Throwable $e) {
                \Log::error('Expert Valuation Notification Failed: ' . $e->getMessage());
            }

            // FCM Notification to Customer (Saudi Phrasing)
            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    lang('تم تقييم منتجك بنجاح 🧡', 'Your evaluation is ready! 🧡', request()),
                    lang("تم تقييم منتجك رقم #{$order->id} بنجاح من قبل خبيرنا. تفضل اطلع عليه الآن.", "Your product #{$order->id} has been successfully evaluated by our expert. Check it now!", request()),
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'order_evaluated_expert']]
                );
            }

            $successMsg = 'تم تقييم الأوردر بنجاح وبشرنا العميل بالنتيجة!';
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


    public function aiEvaluate(Order $order, ThamnEvaluationService $evaluationService)
    {


        try {
            $evaluationService->runAiEvaluation($order);

            // تسجيل وقت التقييم
            if (!$order->evaluated_at) {
                $order->update(['evaluated_at' => now()]);
            }

            return back()->with('success', 'تم تشغيل تقييم AI بنجاح');
        } catch (\Throwable $e) {
            return back()->with('error', 'فشل تقييم AI: ' . $e->getMessage());
        }
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
