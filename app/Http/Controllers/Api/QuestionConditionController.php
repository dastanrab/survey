<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\QuestionConditionRequest;
use App\Models\Question;

class QuestionConditionController extends Controller
{
    public function create(QuestionConditionRequest $request,$question_id)
    {
        $inputs=$request->validated();
        $question=Question::query()->where('id',$question_id)->first();
        if ($question)
        {
            $question_type=Helper::get_question_type_instance($question->question_type_id);
            $question_type->CanCondition();
            $question_type->LoadQuestion($question);
            return response()->json($question_type->AddCondition($inputs['start'],$inputs['end'],$inputs['creator_frotel_id'],$inputs['details']));
        }
        return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);
    }
}
