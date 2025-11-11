<?php

namespace App\Http\Controllers;

use App\Models\AppPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppPageController extends Controller
{
    public function index()
    {
        $pages = AppPage::latest()->paginate(10);
        return view('app_pages.index', compact('pages'));
    }

    public function create()
    {
        return view('app_pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:app_pages,name',
            'type' => 'required|string',
            // ⭐️ نتحقق من الوصف
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        // نأخذ كل البيانات عدا الصور
        $data = $request->except(['background_image', 'logo', 'banner_image']);

        // ⭐️ نضيف الوصف
        $data['description_ar'] = $request->input('description_ar');
        $data['description_en'] = $request->input('description_en');

        // تأكيد البوليانز
        $data['has_banner'] = $request->has('has_banner') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // رفع الملفات
        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('app_pages/backgrounds', 'public');
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('app_pages/logos', 'public');
        }

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('app_pages/banners', 'public');
        }

        AppPage::create($data);

        return redirect()->route('app_pages.index')->with('success', 'تمت إضافة الصفحة بنجاح');
    }

    public function show(AppPage $appPage)
    {
        return view('app_pages.show', compact('appPage'));
    }

    public function edit(AppPage $appPage)
    {
        return view('app_pages.edit', compact('appPage'));
    }

    public function update(Request $request, AppPage $appPage)
    {
        $request->validate([
            'name' => 'required|string|unique:app_pages,name,' . $appPage->id,
            'type' => 'required|string',
        ]);

        $data = $request->except(['background_image', 'logo', 'banner_image']);

        $data['description_ar'] = $request->input('description_ar');
        $data['description_en'] = $request->input('description_en');

        $data['has_banner'] = $request->has('has_banner') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // تحديث الصور مع حذف القديمة
        if ($request->hasFile('background_image')) {
            if ($appPage->background_image && Storage::disk('public')->exists($appPage->background_image)) {
                Storage::disk('public')->delete($appPage->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('app_pages/backgrounds', 'public');
        }

        if ($request->hasFile('logo')) {
            if ($appPage->logo && Storage::disk('public')->exists($appPage->logo)) {
                Storage::disk('public')->delete($appPage->logo);
            }
            $data['logo'] = $request->file('logo')->store('app_pages/logos', 'public');
        }

        if ($request->hasFile('banner_image')) {
            if ($appPage->banner_image && Storage::disk('public')->exists($appPage->banner_image)) {
                Storage::disk('public')->delete($appPage->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('app_pages/banners', 'public');
        }

        $appPage->update($data);

        return redirect()->route('app_pages.index')->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(AppPage $appPage)
    {
        foreach (['background_image', 'logo', 'banner_image'] as $field) {
            if ($appPage->$field && Storage::disk('public')->exists($appPage->$field)) {
                Storage::disk('public')->delete($appPage->$field);
            }
        }

        $appPage->delete();

        return redirect()->route('app_pages.index')->with('success', 'تم حذف الصفحة بنجاح');
    }

    // API: يعيد الصفحات مهيأة لرابط الصور
    public function api()
    {
        $pages = AppPage::where('is_active', true)->get()->map(function($p){
            $p->background_image = $p->background_image ? asset('storage/'.$p->background_image) : null;
            $p->logo = $p->logo ? asset('storage/'.$p->logo) : null;
            $p->banner_image = $p->banner_image ? asset('storage/'.$p->banner_image) : null;
            return $p;
        });

        return response()->json(['pages' => $pages]);
    }
}
