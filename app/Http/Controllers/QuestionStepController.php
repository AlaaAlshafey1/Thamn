<?php

namespace App\Http\Controllers;

use App\Models\QuestionStep;
use Illuminate\Http\Request;

class QuestionStepController extends Controller
{
    public function index()
    {
        $steps = QuestionStep::orderBy('sort_order')->get();
        return view('question_steps.index', compact('steps'));
    }

    public function create()
    {
        return view('question_steps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'required|boolean',
        ]);

        QuestionStep::create($request->all());

        return redirect()
            ->route('question_steps.index')
            ->with('success', 'تم إضافة المرحلة بنجاح');
    }

    public function edit(QuestionStep $question_step)
    {
        return view('question_steps.edit', compact('question_step'));
    }

    public function update(Request $request, QuestionStep $question_step)
    {
        $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'required|boolean',
        ]);

        $question_step->update($request->all());

        return redirect()
            ->route('question_steps.index')
            ->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function destroy(QuestionStep $question_step)
    {
        $question_step->delete();

        return redirect()
            ->route('question_steps.index')
            ->with('success', 'تم حذف المرحلة');
    }
}
