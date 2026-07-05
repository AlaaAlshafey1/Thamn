<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     */
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner (redirects to index with modal).
     */
    public function create()
    {
        return redirect()->route('banners.index');
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:51200', // 50MB max
            'sort_order' => 'nullable|integer',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_banner_' . $banner->id . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('banners', $fileName, 'public');
            $validated['file'] = asset('storage/' . $filePath);

            // Determine file type
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'video/')) {
                $validated['file_type'] = 'video';
            } elseif ($file->getClientOriginalExtension() === 'gif') {
                $validated['file_type'] = 'gif';
            } else {
                $validated['file_type'] = 'image';
            }
        }

        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);

        return redirect()->route('banners.index')
            ->with('success', 'تم تحديث البانر بنجاح.');
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:51200', // 50MB max
            'sort_order' => 'nullable|integer',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_banner.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('banners', $fileName, 'public');
            $validated['file'] = asset('storage/' . $filePath);

            // Determine file type
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'video/')) {
                $validated['file_type'] = 'video';
            } elseif ($file->getClientOriginalExtension() === 'gif') {
                $validated['file_type'] = 'gif';
            } else {
                $validated['file_type'] = 'image';
            }
        }

        $validated['is_active'] = $request->has('is_active');

        Banner::create($validated);

        return redirect()->route('banners.index')
            ->with('success', 'تم إضافة البانر بنجاح.');
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();

        return redirect()->route('banners.index')
            ->with('success', 'تم حذف البانر بنجاح.');
    }
}
