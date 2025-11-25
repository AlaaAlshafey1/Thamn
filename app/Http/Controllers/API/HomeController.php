<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\QuestionResource;
use App\Models\Question;

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

    public function allQuestions($id)
    {
        $questions = Question::with(['category', 'options'])
                            ->where('is_active', 1)
                            ->where('category_id', $id)
                            ->orderBy('order')
                            ->get();

        return response()->json([
            'status' => true,
            'data'   => QuestionResource::collection($questions),
        ]);
    }

}
