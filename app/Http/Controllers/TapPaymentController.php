<?php

namespace App\Http\Controllers;

use App\Models\TapPayment;
use Illuminate\Http\Request;

class TapPaymentController extends Controller
{
    /**
     * قائمة المدفوعات
     */
    public function index()
    {
        $payments = TapPayment::with('order.user')
            ->latest()
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    /**
     * تفاصيل الدفع
     */
    public function show(TapPayment $payment)
    {
        $payment->load(['order.details.question', 'order.details.option', 'order.user']);

        return view('payments.show', compact('payment'));
    }

    /**
     * حذف الدفع (اختياري)
     */
    public function destroy(TapPayment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'تم حذف الدفع بنجاح');
    }
}
