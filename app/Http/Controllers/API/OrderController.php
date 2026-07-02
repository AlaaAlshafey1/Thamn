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
use App\Http\Traits\FCMOperation;

class OrderController extends Controller
{
    use FCMOperation;

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
            'category_id' => $request->category_id ?? 1,
            'status' => $request->status ?? 0,
            'payload' => json_encode($request->answers),
        ]);

        $totalPrice = 0;
        $details = [];

        // Validate option_ids upfront to prevent foreign key constraint violations
        $allOptionIds = [];
        foreach ($request->answers as $answer) {
            $opt = $answer['option_id'] ?? null;
            if (is_array($opt)) {
                $allOptionIds = array_merge($allOptionIds, $opt);
            } elseif ($opt !== null) {
                $allOptionIds[] = $opt;
            }
            $subOpt = $answer['sub_option_id'] ?? null;
            if ($subOpt !== null) {
                $allOptionIds[] = $subOpt;
            }
        }
        $validOptionIds = \App\Models\QuestionOption::whereIn('id', $allOptionIds)->pluck('id')->toArray();

        foreach ($request->answers as $answer) {
            $optionIds = is_array($answer['option_id'] ?? null) ? $answer['option_id'] : [$answer['option_id'] ?? null];

            // لو مفيش option_id → الإجابة عبارة عن value نصية (سؤال حر)
            if (empty(array_filter($optionIds))) {
                $details[] = OrderDetails::create([
                    'order_id'     => $order->id,
                    'question_id'  => $answer['question_id'],
                    'option_id'    => null,
                    'sub_option_id'=> (isset($answer['sub_option_id']) && in_array($answer['sub_option_id'], $validOptionIds)) ? $answer['sub_option_id'] : null,
                    'value'        => $answer['value'] ?? null,
                    'price'        => $answer['price'] ?? null,
                    'status'       => $answer['status'] ?? 1,
                    'stageing'     => $answer['stageing'] ?? null,
                ]);
                continue;
            }

            foreach ($optionIds as $optionId) {
                if ($optionId === null || !in_array($optionId, $validOptionIds))
                    continue;

                $details[] = OrderDetails::create([
                    'order_id'     => $order->id,
                    'question_id'  => $answer['question_id'],
                    'option_id'    => $optionId,
                    'sub_option_id'=> (isset($answer['sub_option_id']) && in_array($answer['sub_option_id'], $validOptionIds)) ? $answer['sub_option_id'] : null,
                    'value'        => $answer['value'] ?? null,
                    'price'        => $answer['price'] ?? null,
                    'status'       => $answer['status'] ?? 1,
                    'stageing'     => $answer['stageing'] ?? null,
                ]);
            }
        }

        $rateTypeAnswer = $order->details()
            ->whereHas('question', function ($q) {
                $q->where('type', 'rateTypeSelection');
            })
            ->first();


        if ($rateTypeAnswer) {
            $totalPrice = $rateTypeAnswer->option->price;
        }

        // إضافة رسوم الصورة الافتراضية إذا لم يقم العميل برفع صورة
        if (!$request->hasFile('images') || count($request->file('images')) === 0) {
            $totalPrice += env('IMAGE_GENERATION_FEE', 5); // زيادة 5 ريال افتراضياً
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
        $rateTypeAnswer = $order->details()
            ->whereHas('question', function ($q) {
                $q->where('type', 'rateTypeSelection');
            })
            ->first();

        // قراءة القيمة من الخيار أو value مباشر
        $evaluationType = $rateTypeAnswer?->option?->badge // badge = 'ai', 'expert', 'best'
            ?? $rateTypeAnswer?->value;
        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
                'thamn_by' => $evaluationType,
                'total_price' => $totalPrice,
                'files' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'file')
                    ->get()
                    ->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'name' => $file->file_name,
                            'url' => full_url($file->file_path),
                        ];
                    }),

                'images' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'image')
                    ->get()
                    ->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'name' => $file->file_name,
                            'url' => full_url($file->file_path),
                        ];
                    }),
                'answers' => $responseAnswers,
            ],
        ]);
    }


    public function update(Request $request, $orderId)
    {
        $user = $request->user();
        $originalOrder = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $request->validate([
            'isUpdate' => 'required|boolean',
            'status' => 'nullable|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable',
            'answers.*.sub_option_id' => 'nullable',
            'answers.*.value' => 'nullable|string',
            'answers.*.price' => 'nullable|numeric',
            'answers.*.status' => 'nullable|integer',
            'answers.*.stageing' => 'nullable|integer',
            'oldImages' => 'nullable|array',
            'oldFiles' => 'nullable|array',
        ]);

        $isUpdate = filter_var($request->isUpdate, FILTER_VALIDATE_BOOLEAN);

        if ($isUpdate) {
            $order = $originalOrder;
            $order->update([
                'status' => $request->status ?? $order->status,
                'payload' => json_encode($request->answers),
            ]);

            // Handle deleted files (those not in oldImages/oldFiles)
            $oldImageIds = $request->get('oldImages', []);
            $oldFileIds = $request->get('oldFiles', []);

            // Images
            $imagesToDelete = OrderFiles::where('order_id', $order->id)
                ->where('type', 'image')
                ->whereNotIn('id', $oldImageIds)
                ->get();
            foreach ($imagesToDelete as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            // Files
            $filesToDelete = OrderFiles::where('order_id', $order->id)
                ->where('type', 'file')
                ->whereNotIn('id', $oldFileIds)
                ->get();
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            // Clear old details
            OrderDetails::where('order_id', $order->id)->delete();
        } else {
            // Create NEW order
            $order = Order::create([
                'user_id' => $user->id,
                'category_id' => $originalOrder->category_id,
                'status' => $request->status ?? 0,
                'payload' => json_encode($request->answers),
            ]);

            // Copy preserved files
            $oldImageIds = $request->get('oldImages', []);
            $oldFileIds = $request->get('oldFiles', []);

            $preservedImages = OrderFiles::where('order_id', $originalOrder->id)
                ->whereIn('id', $oldImageIds)
                ->get();
            foreach ($preservedImages as $file) {
                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->file_name,
                    'file_path' => $file->file_path,
                    'type' => 'image',
                ]);
            }

            $preservedFiles = OrderFiles::where('order_id', $originalOrder->id)
                ->whereIn('id', $oldFileIds)
                ->get();
            foreach ($preservedFiles as $file) {
                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->file_name,
                    'file_path' => $file->file_path,
                    'type' => 'file',
                ]);
            }
        }

        $totalPrice = 0;
        $details = [];

        // Validate option_ids upfront to prevent foreign key constraint violations
        $allOptionIds = [];
        foreach ($request->answers as $answer) {
            $opt = $answer['option_id'] ?? null;
            if (is_array($opt)) {
                $allOptionIds = array_merge($allOptionIds, $opt);
            } elseif ($opt !== null) {
                $allOptionIds[] = $opt;
            }
            $subOpt = $answer['sub_option_id'] ?? null;
            if ($subOpt !== null) {
                $allOptionIds[] = $subOpt;
            }
        }
        $validOptionIds = \App\Models\QuestionOption::whereIn('id', $allOptionIds)->pluck('id')->toArray();

        foreach ($request->answers as $answer) {
            $optionIds = is_array($answer['option_id'] ?? null)
                ? $answer['option_id']
                : [$answer['option_id'] ?? null];

            // لو مفيش option_id → الإجابة عبارة عن value نصية (سؤال حر)
            if (empty(array_filter($optionIds))) {
                $details[] = OrderDetails::create([
                    'order_id' => $order->id,
                    'question_id' => $answer['question_id'],
                    'option_id' => null,
                    'sub_option_id' => (isset($answer['sub_option_id']) && in_array($answer['sub_option_id'], $validOptionIds)) ? $answer['sub_option_id'] : null,
                    'value' => $answer['value'] ?? null,
                    'price' => $answer['price'] ?? null,
                    'status' => $answer['status'] ?? 1,
                    'stageing' => $answer['stageing'] ?? null,
                ]);
                $totalPrice += floatval($answer['price'] ?? 0);
                continue;
            }

            foreach ($optionIds as $optionId) {
                if ($optionId === null || !in_array($optionId, $validOptionIds)) {
                    continue;
                }

                $details[] = OrderDetails::create([
                    'order_id' => $order->id,
                    'question_id' => $answer['question_id'],
                    'option_id' => $optionId,
                    'sub_option_id' => (isset($answer['sub_option_id']) && in_array($answer['sub_option_id'], $validOptionIds)) ? $answer['sub_option_id'] : null,
                    'value' => $answer['value'] ?? null,
                    'price' => $answer['price'] ?? null,
                    'status' => $answer['status'] ?? 1,
                    'stageing' => $answer['stageing'] ?? null,
                ]);

                $totalPrice += floatval($answer['price'] ?? 0);
            }
        }

        $order->update(['total_price' => $totalPrice]);

        // Upload NEW files
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
                    ->map(fn($file) => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => full_url($file->file_path),
                    ]),
                'images' => OrderFiles::where('order_id', $order->id)
                    ->where('type', 'image')
                    ->get()
                    ->map(fn($file) => [
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
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::with([
            'details.question.step',
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
        $title = $lang === 'ar'
            ? ($order->category?->name_ar ?? $order->category?->name_en ?? 'طلب تقييم')
            : ($order->category?->name_en ?? $order->category?->name_ar ?? 'Valuation Order');

        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */
        $images = $order->files
            ->where('type', 'image')
            ->map(fn($file) => full_url($file->file_path))
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


            $question_steps = QuestionStep::find($detail->question->step);
            $groupId = $detail->stageing ?? 0;
            $groupTitle = $lang === 'ar'
                ? ($question_steps?->name_ar ?? $question_steps?->name_en ?? 'تفاصيل')
                : ($question_steps?->name_en ?? $question_steps?->name_ar ?? 'Details');

            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'id' => intval($groupId),
                    'label' => $groupTitle,
                    'items' => []
                ];
            }

            $groups[$groupId]['items'][] = [
                'id' => $detail->id,
                'label' => $lang === 'ar'
                    ? ($detail->question->question_ar ?? $detail->question->question_en)
                    : ($detail->question->question_en ?? $detail->question->question_ar),
                'value' => $lang === 'ar'
                    ? ($detail->option?->option_ar ?? $detail->option?->option_en ?? $detail->value)
                    : ($detail->option?->option_en ?? $detail->option?->option_ar ?? $detail->value),
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
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->delete(); // soft delete

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم حذف الطلب بنجاح' : 'Order deleted successfully'
        ]);
    }

    public function cancel(Request $request, $orderId)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إلغاء الطلب بنجاح' : 'Order cancelled successfully'
        ]);
    }

    public function getOrders(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $query = Order::where('user_id', Auth::id())->where("status", "!=", "in_market")
            ->with([
                'category',
                'details.option',
                'files'
            ]);

        // Status groups mapping
        $statusGroups = [
            'inPricing' => ['beingEstimated', 'beingReEstimated'],
            'priced' => ['estimated', 'estimatedAndStored', 'reEstimated'],
            'incompleteOrCancelled' => ['inComplete', 'notPaid', 'cancelled'],
        ];
        $validStatuses = collect($statusGroups)->flatten()->unique()->toArray();

        // ========================== Filter ==========================
        if ($request->filled('statsCategory')) {
            if (!array_key_exists($request->statsCategory, $statusGroups)) {
                return response()->json([
                    'status' => false,
                    'message' => $lang === 'ar' ? 'تصنيف الحالة غير صالح' : 'Invalid statsCategory'
                ], 400);
            }
            $query->whereIn('status', $statusGroups[$request->statsCategory]);
        } elseif ($request->filled('status')) {
            if (!in_array($request->status, $validStatuses)) {
                return response()->json([
                    'status' => false,
                    'message' => $lang === 'ar' ? 'الحالة غير صالحة' : 'Invalid status'
                ], 400);
            }
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->latest()->get();

        // ========================== Map response ==========================
        $orders = $orders->map(function ($order) use ($lang) {
            $titleParts = [];

            $categoryName = $lang === 'ar'
                ? ($order->category?->name_ar ?? $order->category?->name_en)
                : ($order->category?->name_en ?? $order->category?->name_ar);
            if ($categoryName)
                $titleParts[] = $categoryName;

            $detailValues = $order->details
                ->map(fn($d) => $lang === 'ar'
                    ? ($d->option?->option_ar ?? $d->option?->option_en ?? $d->value)
                    : ($d->option?->option_en ?? $d->option?->option_ar ?? $d->value))
                ->filter()
                ->unique()
                ->values()
                ->take(2)
                ->toArray();

            $titleParts = array_merge($titleParts, $detailValues);
            if (empty($titleParts))
                $titleParts[] = $lang === 'ar' ? "طلب #{$order->id}" : "Order #{$order->id}";

            $files = $order->files
                ->where('type', 'file')
                ->map(fn($f) => ['url' => full_url($f->file_path)])
                ->values();

            $images = $order->files
                ->where('type', 'image')
                ->map(fn($f) => [
                    'id' => $f->id,
                    'name' => $f->file_name,
                    'url' => full_url($f->file_path)
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
                'is_re_evaluated' => $order->re_evaluation_count > 0,

                'title' => implode(' - ', $titleParts),
                'category' => $order->category,
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
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::with([
            'details.question',
            'details.option',
            'category',
            'files',
        ])
            ->where('id', $orderId)
            ->firstOrFail();

        /* ===================== CATEGORY ===================== */
        $category = $lang === 'ar'
            ? ($order->category?->name_ar ?? $order->category?->name_en ?? '')
            : ($order->category?->name_en ?? $order->category?->name_ar ?? '');

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
                        ?? ($lang === 'ar'
                            ? ($detail->option?->option_ar ?? $detail->option?->option_en)
                            : ($detail->option?->option_en ?? $detail->option?->option_ar));
                    break;

                case 'condition':
                    $condition = $lang === 'ar'
                        ? ($detail->option?->option_ar ?? $detail->option?->option_en ?? $detail->value)
                        : ($detail->option?->option_en ?? $detail->option?->option_ar ?? $detail->value);
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
        $image = $imageFile ? full_url($imageFile->file_path) : '';


        /* ===================== IMAGES & FILES ===================== */
        $images = $order->files
            ->where('type', 'image')
            ->map(fn($f) => [
                'id' => $f->id,
                'name' => $f->file_name,
                'url' => full_url($f->file_path)
            ])
            ->values();

        $files = $order->files
            ->where('type', 'file')
            ->map(fn($f) => [
                'id' => $f->id,
                'name' => $f->file_name,
                'url' => full_url($f->file_path)
            ])
            ->values();

        /* ===================== PRICES ===================== */
        $prices = [
            'highest' => $order->ai_max_price ? (float) $order->ai_max_price : null,
            'average' => (float) (
                $order->thamn_price
                ?? $order->ai_price
                ?? 0
            ),
            'lowest' => $order->ai_min_price ? (float) $order->ai_min_price : null,
        ];

        /* ===================== DETAILS ===================== */
        $details = [];

        foreach ($order->details as $detail) {
            if (!$detail->question) {
                continue;
            }

            $title = $lang === 'ar'
                ? ($detail->question->question_ar ?? $detail->question->question_en)
                : ($detail->question->question_en ?? $detail->question->question_ar);

            $value = $lang === 'ar'
                ? ($detail->option?->option_ar ?? $detail->option?->option_en ?? $detail->value)
                : ($detail->option?->option_en ?? $detail->option?->option_ar ?? $detail->value);

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
            'id' => $order->id,
            'category' => $category,
            'description' => $description,
            'image' => $image,
            'images' => $images,
            'files' => $files,
            'reasoning' => $reasoning,
            'prices' => $prices,
            'features' => $order->ai_features ?? [],
            'details' => $details,
            'qrCode' => \Illuminate\Support\Facades\URL::signedRoute('valuation-order.pdf', ['order' => $order->id]),
            'created_at' => $order->created_at,
            'is_re_evaluated' => $order->re_evaluation_count > 0,
            're_evaluation_terms' => 'يتاح لك الاحتجاج وإعادة التثمين مرة واحدة فقط. الغاية من التثمين ليس الحكم النهائي للسلعة في البيع والشراء بل هو تصور تقديري فقط بناءً على المدخلات، ولا يبنى عليه أحكام بيع السلعة.',
        ]);
    }

    public function generatePdf(Request $request, $orderId)
    {
        $order = \App\Models\Order::with([
            'details.question',
            'details.option',
            'category',
            'files',
            'user'
        ])->findOrFail($orderId);

        /* ===================== CATEGORY ===================== */
        $category = $order->category?->name_ar
            ?? $order->category?->name_en
            ?? '';

        /* ===================== PRICES ===================== */
        $evalType = $order->evaluation_type;
        $prices = [
            'highest' => null,
            'average' => 0.0,
            'lowest' => null,
        ];

        if ($evalType === 'expert') {
            $prices['highest'] = $order->expert_max_price ? (float) $order->expert_max_price : null;
            $prices['lowest'] = $order->expert_min_price ? (float) $order->expert_min_price : null;
            $prices['average'] = (float) ($order->expert_price ?? 0);
        } elseif ($evalType === 'best') {
            $prices['highest'] = $order->thamn_max_price ? (float) $order->thamn_max_price : null;
            $prices['lowest'] = $order->thamn_min_price ? (float) $order->thamn_min_price : null;
            $prices['average'] = (float) ($order->thamn_price ?? 0);
        } else { // ai
            $prices['highest'] = $order->ai_max_price ? (float) $order->ai_max_price : null;
            $prices['lowest'] = $order->ai_min_price ? (float) $order->ai_min_price : null;
            $prices['average'] = (float) ($order->ai_price ?? 0);
        }

        /* ===================== DETAILS ===================== */
        $details = [];
        foreach ($order->details as $detail) {
            if (!$detail->question) continue;

            $title = $detail->question->question_ar
                ?? $detail->question->question_en;

            $value = $detail->option?->option_ar
                ?? $detail->option?->option_en
                ?? $detail->value;

            if ($title && $value) {
                $details[] = [
                    'title' => $title,
                    'value' => (string) $value,
                ];
            }
        }
        
        $reasoning = $order->thamn_reasoning
            ?? $order->expert_reasoning
            ?? $order->ai_reasoning
            ?? '';

        /* ===================== MAIN IMAGE ===================== */
        $imageFile = $order->files->firstWhere('type', 'image');
        $image = $imageFile ? public_path('storage/' . $imageFile->file_path) : null;


        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('pdf.order-valuation-result', compact(
            'order', 'category', 'prices', 'details', 'reasoning', 'image'
        ));

        return $pdf->stream('valuation-order-' . $order->id . '.pdf');
    }



    public function reEvaluate(Request $request, $orderId)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::with(['details.question', 'details.option', 'files', 'category', 'user', 'expert'])
            ->findOrFail($orderId);

        // ─── التحقق: إعادة التثمين مرة واحدة فقط ───
        if ($order->re_evaluation_count >= 1) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'لقد استنفذت فرصة إعادة التثمين المتاحة'
                    : 'You have already used your re-evaluation opportunity.',
            ], 400);
        }

        // ─── التحقق: خلال 24 ساعة من التقييم الأصلي ───
        if ($order->evaluated_at && $order->evaluated_at->diffInHours(now()) > 24) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'انتهت مهلة إعادة التثمين (24 ساعة)'
                    : 'Re-evaluation window has expired (24 hours).',
            ], 400);
        }

        // ─── التحقق: لازم يكون متقيم أصلاً ───
        if (!$order->evaluated_at) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'لا يمكن إعادة التثمين لطلب لم يتم تقييمه بعد'
                    : 'Cannot re-evaluate an order that has not been evaluated yet.',
            ], 400);
        }

        $originalExpertId = $order->expert_id;
        $originalExpert = $order->expert;

        // Reset previous evaluation details but keep expert_id for re-evaluation
        $order->update([
            'status' => 'beingReEstimated',
            'ai_min_price' => null,
            'ai_max_price' => null,
            'ai_price' => null,
            'ai_confidence' => null,
            'ai_reasoning' => null,
            'expert_price' => null,
            'expert_reasoning' => null,
            'expert_evaluated' => 0,
            'thamn_price' => null,
            'thamn_by' => null,
            'thamn_at' => null,
        ]);

        // زيادة عداد إعادة التثمين
        $order->increment('re_evaluation_count');

        // Refresh the model so the relations reflect the latest DB state
        $order->refresh();

        $rateTypeAnswer = $order->details()
            ->whereHas('question', fn($q) => $q->where('type', 'rateTypeSelection'))
            ->first();

        $evaluationType = $rateTypeAnswer?->option?->badge ?? $rateTypeAnswer?->value;

        switch ($evaluationType) {
            case 'ai':
                // Use ThamnEvaluationService which correctly attaches images to the AI prompt
                app(\App\Services\ThamnEvaluationService::class)->runAiEvaluation($order);
                break;

            case 'expert':
                if ($originalExpertId) {
                    $order->update([
                        'expert_id' => $originalExpertId,
                        'status' => 'beingReEstimated',
                        'expert_evaluated' => 0,
                    ]);
                } else {
                    $this->sendToExperts($order);
                }
                break;

            case 'best':
                app(\App\Services\ThamnEvaluationService::class)->runAiEvaluation($order);
                if ($originalExpertId) {
                    $order->update([
                        'expert_id' => $originalExpertId,
                        'status' => 'beingReEstimated',
                        'expert_evaluated' => 0,
                    ]);
                } else {
                    app(\App\Services\ThamnEvaluationService::class)->sendBestOrderToExperts($order);
                }
                break;

            default:
                Log::warning('Unknown evaluation type on re-evaluate', [
                    'order_id' => $order->id,
                    'evaluation_type' => $evaluationType,
                ]);
        }

        // Notify Expert about re-evaluation request via Email
        if ($originalExpert && $originalExpert->email) {
            try {
                $customerName = $order->user->first_name . ' ' . $order->user->last_name;
                \Illuminate\Support\Facades\Mail::to($originalExpert->email)->send(new \App\Mail\SystemNotificationMail(
                    'يا هلا بك.. طلب إعادة تقييم لمنتج! 🔄',
                    "يا هلا بك يا خبيرنا الغالي! العميل {$customerName} طلب إعادة تقييم لمنتجه رقم #{$order->id}.\nيرجى الدخول وتدقيق السعر والتقييم مرة أخرى من خلال الرابط التالي.",
                    route('orders.show', $order->id)
                ));
            } catch (\Exception $e) {
                Log::error('Re-evaluation Expert Notification Mail Failed: ' . $e->getMessage());
            }
        }

        // FCM Notification لإعادة التقييم (Saudi Phrasing)
        $tokens = $order->user->getFcmTokens();
        if (!empty($tokens)) {
            $this->notifyByFirebase(
                'تم استلام طلبك يا غالي 🔄',
                "يا هلا بك! استلمنا طلب إعادة التقييم لمنتجك رقم #{$order->id}. بنباشر التقييم الجديد في أقرب وقت ونبشرك بالنتيجة.",
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 're_evaluation_requested']]
            );
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم طلب إعادة التقييم بنجاح' : 'Order re-evaluation requested successfully',
            'order_id' => $order->id,
            'new_status' => $order->fresh()->status,
        ]);
    }

    /**
     * @deprecated Use ThamnEvaluationService::runAiEvaluation() instead — it correctly attaches images.
     */
    private function runAiEvaluation(Order $order): void
    {
        // Delegate to the service that properly handles image attachments
        app(\App\Services\ThamnEvaluationService::class)->runAiEvaluation($order);
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

        $expert = \App\Models\User::role('expert')->first();
        if ($expert) {
            $order->update([
                'expert_id' => $expert->id,
                'status' => 'beingEstimated',

            ]);

            // إرسال Notification للخبير
            $expert->notify(new \App\Notifications\OrderAssignedToExpert($order));
        }

        // إرسال Notification للمستخدم
        $order->user->notify(new \App\Notifications\OrderSentForExpertEvaluation($order));

        // FCM Notification للمستخدم
        $tokens = $order->user->getFcmTokens();
        if (!empty($tokens)) {
            $this->notifyByFirebase(
                'يجارٍ تقييم منتجك 🔍',
                "لقد استلم خبيرنا طلبك رقم #{$order->id}، وسيتم إخطارك بالنتيجة فور الانتهاء.",
                $tokens,
                ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'sent_to_expert']]
            );
        }

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
            'status' => 'beingEstimated',

        ]);

        // إرسال Notification للمستخدم
        $order->user->notify(new \App\Notifications\OrderThamnPriceCalculated($order));

        Log::info("Thamn price calculated", [
            'order_id' => $order->id,
            'thamn_price' => $thamnPrice
        ]);
    }

    public function resendOrder(Request $request, $orderId)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::findOrFail($orderId);

        // نرسل Notification للمستخدم بأن الطلب تم إعادة إرساله
        $order->user->notify(new \App\Notifications\OrderResent($order));

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إعادة إرسال الطلب بنجاح' : 'Order request resent successfully',
            'order_id' => $order->id,
        ]);
    }
    public function sendToMarket(Request $request, $orderId)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::findOrFail($orderId);

        $request->validate([
            'send' => 'required|boolean', // true => نرسل للسوق، false => لا
        ]);

        // ─── يجب أن يكون المنتج متقيم قبل إرساله للسوق ───
        $isEvaluated = $order->thamn_price || $order->ai_price || $order->expert_price;

        if (!$isEvaluated) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'يجب تقييم المنتج قبل إرساله للسوق'
                    : 'Product must be evaluated before sending to market.',
            ], 400);
        }

        if (!$order->total_price) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'يجب حساب سعر المنتج قبل إرساله للسوق'
                    : 'Product price must be calculated before sending to market.',
            ], 400);
        }

        if ($request->send) {
            // تحديث الحالة للسوق
            $order->update([
                'status' => 'sent_to_market'
            ]);

            // Notification للمستخدم أن المنتج أرسل للسوق
            $order->user->notify(new \App\Notifications\OrderSentToMarket($order));

            // FCM Notification
            $tokens = $order->user->getFcmTokens();
            if (!empty($tokens)) {
                $this->notifyByFirebase(
                    '🛒 منتجك في السوق الآن!',
                    "تم إدراج منتجك رقم #{$order->id} في سوق ثمن بنجاح. يمكن للمشترين رؤيته الآن.",
                    $tokens,
                    ['data' => ['user_id' => $order->user_id, 'order_id' => $order->id, 'type' => 'sent_to_market']]
                );
            }

            return response()->json([
                'status' => true,
                'message' => $lang === 'ar'
                    ? 'تم إرسال الطلب للسوق بنجاح'
                    : 'Order sent to market successfully',
                'order_id' => $order->id,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar'
                ? 'لم يتم إرسال الطلب للسوق'
                : 'Order not sent to market',
            'order_id' => $order->id,
        ]);
    }


}
