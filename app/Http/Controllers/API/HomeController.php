<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\QuestionStep;
use App\Models\TermCondition;

class HomeController extends Controller
{
    public function categories(Request $request)
    {
        $categories = Category::orderBy('sort_order')->get();

        return response()->json([
            'status' => true,
            'message' => lang('تم إرجاع الفئات بنجاح', 'Categories fetched successfully', $request),
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function allQuestions($categoryId, Request $request)
    {

        $questions = Question::with(['category', 'options'])
            ->where('is_active', 1)
            ->whereIn('flow', ['valuation', 'both'])
            ->where('category_id', $categoryId)
            ->orderBy('order')
            ->get();


        $locale = strtolower($request->header('Accept-Language', 'en'));
        $groupedQuestions = $questions->groupBy('stageing');

        // جلب جميع الخطوات دفعة واحدة لتجنب N+1 وللترتيب حسب sort_order
        $steps = QuestionStep::whereIn('id', $groupedQuestions->keys())->get()->keyBy('id');

        $stages = $groupedQuestions->map(function ($questionsOfStep, $stageId) use ($steps, $locale) {
            $step = $steps->get($stageId);

            return [
                'step' => (int) $stageId,
                'name' => $locale == "ar" ? ($step->name_ar ?? "") : ($step->name_en ?? ""),
                'questions' => QuestionResource::collection($questionsOfStep),
                'sort_order' => $step ? $step->sort_order : 999,
            ];
        })->sortBy('sort_order')->values()->map(function ($item) {
            unset($item['sort_order']);
            return $item;
        });
        return response()->json([
            'success' => true,
            'data' => $stages,
            'meta' => [
                'image_generation_fee' => env('IMAGE_GENERATION_FEE', 5),
                'image_generation_message' => $locale == "ar" 
                    ? 'سيتم إضافة رسوم لتوليد صورة افتراضية بالذكاء الاصطناعي في حال عدم إرفاق صور.'
                    : 'An extra fee will be added to generate a virtual AI image if no images are uploaded.'
            ]
        ]);
    }

    public function marketQuestionsByGroup($categoryId, Request $request)
    {
        $locale = strtolower($request->header('Accept-Language', 'en'));

        $questions = Question::with(['category', 'options.subOptions'])
            ->where('is_active', 1)
            ->whereIn('flow', ['market', 'both'])
            ->where('category_id', $categoryId)
            ->orderBy('order')
            ->get();

        $groups = ['first' => [], 'main' => [], 'secondary' => []];

        foreach ($questions as $q) {
            $groupType = $q->group_type ?? 'secondary'; // افتراضي
            $groups[$groupType][] = [
                'id' => $q->id,
                'category_id' => $q->category_id,
                'category_name' => $locale === 'ar'
                    ? ($q->category->name_ar ?? '')
                    : ($q->category->name_en ?? ''),
                'label' => $locale === 'ar' ? $q->question_ar : $q->question_en,
                'hint' => $q->settings['hint'][$locale] ?? null,
                'titleDescription' => $q->settings['titleDescription'][$locale] ?? null,
                'description' => $locale === 'ar' ? $q->description_ar : $q->description_en,
                'type' => $q->type,
                'is_required' => (bool) $q->is_required,
                'order' => (int) $q->order,
                'min_value' => $q->min_value,
                'max_value' => $q->max_value,
                'step' => $q->step,
                'addSearch' => $q->settings['addSearch'] ?? false,
                'useCupertinoPicker' => $q->settings['useCupertinoPicker'] ?? false,
                'options' => $q->options->map(function ($opt) use ($locale) {
                    return [
                        'id' => $opt->id,
                        'name' => $locale === 'ar' ? $opt->option_ar : $opt->option_en,
                        'image' => $opt->image,
                        'description' => $locale === 'ar' ? $opt->description_ar : $opt->description_en,
                        'order' => $opt->order,
                        'min' => $opt->min,
                        'max' => $opt->max,
                        'price' => $opt->price,
                        'badge' => $opt->badge,
                        'subOptionsTitle' => $opt->subOptionsTitle,
                        'sub_options' => $opt->subOptions->map(function ($sub) use ($locale) {
                            return [
                                'id' => $sub->id,
                                'label' => $locale === 'ar' ? $sub->question_ar : $sub->question_en,
                                'order' => $sub->order,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $groups
        ]);
    }

    public function terms(Request $request)
    {
        $lang = $request->header('Accept-Language', 'ar');

        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'ar';

        $terms = TermCondition::where('is_active', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($term) use ($lang) {
                return [
                    'id' => $term->id,
                    'title' => $lang === 'ar' ? $term->title_ar : $term->title_en,
                    'content' => $lang === 'ar' ? $term->content_ar : $term->content_en,
                    'file' => $term->file ? asset($term->file) : null,
                    'order' => $term->sort_order,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => lang(
                'تم إرجاع الشروط والأحكام بنجاح',
                'Terms & conditions fetched successfully',
                $request
            ),
            'data' => $terms,
        ]);
    }


}
