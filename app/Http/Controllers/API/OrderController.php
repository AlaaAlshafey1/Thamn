<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderFiles;
use App\Models\QuestionOption;
use App\Models\QuestionStep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

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

            }
        }

            $rateTypeAnswer = $order->details
                ->first(fn ($detail) =>
                    $detail->question?->type === 'rateTypeSelection'

                );


                if( $rateTypeAnswer){

                        $totalPrice = $rateTypeAnswer->option->price    ;

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
        $order = Order::with([
            'details.question',
            'details.option',
            'files',
            'category'
        ])
        ->where('id', $orderId)
        ->where('user_id', $request->user()->id)
        ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */
        $title = $order->category->name_en
            ?? $order->category->name_ar
            ?? 'Valuation Order';

        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */
        $images = $order->files
            ->where('type', 'image')
            ->map(fn ($file) => full_url($file->file_path))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | IS IN MARKET
        |--------------------------------------------------------------------------
        */
        $isInMarket = $order->status === 'sent_to_market';

        /*
        |--------------------------------------------------------------------------
        | GROUP DETAILS FOR UI
        |--------------------------------------------------------------------------
        */
        $groups = [];

        foreach ($order->details as $detail) {
            if (!$detail->question) {
                continue;
            }

            $groupId = $detail->question->step_id ?? 0;
            $groupTitle = $detail->question->step?->title_ar
                ?? $detail->question->step?->title_en
                ?? 'تفاصيل';

            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'id' => $groupId,
                    'label' => $groupTitle,
                    'items' => []
                ];
            }

            $groups[$groupId]['items'][] = [
                'id' => $detail->id,
                'label' => $detail->question->question_ar
                    ?? $detail->question->question_en,
                'value' => $detail->option?->option_ar
                    ?? $detail->option?->option_en
                    ?? $detail->value,
            ];
        }

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'title' => $title,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'isInMarket' => $isInMarket,
                'images' => $images,
                'details' => array_values($groups),
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
    $query = Order::where('user_id', Auth::id())
        ->with([
            'category',
            'details.option',
            'files'
        ]);

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

    /* ===================== MAP RESPONSE ===================== */
    $orders = $orders->map(function ($order) {

        // ===== TITLE =====
        $titleParts = [];

        // Category name
        $categoryName = $order->category?->name_en ?? $order->category?->name_ar;
        if ($categoryName) {
            $titleParts[] = $categoryName;
        }

        // details values (unique)
        $detailValues = $order->details
            ->map(fn ($d) =>
                $d->option?->option_en
                ?? $d->option?->option_ar
                ?? $d->value
            )
            ->filter()
            ->unique()
            ->values()
            ->take(2)
            ->toArray();

        $titleParts = array_merge($titleParts, $detailValues);

        // fallback
        if (empty($titleParts)) {
            $titleParts[] = "Order #{$order->id}";
        }

        $title = implode(' - ', $titleParts);

        // ===== FILES =====
        $files = $order->files
            ->where('type', 'file')
            ->map(fn ($file) => [
                'url' => full_url($file->file_path),
            ])
            ->values();

        // ===== IMAGES =====
        $images = $order->files
            ->where('type', 'image')
            ->map(fn ($file) => [
                'id' => $file->id,
                'name' => $file->file_name,
                'url' => full_url($file->file_path),
            ])
            ->values();

        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'category_id' => $order->category_id,
            'status' => $order->status,
            'pricing_method' => $order->pricing_method,
            'total_price' => $order->total_price,
            'payload' => $order->payload,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'ai_min_price' => $order->ai_min_price,
            'ai_max_price' => $order->ai_max_price,
            'ai_price' => $order->ai_price,
            'ai_confidence' => $order->ai_confidence,
            'ai_reasoning' => $order->ai_reasoning,
            'expert_id' => $order->expert_id,
            'expert_evaluated' => $order->expert_evaluated,
            'expert_price' => $order->expert_price,
            'thamn_price' => $order->thamn_price,
            'thamn_reasoning' => $order->thamn_reasoning,
            'expert_reasoning' => $order->expert_reasoning,
            'thamn_by' => $order->thamn_by,
            'thamn_at' => $order->thamn_at,
            'deleted_at' => $order->deleted_at,

            // ===== extra fields =====
            'title' => $title,
            'category' => [
                'id' => $order->category?->id,
                'name_ar' => $order->category?->name_ar,
                'name_en' => $order->category?->name_en,
                'description_ar' => $order->category?->description_ar,
                'description_en' => $order->category?->description_en,
                'image' => $order->category?->image,
                'is_active' => $order->category?->is_active,
                'created_at' => $order->category?->created_at,
                'updated_at' => $order->category?->updated_at,
            ],
            'isInMarket' => $order->status === 'sent_to_market',
            'images' => $images,
            'files' => $files,
        ];
    });

    return response()->json([
        'status' => true,
        'orders' => $orders
    ]);
}



public function result(Request $request, $orderId)
{
    $order = Order::with([
        'details.question',
        'details.option',
        'category',
        'files',
    ])
    ->where('id', $orderId)
    ->firstOrFail();

    /* ===================== CATEGORY ===================== */
    $category = $order->category->name_en
        ?? $order->category->name_ar
        ?? '';

    /* ===================== DESCRIPTION ===================== */
    $year = null;
    $condition = null;

    foreach ($order->details as $detail) {
        if (!$detail->question) {
            continue;
        }

        switch ($detail->question->type) {
            case 'year':
                $year = $detail->value
                    ?? $detail->option?->option_en
                    ?? $detail->option?->option_ar;
                break;

            case 'condition':
                $condition = $detail->option?->option_en
                    ?? $detail->option?->option_ar
                    ?? $detail->value;
                break;
        }
    }

    $description = trim(implode(' ', array_filter([
        $category,
        $year,
        $condition ? "– {$condition}" : null,
    ])));

    /* ===================== MAIN IMAGE ===================== */
    $imageFile = $order->files->firstWhere('type', 'image');

    $image = '';
    if($order->category->name_en == "cars"){

        $image =  URL::asset('/assets/img/Cars-result.jpeg');
    }


    /* ===================== PRICES ===================== */
    $prices = [
        'highest' => $order->ai_max_price ? (float) $order->ai_max_price : null,
        'average' => (float) (
            $order->thamn_price
            ?? $order->ai_price
            ?? 0
        ),
        'lowest'  => $order->ai_min_price ? (float) $order->ai_min_price : null,
    ];

    /* ===================== DETAILS ===================== */
    $details = [];

    foreach ($order->details as $detail) {
        if (!$detail->question) {
            continue;
        }

        $title = $detail->question->question_en
            ?? $detail->question->question_ar;

        $value = $detail->option?->option_en
            ?? $detail->option?->option_ar
            ?? $detail->value;

        if ($title && $value) {
            $details[] = [
                'title' => $title,
                'value' => (string) $value,
            ];
        }
    }
$reasoning =
    $order->thamn_reasoning
    ?? $order->expert_reasoning
    ?? $order->ai_reasoning
    ?? '';

    /* ===================== RESPONSE ===================== */
    return response()->json([
        'category'    => $category,
        'description' => $description,
        'image'       => $image,
        'reasoning'       => $reasoning ,
        'prices'      => $prices,
        'details'     => $details,
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
