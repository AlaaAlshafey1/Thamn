<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class FavoriteController extends Controller
{
    /**
     * Get Favorites
     * GET /favorites
     */
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $favorites = Favorite::with(['order.category', 'order.user', 'order.files'])
            ->where('user_id', $request->user()->id)
            ->get();

        $data = $favorites->map(function ($favorite) use ($lang) {
            $order = $favorite->order;
            if (!$order)
                return null;

            return [
                'id' => $order->id,
                'title' => $lang === 'ar' ? ($order->category->name_ar ?? '') : ($order->category->name_en ?? ''),
                'category' => [
                    'id' => $order->category_id,
                    'name' => $lang === 'ar' ? ($order->category->name_ar ?? '') : ($order->category->name_en ?? ''),
                ],
                'price' => $order->total_price,
                'image' => $order->files->where('type', 'image')->first()
                    ? full_url($order->files->where('type', 'image')->first()->file_path)
                    : null,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->first_name . ' ' . $order->user->last_name,
                    'image' => $order->user->image,
                ]
            ];
        })->filter()->values();

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم استرجاع المفضلات بنجاح' : 'Favorites fetched successfully',
            'data' => $data,
        ]);
    }

    /**
     * Add to Favorites
     * POST /favorites
     */
    public function store(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $request->validate([
            'productId' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->productId);

        if ($order->status !== 'sent_to_market') {
            return response()->json([
                'status' => false,
                'message' => $lang === 'ar' ? 'هذا المنتج غير موجود في السوق' : 'This product is not in the marketplace',
            ], 400);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'order_id' => $request->productId,
        ]);

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تمت الإضافة للمفضلة بنجاح' : 'Added to favorites successfully',
        ]);
    }

    /**
     * Remove from Favorites
     * DELETE /favorites/{productId}
     */
    public function destroy(Request $request, $productId)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('order_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
        }

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم الحذف من المفضلة بنجاح' : 'Removed from favorites successfully',
        ]);
    }
}
