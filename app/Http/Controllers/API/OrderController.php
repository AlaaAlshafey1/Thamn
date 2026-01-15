<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderFiles;
use App\Models\QuestionStep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;

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
            'category_id' => $request->category_id ?? 1 ,
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
        $images = [];
        $files = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('orders/' . $order->id . '/images', 'public');
                $images[] = Storage::url($path);

                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'image',
                ]);
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('orders/' . $order->id . '/files', 'public');
                $files[] = Storage::url($path);

                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'file',
                ]);
            }
        }

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
                'files' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'file')
                    ->get()
                    ->map(function ($file) {
                        return [
                            'id'   => $file->id,
                            'name' => $file->file_name,
                            'url'  => full_url($file->file_path),
                        ];
                    }),

                'images' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'image')
                    ->get()
                    ->map(function ($file) {
                        return [
                            'id'   => $file->id,
                            'name' => $file->file_name,
                            'url'  => full_url($file->file_path),
                        ];
                    }),
                'answers' => $responseAnswers,
            ],
        ]);
    }


    public function update(Request $request, $orderId)
    {
        $user = $request->user();

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

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

        $order->update([
            'status' => $request->status ?? $order->status,
            'payload' => json_encode($request->answers),
        ]);

        OrderDetails::where('order_id', $order->id)->delete();

        $totalPrice = 0;
        $details = [];

        foreach ($request->answers as $answer) {
            $optionIds = is_array($answer['option_id'] ?? null)
                ? $answer['option_id']
                : [$answer['option_id'] ?? null];

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


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('orders/' . $order->id . '/images', 'public');

                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'image',
                ]);
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('orders/' . $order->id . '/files', 'public');

                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'file',
                ]);
            }
        }

        $responseAnswers = collect($details)->map(function ($d) {
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
                'files' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'file')
                    ->get()
                    ->map(fn ($file) => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => full_url($file->file_path),
                    ]),
                'images' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'image')
                    ->get()
                    ->map(fn ($file) => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => full_url($file->file_path),
                    ]),
                'answers' => $responseAnswers,
            ]
        ]);
    }

    public function show(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'payload' => json_decode($order->payload, true),
                'files' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'file')
                    ->get()
                    ->map(fn ($file) => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => full_url($file->file_path),
                    ]),
                'images' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'image')
                    ->get()
                    ->map(fn ($file) => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => full_url($file->file_path),
                    ]),
            ]
        ]);
    }

    public function destroy(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->delete(); // soft delete

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully'
        ]);
    }

    public function cancel(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }

    public function getOrders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())->with('category');

        // --------------------- فلترة حسب status مباشر ---------------------
        if ($request->filled('status')) {
            $validStatuses = [
                'orderReceived','beingEstimated','beingReEstimated','estimated',
                'estimatedAndStored','reEstimated','inComplete','notPaid','cancelled'
            ];

            if (!in_array($request->status, $validStatuses)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid status'
                ], 400);
            }

            $query->where('status', $request->status);
        }

        // --------------------- فلترة حسب group predefined ---------------------
        if ($request->filled('group')) {
            $groups = [
                'inPricing' => ['beingEstimated','beingReEstimated'],
                'priced' => ['estimated','estimatedAndStored','reEstimated'],
                'incompleteOrCancelled' => ['inComplete','notPaid','cancelled']
            ];

            if (!array_key_exists($request->group, $groups)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid group'
                ], 400);
            }

            $query->whereIn('status', $groups[$request->group]);
        }

        // --------------------- فلترة category ---------------------
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // --------------------- فلترة range تاريخ ---------------------
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->get();

        return response()->json([
            'status' => true,
            'orders' => $orders
        ]);
    }

    public function result(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'result' => [
                'total_price' => $order->total_price,
                'valuation_payload' => json_decode($order->payload) ?? null
            ]
        ]);
    }


    public function reEvaluate(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Reset previous evaluation
        $order->update([
            'ai_min_price' => null,
            'ai_max_price' => null,
            'ai_price' => null,
            'ai_confidence' => null,
            'ai_reasoning' => null,
            'expert_price' => null,
            'expert_reasoning' => null,
            'thamn_price' => null,
            'thamn_by' => null,
            'thamn_at' => null,
        ]);

        // تشغيل التقييم من جديد حسب طريقة المستخدم الأصلية
        $rateTypeAnswer = $order->details()
            ->whereHas('question', fn($q) => $q->where('type', 'rateTypeSelection'))
            ->first();

        $evaluationType = $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value;

        switch ($evaluationType) {
            case 'ai':
                $this->runAiEvaluation($order);
                break;

            case 'expert':
                $this->sendToExperts($order);
                break;

            case 'best':
                $this->runPricingEvaluation($order);
                break;

            default:
                Log::warning('Unknown evaluation type on re-evaluate', [
                    'order_id' => $order->id
                ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order re-evaluation requested successfully',
            'order_id' => $order->id,
        ]);
    }

    private function runAiEvaluation(Order $order): void
    {
        $qaText = '';

        foreach ($order->details as $detail) {
            $question = $detail->question->question_ar ?? null;
            $answer   = $detail->option->option_ar ?? $detail->value ?? null;

            if ($question && $answer) {
                $qaText .= "- {$question}: {$answer}\n";
            }
        }
        $prompt = <<<PROMPT
أنت خبير محترف في تثمين السلع في السوق السعودي.

الدولة: المملكة العربية السعودية
العملة: ريال سعودي (SAR)
فئة السلعة: {$order->category->name_ar}

تفاصيل السلعة:
{$qaText}

ممنوع كتابة أي نص خارج JSON.

{
"min_price": رقم,
"max_price": رقم,
"recommended_price": رقم,
"currency": "SAR",
"confidence": رقم,
"reasoning": "شرح مختصر"
}
PROMPT;

        $aiResult = app(OpenAIService::class)->evaluateProduct($prompt);

        $order->update([
            'ai_min_price'  => $aiResult['min_price'] ?? null,
            'ai_max_price'  => $aiResult['max_price'] ?? null,
            'ai_price'      => $aiResult['recommended_price'] ?? null,
            'ai_confidence' => $aiResult['confidence'] ?? null,
            'ai_reasoning'  => $aiResult['reasoning'] ?? null,
        ]);
    }

// ===============================
// إرسال للأخصائيين للتقييم
// ===============================
private function sendToExperts(Order $order): void
{
    // نغير حالة الأوردر
    $order->update([
        'status' => 'waiting_expert',
        'expert_evaluated' => 0, // لم يتم تقييمه بعد
    ]);

    // مثال: اختيار أول خبير (يمكن تعديل حسب المنطق لديك)
    $expert = \App\Models\User::role('expert')->first();
    if ($expert) {
        $order->update([
            'expert_id' => $expert->id
        ]);

        // إرسال Notification للخبير
        $expert->notify(new \App\Notifications\OrderAssignedToExpert($order));
    }

    // إرسال Notification للمستخدم
    $order->user->notify(new \App\Notifications\OrderSentForExpertEvaluation($order));

    Log::info("Order sent to expert", [
        'order_id' => $order->id,
        'expert_id' => $order->expert_id ?? null
    ]);
}

// ===============================
// تثمين ثمن المنتج
// ===============================
private function runPricingEvaluation(Order $order): void
{
    // مثال: حساب متوسط بين AI و Expert إذا متوفرين
    $aiPrice = $order->ai_price ?? null;
    $expertPrice = $order->expert_price ?? null;

    $thamnPrice = null;

    if ($aiPrice && $expertPrice) {
        $thamnPrice = round(($aiPrice + $expertPrice) / 2, 2);
    } elseif ($aiPrice) {
        $thamnPrice = $aiPrice;
    } elseif ($expertPrice) {
        $thamnPrice = $expertPrice;
    }

    $order->update([
        'thamn_price' => $thamnPrice,
        'thamn_by' => auth()->id() ?? null,
        'thamn_at' => now(),
    ]);

    // إرسال Notification للمستخدم
    $order->user->notify(new \App\Notifications\OrderThamnPriceCalculated($order));

    Log::info("Thamn price calculated", [
        'order_id' => $order->id,
        'thamn_price' => $thamnPrice
    ]);
}

public function resendOrder($orderId)
{
    $order = Order::findOrFail($orderId);

    // نرسل Notification للمستخدم بأن الطلب تم إعادة إرساله
    $order->user->notify(new \App\Notifications\OrderResent($order));

    return response()->json([
        'status' => true,
        'message' => 'Order request resent successfully',
        'order_id' => $order->id,
    ]);
}
public function sendToMarket(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);

    $request->validate([
        'send' => 'required|boolean', // true => نرسل للسوق، false => لا
    ]);

    if (!$order->total_price) {
        return response()->json([
            'status' => false,
            'message' => 'Product price must be calculated before sending to market.'
        ], 400);
    }

    if ($request->send) {
        // تحديث الحالة للسوق
        $order->update([
            'status' => 'sent_to_market'
        ]);

        // Notification للمستخدم أن المنتج أرسل للسوق
        $order->user->notify(new \App\Notifications\OrderSentToMarket($order));

        return response()->json([
            'status' => true,
            'message' => 'Order sent to market successfully',
            'order_id' => $order->id,
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Order not sent to market',
        'order_id' => $order->id,
    ]);
}


}
