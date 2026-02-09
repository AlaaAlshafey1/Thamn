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

class OrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('expert')) {
            // خبير: يشوف اللي متاح (expert_id null) أو اللي هو مسكه (expert_id = أنا)
            $orders = Order::where(function ($q) {
                $q->where('expert_id', Auth::id())
                    ->orWhereNull('expert_id');
            })
                ->whereIn('status', ['orderReceived', 'beingEstimated'])
                ->with('user')
                ->latest()
                ->paginate(20);
        } else {
            $orders = Order::with('user')
                ->latest()
                ->paginate(20);
        }

        return view('orders.index', compact('orders'));
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
            'expert_reasoning' => 'required|string|max:1000',
        ]);

        // تحديث الأوردر
        $order->update([
            'expert_id' => $user->id,
            'expert_price' => $request->expert_price,
            'expert_min_price' => $request->expert_min_price ?? $request->expert_price * 0.8,
            'expert_max_price' => $request->expert_max_price ?? $request->expert_price * 1.2,
            'expert_reasoning' => $request->expert_reasoning,
            'expert_evaluated' => true,
            'total_price' => $request->expert_price, // تحديث السعر النهائي للأوردر
            'status' => 'estimated' // ممكن تحدد حالة الأوردر بعد التقييم
        ]);
        $user->balance += 4;
        $user->save();
        $order->user->notify(new OrderEvaluated($order, 'expert'));

        // إرسال إشعار للأدمن (Database)
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ExpertEvaluatedOrderAdminNotification($order, $user));
        }

        // إرسال إيميل للأدمن
        try {
            Mail::to(config('mail.from.address'))->send(new ExpertValuationMail($order, $user));
        } catch (\Throwable $e) {
            \Log::error('Expert Valuation Mail Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم تقييم الأوردر بنجاح وتم إرسال إشعار للمستخدم');
    }

    public function thamnEvaluate(Request $request, Order $order, ThamnEvaluationService $evaluationService)
    {
        $request->validate([
            'thamn_reasoning' => 'nullable|string|max:1000',
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
            'status' => 'beingEstimated' // ممكن تعدل الحالة حسب النظام
        ]);

        return response()->json(['status' => true, 'message' => 'تم تعيين الأوردر لك']);
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
