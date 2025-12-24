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

}
