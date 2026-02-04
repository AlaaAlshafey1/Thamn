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
use App\Models\About;
use App\Models\Color;
use App\Models\Faq;
use App\Models\Contact;

class HomeController extends Controller
{
    public function categories(Request $request)
    {
        $categories = Category::orderBy('id', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => lang('تم إرجاع الفئات بنجاح', 'Categories fetched successfully', $request),
            'data'    => CategoryResource::collection($categories),
        ]);
    }

    public function allQuestions($categoryId , Request $request)
    {

        $questions = Question::with(['category', 'options'])
                            ->where('is_active', 1)
                            ->whereIn('flow', ['valuation', 'both'])
                            ->where('category_id', $categoryId)
                            ->orderBy('order')
                            ->get();


        $stages = $questions->groupBy('stageing')->map(function($questions, $stage ) use($request) {

            $Question_step = QuestionStep::where("id",$stage)->value("name_ar");
            $locale = strtolower($request->header('Accept-Language', 'en'));

            return [
                'step' => (int) $stage,
                'name' => $locale  == "ar" ? QuestionStep::where("id",$stage)->value("name_ar") : QuestionStep::where("id",$stage)->value("name_en"),
                'questions' => $questions->isNotEmpty()
                    ? QuestionResource::collection($questions)
                    : collect(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $stages
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
                'is_required' => (bool)$q->is_required,
                'order' => (int)$q->order,
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
                    'id'      => $term->id,
                    'title'   => $lang === 'ar' ? $term->title_ar : $term->title_en,
                    'content' => $lang === 'ar' ? $term->content_ar : $term->content_en,
                    'order'   => $term->sort_order,
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => lang(
                'تم إرجاع الشروط والأحكام بنجاح',
                'Terms & conditions fetched successfully',
                $request
            ),
            'data' => $terms,
        ]);
    }



    public function appData(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'en';

        // ------------------- الصفحات -------------------
        $pages = About::all()->mapWithKeys(function($page) use ($lang) {
            return [
                $page->type => [
                    'content' => $lang === 'ar' ? $page->content_ar : $page->content_en
                ]
            ];
        });

        // ------------------- FAQs -------------------
        $faqs = Faq::all()->map(function($faq) use ($lang) {
            return [
                'id' => $faq->id,
                'category' => $faq->category,
                'question' => $lang === 'ar' ? $faq->question_ar : $faq->question_en,
                'answer' => $lang === 'ar' ? $faq->answer_ar : $faq->answer_en,
            ];
        });

        // ------------------- بيانات الاتصال -------------------
        $contacts = Contact::all()->map(function($contact) use ($lang) {
            return [
                'id' => $contact->id,
                'type' => $contact->type,
                'value' => $contact->value,
                'label' => $lang === 'ar' ? $contact->label_ar : $contact->label_en,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => $lang === 'ar' ? 'تم إرجاع بيانات التطبيق بنجاح' : 'App data fetched successfully',
            'data' => [
                'pages' => $pages,
                'faqs' => $faqs,
                'contacts' => $contacts
            ]
        ]);
    }

    public function colors()
    {
        // جلب كل الألوان
        $colors = Color::orderBy('group')->get();

        // ترتيبهم حسب المجموعة
        $grouped = $colors->groupBy('group')->map(function ($group) {
            return $group->pluck('value','key');
        });

        return response()->json([
            'status' => true,
            'data' => $grouped
        ]);
    }
}
