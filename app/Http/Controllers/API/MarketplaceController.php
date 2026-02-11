<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    /**
     * Get Products for Marketplace
     * GET /marketplace/products
     */
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $query = Order::with(['category', 'user', 'files'])
            ->whereIn('status', ['sent_to_market', 'in_market', 'cancelled_from_market']);

        // Filter by Category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            if ($request->status == 'cancelled_from_market') {
                $query->where('status', $request->status);
            } else {

                $query->whereIn('status', ["sent_to_market", "in_market"]);

            }
        }

        // Advanced Filters
        if ($request->has('filters')) {
            $filters = $request->get('filters');
            if (is_string($filters)) {
                $filters = json_decode($filters, true);
            }

            if (is_array($filters)) {
                foreach ($filters as $questionId => $value) {
                    if ($questionId == '4') { // price_range
                        if (is_array($value)) {
                            $min = $value['min'] ?? null;
                            $max = $value['max'] ?? null;
                            if ($min !== null && $max !== null) {
                                $query->whereBetween('total_price', [$min, $max]);
                            }
                        }
                        continue;
                    }

                    // Question based filters
                    $query->whereHas('details', function ($q) use ($questionId, $value) {
                        $q->where('question_id', $questionId);
                        if (is_array($value)) {
                            $q->whereIn('option_id', $value);
                        } else {
                            $q->where('option_id', $value);
                        }
                    });
                }
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('details', function ($dq) use ($search) {
                    $dq->where('value', 'like', '%' . $search . '%');
                })->orWhere('payload', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_high':
                $query->orderBy('total_price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('total_price', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate($request->get('per_page', 15));

        $data = $products->getCollection()->map(function ($order) use ($lang) {
            return [
                'id' => $order->id,
                'title' => $lang === 'ar' ? ($order->category->name_ar ?? '') : ($order->category->name_en ?? ''),
                'payment_type' => $order->payment_type,
                'status' => $order->status,
                'category' => [
                    'id' => $order->category_id,
                    'name' => $lang === 'ar' ? ($order->category->name_ar ?? '') : ($order->category->name_en ?? ''),
                ],
                'total_price' => $order->total_price,
                'image' => $order->files->where('type', 'image')->first() ? Storage::url($order->files->where('type', 'image')->first()->file_path) : null,
                'images' => $order->files->where('type', 'image')->map(fn($f) => Storage::url($f->file_path)),
                'files' => $order->files->where('type', 'file')->map(fn($f) => Storage::url($f->file_path)),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'payload' => $order->payload,
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->first_name . ' ' . $order->user->last_name,
                    'image' => $order->user->image,
                ]
            ];
        });

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم استرجاع منتجات السوق بنجاح' : 'Marketplace products fetched successfully',
            'data' => $data,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get Product Details
     * GET /marketplace/products/{productId}
     */
    public function show(Request $request, $id)
    {



        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $order = Order::with(['category', 'user', 'files', 'details.question', 'details.option'])
            ->whereIn('status', ['sent_to_market', 'in_market', 'cancelled_from_market'])
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'المنتج غير موجود' : 'Product not found',
            ], 404);
        }

        $groups = [];
        foreach ($order->details as $detail) {
            $groupId = $detail->stageing ?? 'default';
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'group' => $groupId,
                    'items' => []
                ];
            }
            $groups[$groupId]['items'][] = [
                'id' => $detail->id,
                'label' => $lang === 'ar' ? ($detail->question->question_ar ?? '') : ($detail->question->question_en ?? ''),
                'value' => $lang === 'ar' ? ($detail->option->option_ar ?? $detail->value) : ($detail->option->option_en ?? $detail->value),
                // 'price' => $detail->price,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $order->id,
                'category' => [
                    'id' => $order->category_id,
                    'name' => $lang === 'ar' ? ($order->category->name_ar ?? '') : ($order->category->name_en ?? ''),
                ],
                'price' => $order->total_price,
                'status' => $order->status,
                'description' => $order->payload,
                'images' => $order->files->where('type', 'image')->map(fn($f) => Storage::url($f->file_path)),
                'files' => $order->files->where('type', 'file')->map(fn($f) => Storage::url($f->file_path)),
                "payment_type" => $order->payment_type,
                'details' => array_values($groups),
                'seller' => [
                    'id' => $order->user->id,
                    'name' => $order->user->first_name . ' ' . $order->user->last_name,
                    'image' => $order->user->image,
                    'phone' => $order->user->phone,
                ],
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}
