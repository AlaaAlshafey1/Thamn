<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;

class PageController extends Controller
{
    // عرض الصفحة حسب النوع
    public function index($type = 'about')
    {
        $pages = About::get();
        return view('pages.index', compact('pages', 'type'));
    }

    // نموذج إنشاء محتوى جديد
    public function create($type = 'about')
    {
        $page = About::ofType($type)->first();
        if ($page) {
            return redirect()->route('pages.index', $type)
                ->with('info', 'المحتوى موجود بالفعل، يمكنك تعديله.');
        }
        return view('pages.form', compact('type'));
    }

    // تخزين المحتوى
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
        ]);

        About::create($request->only(['type', 'content_ar', 'content_en']));

        return redirect()->route('pages.index', $request->type)
            ->with('success', 'تم إضافة المحتوى بنجاح');
    }

    // نموذج تعديل المحتوى
    public function edit($id)
    {
        $page = About::findOrFail($id);
        return view('pages.form', compact('page'));
    }

    // تحديث المحتوى
    public function update(Request $request, $id)
    {
        $request->validate([
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
        ]);

        $page = About::findOrFail($id);
        $page->update($request->only(['content_ar', 'content_en']));

        return redirect()->route('pages.index', $page->type)
            ->with('success', 'تم تحديث المحتوى بنجاح');
    }

    // حذف المحتوى
    public function destroy($id)
    {
        $page = About::findOrFail($id);
        $type = $page->type;
        $page->delete();

        return redirect()->route('pages.index', $type)
            ->with('success', 'تم حذف المحتوى بنجاح');
    }
}
