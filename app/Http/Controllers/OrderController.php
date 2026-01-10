<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
    if(auth()->user()->hasRole('expert')){

        $orders = Order::where("expert_id",Auth::id())->orwhere("expert_id",null)->with('user')
            ->latest()
            ->paginate(20);

    }else{

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
        $order->load(['details','files','user','payments']);

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


    return redirect()->route('orders.index')->with('success','تم إنشاء الطلب بنجاح');
}

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success','تم تحديث حالة الطلب');
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
            'expert_reasoning' => 'required|string|max:1000',
        ]);

        // تحديث الأوردر
        $order->update([
            'expert_id' => $user->id,
            'expert_price' => $request->expert_price,
            'expert_reasoning' => $request->expert_reasoning,
            'expert_evaluated' => true,
            'total_price' => $request->expert_price, // تحديث السعر النهائي للأوردر
            'status' => 'estimated' // ممكن تحدد حالة الأوردر بعد التقييم
        ]);

        return back()->with('success','تم تقييم الأوردر بنجاح');
    }

    public function thamnEvaluate(Request $request, Order $order)
    {
        $request->validate([
            'thamn_reasoning' => 'nullable|string|max:1000',
        ]);

        $thamnPrice = $order->calculateThamnPrice();

        if (!$thamnPrice) {
            return back()->with('error', 'يجب وجود تقييم AI وتقييم خبير أولاً');
        }

        $order->update([
            'thamn_price'     => $thamnPrice,
            'thamn_reasoning' => $request->thamn_reasoning,
            'thamn_by'        => auth()->id(),
            'thamn_at'        => now(),
            'total_price'    => $thamnPrice, // السعر النهائي
        ]);

        return back()->with('success', 'تم اعتماد تقييم ثمن بنجاح');
    }

// OrderController.php
public function assignExpert(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
    ]);

    $order = Order::findOrFail($request->order_id);

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


}
