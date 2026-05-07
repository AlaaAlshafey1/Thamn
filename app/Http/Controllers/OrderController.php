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
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\OrderAcceptedByExpertNotification;

class OrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('expert')) {
            // الطلبات النشطة: اللي متاح (expert_id null) أو اللي هو مسكه وشغال عليه
            $activeOrders = Order::where(function ($q) {
                $q->where('expert_id', Auth::id())
                    ->orWhereNull('expert_id');
            })
            ->whereIn('status', ['orderReceived', 'beingEstimated', 'paid'])
            ->when(auth()->user()->category_id, function ($q) {
                return $q->where('category_id', auth()->user()->category_id);
            })
            ->with('user')
            ->latest()
            ->get();

            // الطلبات السابقة: اللي هو خلصها
            $completedOrders = Order::where('expert_id', Auth::id())
                ->whereIn('status', ['evaluated', 'finished', 'completed'])
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
            'status' => 'estimated' // ممكن تحدد حالة الأوردر بعد التقييم
        ]);
        $user->balance += 10;
        $user->save();
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

        return back()->with('success', 'تم تقييم الأوردر بنجاح وبشرنا العميل بالنتيجة!');
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
            $order->user->notify(new OrderEvaluated($order, 'ai'));

            return back()->with('success', 'تم تشغيل تقييم AI بنجاح');
        } catch (\Throwable $e) {
            return back()->with('error', 'فشل تقييم AI: ' . $e->getMessage());
        }
    }

}
