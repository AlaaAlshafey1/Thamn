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
            'type' => 'required|in:singleChoiceCard,singleChoiceChip,singleChoiceChipWithImage,singleChoiceDropdown,multiSelection,counterInput,dateCountInput,singleSelectionSlider,valueRangeSlider,rating,price,progress,productAges',
            'order' => 'nullable|integer',
            'options_ar' => 'nullable|array',
            'options_en' => 'nullable|array',
            'options_image.*' => 'nullable|image|max:2048',
            'options_min.*' => 'nullable|numeric',
            'options_max.*' => 'nullable|numeric',
            'sub_options_ar' => 'nullable|array',
            'sub_options_en' => 'nullable|array',
            'sub_options_min' => 'nullable|array',
            'sub_options_max' => 'nullable|array',
        ]);

        $data = $request->only([
            'category_id',
            'question_ar',
            'question_en',
            'description_ar',
            'description_en',
            'type',
            'order',
            'min_value',
            'max_value',
            'step',
            'stageing',
            'settings'
        ]);
        $data['is_required'] = $request->has('is_required');
        $data['is_active']   = $request->is_active ?? 1;

        $question = Question::create($data);

        $optionTypes = [
            'singleChoiceCard',
            'singleChoiceChip',
            'singleChoiceChipWithImage',
            'singleChoiceDropdown',
            'multiSelection'
        ];

        if (in_array($question->type, $optionTypes)) {
            foreach ($request->options_ar ?? [] as $index => $option_ar) {

                $imagePath = null;
                if (isset($request->options_image[$index])) {
                    $imagePath = $request->options_image[$index]->store('options', 'public');
                }

                // الخيار الرئيسي
                $option = QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar'   => $option_ar,
                    'option_en'   => $request->options_en[$index] ?? null,
                    'image'       => $imagePath,
                    'order'       => $index,
                    'min'         => $request->options_min[$index] ?? null,
                    'max'         => $request->options_max[$index] ?? null,
                    'is_active'   => true,
                ]);

                // التعامل مع الـ sub-options
                if(isset($request->sub_options_ar[$index]) && is_array($request->sub_options_ar[$index])) {
                    foreach ($request->sub_options_ar[$index] as $subIndex => $sub_ar) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_ar'   => $sub_ar,
                            'option_en'   => $request->sub_options_en[$index][$subIndex] ?? null,
                            'parent_option_id' => $option->id,
                            'order'       => $subIndex,
                            'min'         => $request->sub_options_min[$index][$subIndex] ?? null,
                            'max'         => $request->sub_options_max[$index][$subIndex] ?? null,
                            'is_active'   => true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('questions.index')
            ->with('success', 'تمت إضافة السؤال والخيارات بنجاح');
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
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',

            // هنا ضفنا slider
            'type' => 'required|in:singleChoiceCard,singleChoiceChip,singleChoiceChipWithImage,singleChoiceDropdown,multiSelection,counterInput,dateCountInput,singleSelectionSlider,valueRangeSlider,rating,price,progress,productAges',

            'is_required' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer',

            // للخيارات فقط
            'options_ar' => 'nullable|array',
            'options_en' => 'nullable|array',
            'options_id' => 'nullable|array',
            'options_image.*' => 'nullable|image|max:2048',

            // للسلايدر فقط
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'step' => 'nullable|numeric',
        ]);

        $data = $request->only([
            'category_id',
            'question_ar',
            'question_en',
            'description_ar',
            'description_en',
            'type',
            'is_required',
            'is_active',
            'order',
            'staging'
        ]);

        // إضافة قيم السلايدر
        if ($request->type === 'slider') {
            $data['min_value'] = $request->min_value ?? 0;
            $data['max_value'] = $request->max_value ?? 100;
            $data['step'] = $request->step ?? 1;
        } else {
            // لو النوع مش سلايدر نمسح القيم القديمة
            $data['min_value'] = null;
            $data['max_value'] = null;
            $data['step'] = null;
        }

        $question->update($data);

        /*
        |--------------------------------------------------------------------------
        | معالجة الخيارات (Select - Radio - Checkbox)
        |--------------------------------------------------------------------------
        */
        if (in_array($question->type, ['select', 'radio', 'checkbox'])) {

            $existingOptions = $request->options_id ?? [];

            // حذف اللي المستخدم مسحه
            \App\Models\QuestionOption::where('question_id', $question->id)
                ->whereNotIn('id', $existingOptions)
                ->delete();

            // إضافة/تعديل الخيارات
            foreach ($request->options_ar ?? [] as $index => $option_ar) {
                $option_en = $request->options_en[$index] ?? null;
                $imagePath = null;

                // صورة جديدة؟
                if (isset($request->options_image[$index])) {
                    $imagePath = $request->options_image[$index]->store('options', 'public');
                }

                if (isset($existingOptions[$index])) {
                    // تعديل
                    $option = \App\Models\QuestionOption::find($existingOptions[$index]);
                    if ($option) {
                        $option->update([
                            'option_ar' => $option_ar,
                            'option_en' => $option_en,
                            'image' => $imagePath ?? $option->image,
                            'order' => $index
                        ]);
                    }
                } else {
                    // إضافة جديد
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
        } else {
            // لو غير النوع إلى slider أو text → امسح الخيارات كلها
            \App\Models\QuestionOption::where('question_id', $question->id)->delete();
        }

        return redirect()->route('questions.index')
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'تم حذف السؤال بنجاح');
    }
}
