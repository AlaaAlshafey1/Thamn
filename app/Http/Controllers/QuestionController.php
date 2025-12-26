<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionOption;
use App\Models\QuestionStep;

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
        $steps = QuestionStep::where('is_active', 1)
                    ->orderBy('sort_order')
                    ->get();

        return view('questions.create', compact('categories','steps'));
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
    'singleChoiceCard','singleChoiceChip',
    'singleChoiceChipWithImage','singleChoiceDropdown','valueRangeSlider','singleSelectionSlider','multiSelection','progress'
        ];

        if (in_array($question->type, $optionTypes)) {
            foreach ($request->options_ar ?? [] as $index => $option_ar) {

                $imagePath = null;
                if (isset($request->options_image[$index])) {
                    $imagePath = $request->options_image[$index]->store('options', 'public');
                }

                $option = QuestionOption::create([
                    'question_id'   => $question->id,
                    'option_ar'     => $option_ar,
                    'option_en'     => $request->options_en[$index] ?? null,
                    'description_ar'=> $request->options_description_ar[$index] ?? null,
                    'description_en'=> $request->options_description_en[$index] ?? null,
                    'image'         => $imagePath,
                    'order'         => $request->options_order[$index] ?? $index,
                    'min'           => $request->options_min[$index] ?? null,
                    'max'           => $request->options_max[$index] ?? null,
                    'price'         => $request->options_price[$index] ?? null,
                    'badge'         => $request->options_badge[$index] ?? null,
                    'sub_options_title' => $request->options_subOptionsTitle[$index] ?? null,
                    'is_active'     => true,
                ]);

                // التعامل مع الـ sub-options
                if(isset($request->sub_options_ar[$index]) && is_array($request->sub_options_ar[$index])) {
                    foreach ($request->sub_options_ar[$index] as $subIndex => $sub_ar) {
                        QuestionOption::create([
                            'question_id'      => $question->id,
                            'parent_option_id' => $option->id,
                            'option_ar'        => $sub_ar,
                            'option_en'        => $request->sub_options_en[$index][$subIndex] ?? null,
                            'description_ar'   => $request->sub_options_description_ar[$index][$subIndex] ?? null,
                            'description_en'   => $request->sub_options_description_en[$index][$subIndex] ?? null,
                            'order'            => $subIndex,
                            'min'              => $request->sub_options_min[$index][$subIndex] ?? null,
                            'max'              => $request->sub_options_max[$index][$subIndex] ?? null,
                            'is_active'        => true,
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
        $steps = QuestionStep::where('is_active', 1)
            ->orderBy('sort_order')
            ->get();
        $categories = Category::where('is_active', 1)->get();
        return view('questions.edit', compact('question','categories','steps'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',

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

        // ===================== تحديث السؤال =====================
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

        $question->update($data);

        // ===================== مسح كل الخيارات القديمة =====================
        QuestionOption::where('question_id', $question->id)->delete();

        // ===================== أنواع الأسئلة اللي ليها options =====================
        $optionTypes = [
            'singleChoiceCard','singleChoiceChip',
            'singleChoiceChipWithImage','singleChoiceDropdown','valueRangeSlider','singleSelectionSlider','multiSelection','progress'
        ];

        if (in_array($question->type, $optionTypes)) {

            foreach ($request->options_ar ?? [] as $index => $option_ar) {

                $imagePath = null;
                if (isset($request->options_image[$index])) {
                    $imagePath = $request->options_image[$index]->store('options', 'public');
                }

                // -------- الخيار الرئيسي --------
                $option = QuestionOption::create([
                    'question_id'   => $question->id,
                    'parent_option_id'=> null,
                    'option_ar'     => $option_ar,
                    'option_en'     => $request->options_en[$index] ?? null,
                    'description_ar'=> $request->options_description_ar[$index] ?? null,
                    'description_en'=> $request->options_description_en[$index] ?? null,
                    'image'         => $imagePath,
                    'order'         => $request->options_order[$index] ?? $index,
                    'min'           => $request->options_min[$index] ?? null,
                    'max'           => $request->options_max[$index] ?? null,
                    'price'         => $request->options_price[$index] ?? null,
                    'badge'         => $request->options_badge[$index] ?? null,
                    'sub_options_title' => $request->options_subOptionsTitle[$index] ?? null,
                    'is_active'     => true,
                ]);

                // -------- sub options --------
                if (!empty($request->sub_options_ar[$index])) {
                    foreach ($request->sub_options_ar[$index] as $subIndex => $sub_ar) {
                        QuestionOption::create([
                            'question_id'      => $question->id,
                            'parent_option_id' => $option->id,
                            'option_ar'        => $sub_ar,
                            'option_en'        => $request->sub_options_en[$index][$subIndex] ?? null,
                            'description_ar'   => $request->sub_options_description_ar[$index][$subIndex] ?? null,
                            'description_en'   => $request->sub_options_description_en[$index][$subIndex] ?? null,
                            'order'            => $subIndex,
                            'min'              => $request->sub_options_min[$index][$subIndex] ?? null,
                            'max'              => $request->sub_options_max[$index][$subIndex] ?? null,
                            'price'            => $request->options_price[$index] ?? null,
                            'badge'            => $request->options_badge[$index] ?? null,
                            'sub_options_title' => $request->options_subOptionsTitle[$index] ?? null,

                            'is_active'        => true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('questions.index')
            ->with('success', 'تم تحديث السؤال والخيارات بنجاح');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'تم حذف السؤال بنجاح');
    }
}
