<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\NewRefundRequestNotification;
use App\Notifications\RefundProcessedNotification;
use App\Mail\SystemNotificationMail;

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

        $refund = RefundRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'iban' => $request->iban,
            'account_holder_name' => $request->account_holder_name,
            'amount' => $order->total_price,
            'status' => 'pending'
        ]);

        // Notify Admins
        $admins = User::role('superadmin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewRefundRequestNotification($refund));
        }

        // Send Direct Email to Admin
        try {
            $adminEmail = 'alaa.alshafey12345@gmail.com';
            Mail::to($adminEmail)->send(new \App\Mail\SystemNotificationMail(
                'طلب استرداد مبلغ جديد!',
                "العميل {$refund->user->first_name} قدم طلب استرداد لمبلغ: " . number_format($refund->amount, 2) . " ريال للطلب رقم #{$refund->order_id}.\nبيانات البنك: {$refund->bank_name} - {$refund->iban}\nيرجى مراجعة الطلب في لوحة التحكم.",
                route('refunds.index')
            ));
        } catch (\Exception $e) {
            \Log::error('Admin Refund Email Failed: ' . $e->getMessage());
        }

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

            // Notify Customer
            $refund->user->notify(new RefundProcessedNotification($refund));

            // Send Email to Customer
            try {
                Mail::to($refund->user->email)->send(new SystemNotificationMail(
                    'تم استرداد مبلغ طلبك من ثمن',
                    "عزيزي العميل، نعتذر منك بشدة عن عدم تمكننا من تقييم طلبك رقم #{$refund->order_id} في الوقت المحدد. نود إبلاغك بأنه تم تحويل مبلغ الاسترداد إلى حسابك البنكي بنجاح. شكراً لتفهمك.",
                    route('orders.show', $refund->order_id)
                ));
            } catch (\Exception $e) {
                \Log::error('Customer Refund Email Failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'تم تحديث حالة طلب الاسترداد');
    }
}
