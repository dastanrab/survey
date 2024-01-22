<?php

namespace App\Classes\Question_answer;

use App\Models\QuestionCondition;
use App\Models\QuestionConditionTemplate;
use App\Models\QuestionUserAnswer;
use App\Trait\HandleModelError;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class ScoreAnswer implements Question_Answer
{
    use HandleModelError;
    public $current_question;
    public $answer_model;
    private $condition_id;
    public function __construct()
    {
        $this->answer_model = new QuestionUserAnswer();
    }
    public function Answer($value,$voter_frotel_id,$system_id,$details=null,$frotel_id=null,$refrence_id=null)
    {
       $this->ValidateValue($value,$voter_frotel_id);
       $condition=$this->ValidateCondition($details,$value);
        DB::beginTransaction();
        try {
             \App\Models\QuestionUserAnswer::query()->create(['question_id'=>$this->current_question->id,'voter_frotel_id'=>$voter_frotel_id,$this->current_question->id,'value_answer'=>$value,'text_answer'=>$this->current_question->title]);
            \App\Models\Score::query()->create(['frotel_id'=>$frotel_id,'refrence_id','system_id'=>$system_id,'score'=>$value,'created_at','updated_at']);
            if ($condition)
            {
                foreach ($details as  $value)
                {
                   $this->AnswerCondition($value['title'],$value['value'],$this->current_question->id,$voter_frotel_id,$this->condition_id);
                }
            }
            DB::commit();
        }catch (Exception $exception)
        {
            DB::rollBack();
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,$this->ErrorMassage($exception->getCode())),500));
        }


    }
    public function AnswerCondition($title,$value,$question_id,$voter_frotel_id,$condition_question_id)
    {
        try {
            return \App\Models\QuestionUserAnswer::query()->create(['voter_frotel_id'=>$voter_frotel_id,'question_id'=>$question_id,'condition_question_id'=>$condition_question_id,'value_answer'=>$value,'text_answer'=>$title])->toArray();

        }catch (Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    public function HaveCondition(): bool|HttpResponseException
    {
        return true;
    }

    public function LoadQuestion(Model $model)
    {
        $this->current_question=$model;
    }
    public function ValidateValue($value,$voter_frotel_id){
        if (QuestionUserAnswer::query()->where('voter_frotel_id',$voter_frotel_id)->whereNull('condition_question_id')->where('question_id',$this->current_question->id)->exists())
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"شما قبلا به این سوال امتیازی پاسخ داده اید"),403));

        }
        if (!is_numeric($value))
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"نوع جواب برای سوالی امتیازی باید عددی باشد"),422));
        }
        $question_value=QuestionConditionTemplate::query()->where('question_id',$this->current_question->id)->first();
        if ($value>$question_value->value)
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"مقدار جواب نباید ار سقف امتیاز بیشتر باشد"),422));

        }
        return  true;
    }
    public function ValidateCondition($details,$value)
    {
        $exist=QuestionCondition::query()->where('question_id',$this->current_question->id)->first();
        if ($exist){
            if (!count($details)>0){
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"برای بازه شرط ها داده ای ارسال نکرده اید"),422));
            }
            $condition=QuestionCondition::query()->where('question_id',$this->current_question->id)->where('start_range', '<', $value)->where('end_range', '>=', $value)->first();
            if (!$condition){
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"بازه مناسبی یافت نشد"),404));
            }
            $condition_questions=QuestionConditionTemplate::query()->where('parent_question_answerd_condition_id',$condition->id)->get();
            $this->condition_id=$condition->id;
            if (count($details)>count($condition_questions))
            {
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"جواب های ارسالی برای بازه شرط بیش از حد تعیین شده"),422));

            }
            $titles=$condition_questions->keyBy('value')->map(function ($value) {
                return $value->title;
            })->toArray();
            foreach ($details as $detail)
            {
                if (!isset($titles[$detail['value']]) or !($titles[$detail['value']]==$detail['title']))
                {
                  $title= $detail['title'];
                    throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"$title ارسال شده در بین جواب های تعیین شده نیست "),403));
                }
            }
            return true;
        }
        return false;

    }
    private function response(bool $status,string $msg,mixed $data):array
    {
        return ['status'=>$status,'msg'=>$msg,'data'=>$data];
    }
}
