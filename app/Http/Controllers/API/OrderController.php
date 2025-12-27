<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\QuestionStep;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'status' => 'nullable|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable',
            'answers.*.sub_option_id' => 'nullable',
            'answers.*.value' => 'nullable|string',
            'answers.*.price' => 'nullable|numeric',
            'answers.*.status' => 'nullable|integer',
            'answers.*.stageing' => 'nullable|integer',
        ]);


        $order = Order::create([
            'user_id' => $user->id,
            'status' => $request->status ?? 0,
            'payload' => json_encode($request->answers),
        ]);

        $totalPrice = 0;
        $details = [];

        foreach ($request->answers as $answer) {
            $optionIds = is_array($answer['option_id'] ?? null) ? $answer['option_id'] : [$answer['option_id'] ?? null];

            foreach ($optionIds as $optionId) {
                if ($optionId === null) continue;

                $details[] = OrderDetails::create([
                    'order_id' => $order->id,
                    'question_id' => $answer['question_id'],
                    'option_id' => $optionId,
                    'sub_option_id' => $answer['sub_option_id'] ?? null,
                    'value' => $answer['value'] ?? null,
                    'price' => $answer['price'] ?? null,
                    'status' => $answer['status'] ?? 1,
                    'stageing' => $answer['stageing'] ?? null,
                ]);

                $totalPrice += $answer['price'] ?? 0;
            }
        }

        $order->update(['total_price' => $totalPrice]);


        $responseAnswers = collect($details)->map(function($d) {
            return [
                'question_id' => $d->question_id,
                'option_id' => $d->option_id,
                'sub_option_id' => $d->sub_option_id,
                'value' => $d->value,
                'price' => $d->price,
                'status' => $d->status,
                'stageing' => $d->stageing,
            ];
        });

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
                'total_price' => $totalPrice,
                'answers' => $responseAnswers,
            ],
        ]);
    }
}
