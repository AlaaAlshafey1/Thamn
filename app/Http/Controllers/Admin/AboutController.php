<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;

class AboutController extends Controller
{
    // عرض المحتوى الحالي
    public function index()
    {
        $about = About::first(); // عادة يوجد صف واحد فقط
        return view('about.index', compact('about'));
    }

    // نموذج إنشاء المحتوى (لو ما فيش)
    public function create()
    {
        $about = About::first();
        if($about) {
            return redirect()->route('about.index')->with('info', 'المحتوى موجود بالفعل، يمكنك تعديله.');
        }
        return view('about.form');
    }

    // تخزين المحتوى الجديد
    public function store(Request $request)
    {
        $request->validate([
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
        ]);

        About::create($request->only(['content_ar','content_en']));

        return redirect()->route('about.index')->with('success', 'تم إضافة المحتوى بنجاح');
    }

    // نموذج تعديل المحتوى
    public function edit(About $about)
    {
        return view('about.form', compact('about'));
    }

    // تحديث المحتوى
    public function update(Request $request, About $about)
    {
        $request->validate([
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
        ]);

        $about->update($request->only(['content_ar','content_en']));

        return redirect()->route('about.index')->with('success', 'تم تحديث المحتوى بنجاح');
    }

    // حذف المحتوى (اختياري)
    public function destroy(About $about)
    {
        $about->delete();
        return redirect()->route('about.index')->with('success', 'تم حذف المحتوى بنجاح');
    }
}

