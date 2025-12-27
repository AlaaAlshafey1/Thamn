<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function store(Request $request, $question_id)
    {
        $user = $request->user();
        $question = Question::findOrFail($question_id);

        $request->validate([
            'option_id'     => 'nullable|exists:question_options,id',
            'sub_option_id' => 'nullable|exists:question_options,id',
            'value'         => 'nullable|string',
        ]);


        if ($question->type === 'multiSelection') {

            Answer::where('user_id', $user->id)
                ->where('question_id', $question->id)
                ->delete();

            foreach ($request->option_id ?? [] as $optionId) {
                Answer::create([
                    'user_id'     => $user->id,
                    'question_id' => $question->id,
                    'option_id'   => $optionId,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Answers saved'
            ]);
        }


        Answer::updateOrCreate(
            [
                'user_id'     => $user->id,
                'question_id' => $question->id,
            ],
            [
                'option_id'     => $request->option_id,
                'sub_option_id' => $request->sub_option_id,
                'value'         => $request->value,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Answer saved'
        ]);
    }

}
