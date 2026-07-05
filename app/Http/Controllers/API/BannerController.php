<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Get all active banners
     * GET /api/banners
     */
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        $banners = Banner::where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($banner) use ($lang) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->getTitle($lang),
                    'file' => $banner->file,
                    'file_type' => $banner->file_type,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => lang('تم إرجاع البانرات بنجاح', 'Banners fetched successfully', $request),
            'data' => $banners,
        ]);
    }
}
