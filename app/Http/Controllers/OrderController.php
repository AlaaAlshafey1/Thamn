<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->latest()
            ->paginate(20);

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

}
