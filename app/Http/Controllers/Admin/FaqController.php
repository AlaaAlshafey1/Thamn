<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    // عرض جميع الأسئلة
    public function index()
    {
        $faqs = Faq::latest()->get();
        return view('faqs.index', compact('faqs'));
    }

    // نموذج إضافة سؤال جديد
    public function create()
    {
        return view('faqs.form');
    }

    // تخزين السؤال
    public function store(Request $request)
    {
        $request->validate([
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'category' => 'required|string',
        ]);

        Faq::create($request->all());

        return redirect()->route('faqs.index')->with('success', 'تم إضافة السؤال بنجاح');
    }

    // نموذج تعديل السؤال
    public function edit(Faq $faq)
    {
        return view('faqs.form', compact('faq'));
    }

    // تحديث السؤال
    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'category' => 'required|string',
        ]);

        $faq->update($request->all());

        return redirect()->route('faqs.index')->with('success', 'تم تحديث السؤال بنجاح');
    }

    // حذف السؤال
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('faqs.index')->with('success', 'تم حذف السؤال بنجاح');
    }
}
