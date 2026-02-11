<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderFiles;
use Illuminate\Support\Facades\Storage;

class MarketPlaceOrderController extends Controller
{
    /**
     * إضافة منتج جديد للماركت
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // ================= VALIDATION =================
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable',
            'answers.*.sub_option_id' => 'nullable',
            'answers.*.value' => 'nullable|string',
            'answers.*.price' => 'nullable|numeric',
            'answers.*.group_type' => 'nullable|string',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'files.*' => 'nullable|file',
            'payment_type' => 'nullable|string',
        ]);

        // ================= CREATE ORDER =================
        $order = Order::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'status' => 'sent_to_market',
            'payload' => json_encode($request->answers),
            'total_price' => $request->answerstotal_price ?? 0,
            'payment_type' => $request->payment_type,
        ]);

        $totalPrice = 0;
        $details = [];

        // ================= STORE ORDER DETAILS =================
        foreach ($request->answers as $answer) {
            // إذا فيه option_id
            if (!empty($answer['option_id'])) {
                $optionIds = is_array($answer['option_id']) ? $answer['option_id'] : [$answer['option_id']];
                foreach ($optionIds as $optionId) {
                    $detail = OrderDetails::create([
                        'order_id' => $order->id,
                        'question_id' => $answer['question_id'],
                        'option_id' => $optionId,
                        'sub_option_id' => $answer['sub_option_id'] ?? null,
                        'value' => $answer['value'] ?? null,
                        'price' => $answer['price'] ?? 0,
                        'status' => 1,
                        'stageing' => $answer['group_type'] ?? null,
                    ]);
                    $totalPrice += $answer['price'] ?? 0;
                    $details[] = $detail;
                }
            } else {
                // لو مفيش option_id نخزن النص مباشرة
                $detail = OrderDetails::create([
                    'order_id' => $order->id,
                    'question_id' => $answer['question_id'],
                    'option_id' => null,
                    'sub_option_id' => $answer['sub_option_id'] ?? null,
                    'value' => $answer['value'] ?? null,
                    'price' => $answer['price'] ?? 0,
                    'status' => 1,
                    'stageing' => $answer['group_type'] ?? null,
                ]);
                $totalPrice += $answer['price'] ?? 0;
                $details[] = $detail;
            }
        }

        $order->update(['total_price' => $totalPrice]);

        // ================= STORE FILES =================
        $filesData = ['images' => [], 'files' => []];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store("market_orders/{$order->id}/images", 'public');
                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'image',
                ]);
                $filesData['images'][] = \Illuminate\Support\Facades\Storage::url($path);
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store("market_orders/{$order->id}/files", 'public');
                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'file',
                ]);
                $filesData['files'][] = \Illuminate\Support\Facades\Storage::url($path);
            }
        }

        // ================= RESPONSE =================
        $responseAnswers = collect($details)->map(function ($d) {
            return [
                'question_id' => $d->question_id,
                'option_id' => $d->option_id,
                'sub_option_id' => $d->sub_option_id,
                'value' => $d->value,
                'price' => $d->price,
                'group_type' => $d->stageing,
            ];
        });

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'category_id' => $order->category_id,
                'status' => $order->status,
                'total_price' => $totalPrice,
                'payment_type' => $order->payment_type,
                'images' => $filesData['images'],
                'files' => $filesData['files'],
                'answers' => $responseAnswers,
            ],
        ]);
    }


    /**
     * تحديث منتج موجود
     */
    public function update(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable',
            'answers.*.sub_option_id' => 'nullable',
            'answers.*.value' => 'nullable|string',
            'answers.*.price' => 'nullable|numeric',
            'answers.*.group_type' => 'nullable|string',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'files.*' => 'nullable|file',
        ]);

        $order->update([
            'payload' => json_encode($request->answers),
        ]);

        OrderDetails::where('order_id', $order->id)->delete();

        $totalPrice = 0;
        $details = [];

        foreach ($request->answers as $answer) {
            if (!empty($answer['option_id'])) {
                $optionIds = is_array($answer['option_id']) ? $answer['option_id'] : [$answer['option_id']];
                foreach ($optionIds as $optionId) {
                    $detail = OrderDetails::create([
                        'order_id' => $order->id,
                        'question_id' => $answer['question_id'],
                        'option_id' => $optionId,
                        'sub_option_id' => $answer['sub_option_id'] ?? null,
                        'value' => $answer['value'] ?? null,
                        'price' => $answer['price'] ?? 0,
                        'status' => 1,
                        'stageing' => $answer['group_type'] ?? null,
                    ]);
                    $totalPrice += $answer['price'] ?? 0;
                    $details[] = $detail;
                }
            } else {
                $detail = OrderDetails::create([
                    'order_id' => $order->id,
                    'question_id' => $answer['question_id'],
                    'option_id' => null,
                    'sub_option_id' => $answer['sub_option_id'] ?? null,
                    'value' => $answer['value'] ?? null,
                    'price' => $answer['price'] ?? 0,
                    'status' => 1,
                    'stageing' => $answer['group_type'] ?? null,
                ]);
                $totalPrice += $answer['price'] ?? 0;
                $details[] = $detail;
            }
        }

        $order->update(['total_price' => $totalPrice]);

        // رفع الملفات الجديدة إذا موجودة
        $filesData = ['images' => [], 'files' => []];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store("market_orders/{$order->id}/images", 'public');
                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'image',
                ]);
                $filesData['images'][] = \Illuminate\Support\Facades\Storage::url($path);
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store("market_orders/{$order->id}/files", 'public');
                OrderFiles::create([
                    'order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'type' => 'file',
                ]);
                $filesData['files'][] = \Illuminate\Support\Facades\Storage::url($path);
            }
        }

        $responseAnswers = collect($details)->map(function ($d) {
            return [
                'question_id' => $d->question_id,
                'option_id' => $d->option_id,
                'sub_option_id' => $d->sub_option_id,
                'value' => $d->value,
                'price' => $d->price,
                'group_type' => $d->stageing,
            ];
        });

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'total_price' => $totalPrice,
                'answers' => $responseAnswers,
                'images' => $filesData['images'],
                'files' => $filesData['files'],
            ],
        ]);
    }


    /**
     * عرض تفاصيل المنتج
     */
    public function show(Request $request, $orderId)
    {
        $order = Order::with(['details', 'files', 'category'])
            ->where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $groups = [];

        foreach ($order->details as $detail) {
            $groupId = $detail->question->group_type ?? 'default';
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'group_type' => $groupId,
                    'items' => []
                ];
            }
            $groups[$groupId]['items'][] = [
                'question_id' => $detail->question_id,
                'option_id' => $detail->option_id,
                'sub_option_id' => $detail->sub_option_id,
                'value' => $detail->value,
                'price' => $detail->price,
            ];
        }

        return response()->json([
            'status' => true,
            'order' => [
                'id' => $order->id,
                'category_id' => $order->category_id,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'payment_type' => $order->payment_type,
                'groups' => array_values($groups),
                'images' => $order->files->where('type', 'image')->map(fn($f) => \Illuminate\Support\Facades\Storage::url($f->file_path)),
                'files' => $order->files->where('type', 'file')->map(fn($f) => \Illuminate\Support\Facades\Storage::url($f->file_path)),
            ]
        ]);
    }


    /**
     * حذف المنتج
     */
    public function destroy(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * إلغاء المنتج
     */
    public function cancel(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'status' => true,
            'message' => 'Product cancelled successfully'
        ]);
    }
}
