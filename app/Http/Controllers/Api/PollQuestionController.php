<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PollQuestionRequest;
use App\Models\Poll;
use Illuminate\Http\Request;

class PollQuestionController extends Controller
{
    public function create(PollQuestionRequest $request,$poll_id)
    {
        $inputs=$request->validated();
        $q=\App\Models\Question::query()->where('id',$inputs['question_id'])->first();
        $questin=Helper::get_question_type_instance($q->question_type_id);
        $questin->LoadQuestion($q);
        $poll_question=$questin->AddToPoll($poll_id,$inputs['creator_frotel_id']);
        if ($poll_question['status'])
        {
            return response()->json(Helper::response_body(true,'سوال با موفقیت ایجاد شد',[],$poll_question['data']));

        }
        return response()->json(Helper::response_body(false,$poll_question['msg']));
    }
    public function index($poll_id)
    {
        $poll=Poll::query()->where('id',$poll_id)->with(['questions.question_type','questions.question_conditions.question_condition_templates'])->first();
        return response()->json(\App\Classes\Helper::response_body(true,'لیست  سوالات نظرسنجی',[],$poll));

    }
}
