<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AnswerRequest;
use App\Models\Poll;
use App\Models\Question;
use App\Models\SystemCategoryPoll;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function showPollQuestions($system_id,$category_id,$poll_id)
    {
       $system_category_poll= SystemCategoryPoll::query()->where('system_id',$system_id)->where('category_id',$category_id)->where('poll_id',$poll_id)->first();
       if ($system_category_poll)
       {
           return response()->json(Helper::response_body(true,'سوالات نظرسنجی مورد نظر',['system_id'=>$system_id,'category_id'=>$category_id,'poll_question'=>Poll::query()->where('id',$poll_id)->with(['questions.question_type','questions.question_templates','questions.question_conditions'])->first()]));

       }
        return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);
    }
    public function create(AnswerRequest $request)
    {
        $inputs=$request->validated();
        $system_category_poll= SystemCategoryPoll::query()->where('system_id',$inputs['system_id'])->where('category_id',$inputs['category_id'])->where('poll_id',$inputs['poll_id'])->first();
        if ($system_category_poll)
        {
            Helper::check_poll_expire($system_category_poll->expire_at);
            foreach ($inputs['data'] as $answer)
            {
                $question=Question::query()->where('id',$answer['question_id'])->first();
                $answer_template=Helper::get_question_answer_instance($question->question_type_id);
                $answer_template->LoadQuestion($question);
                $answer_template->Answer($answer['value'],$inputs['voter_frotel_id'],$inputs['system_id'],details:@$answer['details'],frotel_id: @$inputs['frotel_id'],refrence_id: @$inputs['refrence_id']);
            }
            return response()->json(Helper::response_body(true,'جواب با موفقیت ثبت شد'));
        }
        return response()->json(Helper::response_body(false,'نظرسنجی برای پاسخ دهی یافت نشد'),404);

    }
}
