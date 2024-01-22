<?php

namespace App\Classes\Question_type;

use App\Classes\Helper;
use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\Question;
use App\Models\QuestionCondition;
use App\Models\QuestionConditionTemplate;
use App\Trait\ConditionFuncs;
use App\Trait\HandleModelError;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class ScoreQuestion implements QuestionTypeInterface
{
    use HandleModelError,ConditionFuncs;
    private Question $question_model;
    private PollQuestion $poll_question_model;
    public $current_question;
    private QuestionConditionTemplate $question_template;

    public function __construct()
    {
        $this->question_model = new Question();
        $this->poll_question_model = new PollQuestion();
        $this->question_template=new QuestionConditionTemplate();
    }

    public function Create(string $title, int $type, int $parent_id = null,array $details = []): array
    {
        $this->ValidateQuestionDetails($details);
        DB::beginTransaction();
        try {
            $this->LoadQuestion($this->question_model->query()->create(['title'=>$title,'question_type_id'=>$type,'question_parent_id'=>null]));
            $template= $this->AddTemplate('سقف امتیاز',$details['score_value']);
            DB::commit();
            return $this->response(true,'با موفقیت ایجاد شد',['question'=>$this->current_question->toArray(),'template'=>$template]);

        }catch (Exception $exception)
        {
            DB::rollBack();
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }
    }

    public function AddToPoll(int $poll_id, int $frotel_id): array
    {
        $this->CanPoll();
        $poll=Poll::query()->where('id',$poll_id)->first();
        if (!$poll)
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'نظری یافت نشد'),404));
        }
        $check=$this->poll_question_model->query()->where('poll_id',$poll_id)->where('question_id',$this->question_model->id)->exists();
        if ($check){
            return $this->response(false,'این سوال قبلا در این نظر سنجی استفاده شده است',[]);
        }
        DB::beginTransaction();
        try {
            $poll_question= $this->poll_question_model->query()->create(['poll_id'=>$poll_id,'creator_frotel_id'=>$frotel_id,'question_id'=>$this->current_question->id]);
            DB::commit();
            return $this->response(true,'با موفقیت ایجاد شد',['poll'=>$poll,'question'=>$this->current_question->toArray(),'poll_question'=>$poll_question->toArray()]);

        }catch (Exception $exception)
        {
            DB::rollBack();
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }
    }

    public function CanCondition()
    {
        return true;
    }

    public function CreateAnswerTemplate( string $title, int $value, int $parent_id = null): \HttpResponseException|array
    {
        $this->CheckValueSet();
        try {

            $template=\App\Models\QuestionConditionTemplate::query()->create(['title'=>$title,'value'=>$value,'question_id'=>$this->current_question->id,'parent_question_answerd_condition_id'=>$parent_id]);
            return $this->response(true,'با موفقیت ایجاد شد',$template->toArray());
        }catch (Exception $exception)
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,$this->ErrorMassage($exception->getCode())),500));
        }
    }
    public function AddCondition($start,$end,$creator_frotel_id,$condition_templates)
    {
        return $this->add_condition($start,$end,$creator_frotel_id,$condition_templates);

    }
    public function AddConditions($conditions)
    {
//        $template=\App\Models\QuestionConditionTemplate::query()->create(['start_range'=>$start,'end_range'=>$end,'question_id'=>$this->current_question->id,'parent_question_answerd_condition_id'=>$parent_id]);

    }

    public function LoadQuestion(Model $model)
    {
        $this->current_question=$model;
    }
    private function response(bool $status,string $msg,mixed $data):array
    {
        return ['status'=>$status,'msg'=>$msg,'data'=>$data];
    }

    public function CanPoll(): bool|\HttpResponseException
    {
        $exist=QuestionConditionTemplate::query()->where('question_id',$this->current_question->id)->first();
        if (!$exist)
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'برای این سوال که از نوع امتیازی است ابتدا باید سقف امتیاز تعیین کنید'),403));

        }
        $this->check_continuity($this->current_conditions($this->current_question->id),$exist->value);
        return true;
    }
    private function CheckValueSet()
    {
       $exist=QuestionConditionTemplate::query()->where('question_id',$this->current_question->id)->exists();

       if ($exist)
       {
           throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'برای این سوال که از نوع امتیازی است قبلا سقف امتیاز تعیین شده است'),403));

       }
    }
    private function AddTemplate($title,$value,$parent_id=null)
    {
        try {
            return \App\Models\QuestionConditionTemplate::query()->create(['title'=>$title,'value'=>$value,'question_id'=>$this->current_question->id,'parent_question_answerd_condition_id'=>$parent_id])->toArray();

        }catch (Exception $exception){
            throw new Exception($exception->getMessage());
        }

    }
    private function UpdateTemplate($title,$value,$question_id)
    {
        try {
            $question=\App\Models\QuestionConditionTemplate::query()->where('question_id',$question_id)->whereNull('parent_question_answerd_condition_id')->first();
            if ($question)
            {
                QuestionCondition::query()->where('question_id',$this->current_question->id)->delete();
                return $question->update(['title'=>$title,'value'=>$value]);
            }
            return \App\Models\QuestionConditionTemplate::query()->create(['title'=>$title,'value'=>$value,'question_id'=>$this->current_question->id,'parent_question_answerd_condition_id'=>null])->toArray();

        }catch (Exception $exception){
            throw new Exception($exception->getMessage());
        }

    }

    public function ValidateQuestionDetails(array $details): bool|\HttpResponseException
    {
        if (isset($details['score_value']) and is_numeric($details['score_value']))
        {
            return true;
        }
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'میزان سقف امتیاز برای سوال وارد نشده است'),422));
    }

    public function Update(string $title, int $parent_id = null,array $details = null)
    {
        $this->ValidateQuestionDetails($details);
        DB::beginTransaction();
        try {
            $this->question_model->query()->where('id',$this->current_question->id)->update(['title'=>$title,'question_type_id'=>$this->current_question->question_type_id,'question_parent_id'=>$this->current_question->question_parent_id]);
            $this->UpdateTemplate('سقف امتیاز',$details['score_value'],$this->current_question->id);
            DB::commit();
            return $this->response(true,'با موفقیت ایجاد شد',['question'=>$this->current_question->refresh()->toArray()]);

        }catch (Exception $exception)
        {
            DB::rollBack();
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }
    }


}
