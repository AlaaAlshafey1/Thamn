<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionOption;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('category')->latest()->paginate(10);
        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        $categories = Category::where('is_active', 1)->get();
        return view('questions.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'nullable|string|max:255',
            'type' => 'required|in:text,number,select,radio,checkbox,image',
            'is_required' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'options_ar' => 'nullable|array',
            'options_en' => 'nullable|array',
            'options_image.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'category_id',
            'question_ar',
            'question_en',
            'description_ar',  // جديد
            'description_en',  // جديد
            'type',
            'is_required',
            'is_active',
            'order'
        ]);
        $question = Question::create($data);

        if(in_array($question->type, ['select','radio','checkbox']) && $request->filled('options_ar')) {
            foreach($request->options_ar as $index => $option_ar) {
                $option_en = $request->options_en[$index] ?? null;
                $imagePath = null;

                if(isset($request->options_image[$index])) {
                    $imagePath = $request->options_image[$index]->store('options', 'public');
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar' => $option_ar,
                    'option_en' => $option_en,
                    'image' => $imagePath,
                    'order' => $index,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('questions.index')->with('success', 'تمت إضافة السؤال والخيارات بنجاح');
    }

    public function show(Question $question)
    {

        $question->load('category', 'options'); // جلب الفئة والخيارات
        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $categories = Category::where('is_active', 1)->get();
        return view('questions.edit', compact('question','categories'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'nullable|string|max:255',
            'type' => 'required|in:text,number,select,radio,checkbox,image',
            'is_required' => 'sometimes|boolean',
            'order' => 'nullable|integer',
            'options_ar' => 'nullable|array',
            'options_en' => 'nullable|array',
            'options_image.*' => 'nullable|image|max:2048',
            'options_id' => 'nullable|array', // hidden inputs for existing options
        ]);

        $data = $request->only([
            'category_id',
            'question_ar',
            'question_en',
            'description_ar',  // جديد
            'description_en',  // جديد
            'type',
            'is_required',
            'is_active',
            'order'
        ]);
        $question->update($data);

        if(in_array($question->type, ['select','radio','checkbox'])) {

            $existingOptions = $request->options_id ?? [];

            // حذف الخيارات اللي مش موجودة في الفورم (المستخدمة مسحتهم)
            \App\Models\QuestionOption::where('question_id', $question->id)
                ->whereNotIn('id', $existingOptions)
                ->delete();

            foreach($request->options_ar as $index => $option_ar) {
                $option_en = $request->options_en[$index] ?? null;
                $imagePath = null;

                // الصورة الجديدة لو تم رفعها
                if(isset($request->options_image[$index])) {
                    $imagePath = $request->options_image[$index]->store('options', 'public');
                }

                if(isset($existingOptions[$index])) {
                    // تعديل خيار موجود
                    $option = \App\Models\QuestionOption::find($existingOptions[$index]);
                    if($option) {
                        $option->update([
                            'option_ar' => $option_ar,
                            'option_en' => $option_en,
                            'image' => $imagePath ?? $option->image,
                        ]);
                    }
                } else {
                    // إضافة خيار جديد
                    \App\Models\QuestionOption::create([
                        'question_id' => $question->id,
                        'option_ar' => $option_ar,
                        'option_en' => $option_en,
                        'image' => $imagePath,
                        'order' => $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('questions.index')->with('success', 'تم تحديث السؤال والخيارات بنجاح');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'تم حذف السؤال بنجاح');
    }
}
