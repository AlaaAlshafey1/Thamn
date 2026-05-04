<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RefundRequest;
use App\Models\User;
use App\Notifications\NewRefundRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class RefundController extends Controller
{
    /**
     * Get orders eligible for refund that haven't submitted details yet.
     * This is what the mobile app will check to show the "Refund Modal".
     */
    public function getPendingRefunds()
    {
        $user = Auth::user();
        
        $orders = Order::where('user_id', $user->id)
            ->where('status', 'expired')
            ->whereDoesntHave('refundRequest')
            ->with('category')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * Submit refund payment details from mobile.
     * Once submitted, the order will no longer appear in getPendingRefunds.
     */
    public function submitRefundDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'bank_name' => 'required|string',
            'iban' => 'required|string',
            'account_holder_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات غير مكتملة',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::find($request->order_id);

        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'غير مسموح لك بهذا الإجراء'
            ], 403);
        }

        if ($order->status !== 'expired') {
            return response()->json([
                'status' => false,
                'message' => 'هذا الطلب غير متاح للاسترداد حالياً'
            ], 400);
        }

        // Check if already submitted
        if ($order->refundRequest) {
             return response()->json([
                'status' => false,
                'message' => 'تم إرسال بيانات الاسترداد مسبقاً لهذا الطلب'
            ], 400);
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
                'طلب استرداد مبلغ جديد (من الموبايل)!',
                "العميل {$refund->user->first_name} قدم طلب استرداد لمبلغ: " . number_format($refund->amount, 2) . " ريال للطلب رقم #{$refund->order_id}.\nبيانات البنك: {$refund->bank_name} - {$refund->iban}\nيرجى مراجعة الطلب في لوحة التحكم.",
                route('refunds.index')
            ));
        } catch (\Exception $e) {
            \Log::error('Admin Refund API Email Failed: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال بيانات الاسترداد بنجاح، ستظهر في لوحة التحكم للمراجعة',
            'data' => $refund
        ]);
    }
}
