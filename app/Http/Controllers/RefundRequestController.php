<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundRequestController extends Controller
{
    /**
     * Display a listing of refund requests for admin.
     */
    public function index()
    {
        if (!Auth::user()->hasRole('superadmin')) {
            abort(403);
        }

        $refunds = RefundRequest::with(['order', 'user'])->latest()->get();
        return view('refunds.index', compact('refunds'));
    }

    /**
     * Show form to create refund request.
     */
    public function create(Order $order)
    {
        // Check if user owns the order and order is expired
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'expired') {
            return back()->with('error', 'هذا الطلب غير متاح للاسترداد حالياً');
        }

        // Check if already requested
        if ($order->refundRequest) {
            return back()->with('info', 'لقد قمت بإرسال طلب استرداد مسبقاً لهذا الطلب');
        }

        return view('refunds.create', compact('order'));
    }

    /**
     * Store refund request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'bank_name' => 'required|string',
            'iban' => 'required|string',
            'account_holder_name' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'expired') {
            return back()->with('error', 'هذا الطلب غير متاح للاسترداد');
        }

        RefundRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'iban' => $request->iban,
            'account_holder_name' => $request->account_holder_name,
            'amount' => $order->total_price,
            'status' => 'pending'
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'تم إرسال طلب الاسترداد بنجاح، سيتم التواصل معك قريباً');
    }

    /**
     * Process refund (Admin).
     */
    public function process(Request $request, $id)
    {
        if (!Auth::user()->hasRole('superadmin')) {
            abort(403);
        }

        $refund = RefundRequest::findOrFail($id);
        $request->validate(['status' => 'required|in:processed,rejected']);

        $refund->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        if ($request->status === 'processed') {
            $refund->order->update(['status' => 'refunded']);
        }

        return back()->with('success', 'تم تحديث حالة طلب الاسترداد');
    }
}
