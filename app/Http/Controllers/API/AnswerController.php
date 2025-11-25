<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function store(Request $request, Question $question)
    {
        $user = $request->user();

        $validated = $request->validate([
            'answer'  => 'nullable|string',
            'options' => 'nullable|array',
        ]);

        // لو السؤال اختيار Checkbox
        if ($question->type === 'checkbox') {
            $validated['options'] = json_encode($validated['options'] ?? []);
        }

        // مسح أى إجابة قديمة لنفس المستخدم والسؤال
        Answer::updateOrCreate(
            [
                'user_id' => $user->id,
                'question_id' => $question->id
            ],
            [
                'answer'  => $validated['answer'] ?? null,
                'options' => $validated['options'] ?? null,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => lang("تم حفظ الإجابة", "Answer saved", $request)
        ]);
    }
}
