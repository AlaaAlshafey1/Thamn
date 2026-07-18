<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionOption;
use App\Models\QuestionStep;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with(['questions' => function ($query) use ($request) {
            if ($request->filled('flow')) {
                if ($request->flow === 'valuation') {
                    $query->whereIn('flow', ['valuation', 'both']);
                } elseif ($request->flow === 'market') {
                    $query->whereIn('flow', ['market', 'both']);
                }
            }
            // Order questions by order ASC, then by created_at ASC
            $query->orderBy('order', 'asc')->orderBy('created_at', 'asc');
        }])->orderBy('created_at', 'asc')->get();

        $uncategorizedQuestions = Question::whereNull('category_id')->where(function($query) use ($request) {
            if ($request->filled('flow')) {
                if ($request->flow === 'valuation') {
                    $query->whereIn('flow', ['valuation', 'both']);
                } elseif ($request->flow === 'market') {
                    $query->whereIn('flow', ['market', 'both']);
                }
            }
        })->orderBy('order', 'asc')->orderBy('created_at', 'asc')->get();

        $steps = \App\Models\QuestionStep::orderBy('sort_order', 'asc')->get();

        return view('questions.index', [
            'categories' => $categories,
            'uncategorizedQuestions' => $uncategorizedQuestions,
            'steps' => $steps,
            'flow'      => $request->flow
        ]);
    }


    public function create(Request $request)
    {
        $categories = Category::where('is_active', 1)->get();

        $steps = QuestionStep::where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        return view('questions.create', compact('categories','steps'))
            ->with('flow', $request->flow);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'nullable|string|max:255',
            'type' => 'required',
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
            'flow' => 'required|in:valuation,market,both',

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
            'flow',
            'settings'
        ]);
        $data['group_type'] = $request->group_type;
        $data['is_required'] = $request->has('is_required');
        $data['is_active']   = $request->is_active ?? 1;

        $question = Question::create($data);

    //     $optionTypes = [
    // 'singleChoiceCard','singleChoiceChip',
    // 'singleChoiceChipWithImage','rateTypeSelection','productAges','singleChoiceDropdown','valueRangeSlider','singleSelectionSlider','multiSelection','progress'
    //     ];


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


        return redirect()->route('questions.index')
            ->with('success', 'تمت إضافة السؤال والخيارات بنجاح');
    }
    public function show(Question $question)
    {

        $question->load('category', 'options'); // جلب الفئة والخيارات
        return view('questions.show', compact('question'));
    }

    public function edit(Request $request, Question $question)
    {
        $steps = QuestionStep::where('is_active', 1)
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', 1)->get();

        return view('questions.edit', compact('question','categories','steps'))
            ->with('flow', $request->flow);
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',

            'type' => 'required',

            'order' => 'nullable|integer',
            'flow' => 'required|in:valuation,market,both',

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
            'settings',
            'flow',
            'group_type'
        ]);

        $data['group_type'] = $request->group_type;

        $data['is_required'] = $request->has('is_required');
        $data['is_active']   = $request->is_active ?? 1;

        $question->update($data);

        // Get the IDs of options and sub-options that are kept
        $keptOptionIds = $request->options_id ?? [];
        $keptSubOptionIds = [];
        if ($request->sub_options_id) {
            foreach ($request->sub_options_id as $subIds) {
                if (is_array($subIds)) {
                    $keptSubOptionIds = array_merge($keptSubOptionIds, $subIds);
                }
            }
        }
        $keptSubOptionIds = array_filter($keptSubOptionIds);

        // Delete options that are not in the kept lists
        QuestionOption::where('question_id', $question->id)
            ->whereNull('parent_option_id')
            ->whereNotIn('id', $keptOptionIds)
            ->delete();

        QuestionOption::where('question_id', $question->id)
            ->whereNotNull('parent_option_id')
            ->whereNotIn('id', $keptSubOptionIds)
            ->delete();

        foreach ($request->options_ar ?? [] as $index => $option_ar) {
            $optionId = $request->options_id[$index] ?? null;
            $imagePath = null;
            
            if (isset($request->options_image[$index])) {
                $imagePath = $request->options_image[$index]->store('options', 'public');
            }

            $optionData = [
                'question_id'   => $question->id,
                'parent_option_id'=> null,
                'option_ar'     => $option_ar,
                'option_en'     => $request->options_en[$index] ?? null,
                'description_ar'=> $request->options_description_ar[$index] ?? null,
                'description_en'=> $request->options_description_en[$index] ?? null,
                'order'         => $request->options_order[$index] ?? $index,
                'min'           => $request->options_min[$index] ?? null,
                'max'           => $request->options_max[$index] ?? null,
                'price'         => $request->options_price[$index] ?? null,
                'badge'         => $request->options_badge[$index] ?? null,
                'sub_options_title' => $request->options_subOptionsTitle[$index] ?? null,
                'is_active'     => true,
            ];

            if ($imagePath) {
                $optionData['image'] = $imagePath;
            }

            if ($optionId) {
                $option = QuestionOption::find($optionId);
                if ($option) {
                    $option->update($optionData);
                } else {
                    $option = QuestionOption::create($optionData);
                }
            } else {
                $option = QuestionOption::create($optionData);
            }

            // -------- sub options --------
            if (!empty($request->sub_options_ar[$index])) {
                foreach ($request->sub_options_ar[$index] as $subIndex => $sub_ar) {
                    $subOptionId = $request->sub_options_id[$index][$subIndex] ?? null;
                    
                    $subData = [
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
                    ];

                    if ($subOptionId) {
                        $subOption = QuestionOption::find($subOptionId);
                        if ($subOption) {
                            $subOption->update($subData);
                        } else {
                            QuestionOption::create($subData);
                        }
                    } else {
                        QuestionOption::create($subData);
                    }
                }
            }
        }


        return redirect()->route('questions.index')
            ->with('success', 'تم تحديث السؤال والخيارات بنجاح');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:questions,id',
            'order.*.position' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            Question::where('id', $item['id'])->update(['order' => $item['position']]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
    }

    public function toggleActive(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:questions,id',
            'is_active' => 'required|boolean',
        ]);

        $question = Question::findOrFail($request->id);
        $question->is_active = $request->is_active;
        $question->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث حالة السؤال بنجاح']);
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'تم حذف السؤال بنجاح');
    }

    public function duplicate(Request $request, Question $question)
    {
        // نسخ السؤال مع إمكانية تغيير الوجهة من الـ modal
        $newQuestion = $question->replicate();
        $newQuestion->question_ar = $question->question_ar . ' (نسخة)';
        $newQuestion->order = $question->order + 1;

        // طبّق الخيارات من الـ modal إذا أُرسلت
        if ($request->filled('flow'))        $newQuestion->flow        = $request->flow;
        if ($request->filled('category_id')) $newQuestion->category_id = $request->category_id;
        if ($request->filled('stageing'))    $newQuestion->stageing    = $request->stageing;

        $newQuestion->save();

        // نسخ الخيارات الرئيسية مع الـ sub-options
        $question->load('options');
        foreach ($question->options->whereNull('parent_option_id') as $option) {
            $newOption = $option->replicate();
            $newOption->question_id = $newQuestion->id;
            $newOption->save();

            foreach ($question->options->where('parent_option_id', $option->id) as $subOption) {
                $newSub = $subOption->replicate();
                $newSub->question_id = $newQuestion->id;
                $newSub->parent_option_id = $newOption->id;
                $newSub->save();
            }
        }

        $targetFlow = $request->current_flow ?? $request->flow ?? request('flow');

        return redirect()
            ->route('questions.index', ['flow' => $targetFlow])
            ->with('success', 'تم نسخ السؤال "' . $question->question_ar . '" بنجاح ✓');
    }
}
