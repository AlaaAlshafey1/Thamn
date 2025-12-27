<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TapPayment;
use App\Services\TapPaymentService;

class PaymentController extends Controller
{
    private $tapPaymentService;

    public function __construct(TapPaymentService $tapPaymentService)
    {
        $this->tapPaymentService = $tapPaymentService;
    }

    // إنشاء طلب دفع للـ Order
    public function payOrder($order_id)
    {
        $order = Order::with('user')->findOrFail($order_id);

        $customerName  = $order->user->name ?? 'Unknown Customer';
        $customerEmail = $order->user->email ?? 'noemail@example.com';
        $customerPhone = $order->user->phone ?? '0000000000';

        $amount = $order->total_price ?? 0;

        if ($amount <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'قيمة الطلب غير صالحة للدفع.'
            ], 400);
        }

        $payment = $this->tapPaymentService->createPayment(
            $amount ,
            "SAR",
            [
                "first_name" => $customerName,
                "email" => $customerEmail,
                "phone" => [
                    "country_code" => "966",
                    "number" => $customerPhone
                ]
            ],
            route('payment.callback', ['order_id' => $order->id])
        );

        TapPayment::create([
            'order_id'      => $order->id,
            'charge_id'     => $payment['id'] ?? null,
            'amount'        => $amount,
            'status'        => $payment['status'] ?? 'INITIATED',
            'response_data' => json_encode($payment),
        ]);

        return response()->json([
            'status' => true,
            'payment' => $payment
        ]);
    }


    public function callback(Request $request)
    {
        $orderId = $request->query('order_id');
        $chargeId = $request->query('tap_id');

        $payment = TapPayment::where('charge_id', $chargeId)->first();
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment not found.'], 404);
        }

        $statusResponse = $this->tapPaymentService->getPaymentStatus($chargeId);

        $status = $statusResponse['status'] ?? 'FAILED';

        $payment->status = strtoupper($status) === 'CAPTURED' ? 'paid' : 'failed';
        $payment->response_data = json_encode($statusResponse);
        $payment->save();

        $payment->order->update([
            'payment_status' => $payment->status,
        ]);

        return response()->json([
            'status' => true,
            'order_id' => $orderId,
            'payment_status' => $payment->status,
            'payment_details' => $statusResponse
        ]);
    }


    public function callbackError(Request $request)
    {
        $chargeId = $request->query('tap_id');

        return response()->json([
            'status' => false,
            'message' => 'عملية الدفع فشلت.',
            'charge_id' => $chargeId
        ]);
    }
}
