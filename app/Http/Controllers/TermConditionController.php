<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class TermConditionController extends Controller
{
    public function index()
    {
        $terms = TermCondition::orderBy('sort_order')->get();
        return view('admin.terms.index', compact('terms'));
    }

    public function create()
    {
        return view('admin.terms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'required|boolean',
        ]);

        TermCondition::create($request->all());

        return redirect()
            ->route('terms.index')
            ->with('success', 'تمت إضافة بند الشروط بنجاح');
    }

    public function edit(TermCondition $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    public function update(Request $request, TermCondition $term)
    {
        $request->validate([
            'title_ar'   => 'required|string|max:255',
            'title_en'   => 'nullable|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'required|boolean',
        ]);

        $term->update($request->all());

        return redirect()
            ->route('terms.index')
            ->with('success', 'تم تحديث بند الشروط بنجاح');
    }

    public function destroy(TermCondition $term)
    {
        $term->delete();

        return redirect()
            ->route('terms.index')
            ->with('success', 'تم حذف بند الشروط');
    }
}
