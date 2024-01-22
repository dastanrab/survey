<?php

namespace App\Classes\Question_type;

use App\Models\PollQuestion;
use App\Models\Question;
use App\Models\QuestionConditionTemplate;
use App\Trait\ConditionFuncs;
use App\Trait\HandleModelError;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class YesNoQuestion implements QuestionTypeInterface
{
    use HandleModelError,ConditionFuncs;
    private Question $question_model;
    private PollQuestion $poll_question_model;
    private QuestionConditionTemplate $question_template;
    public $current_question;

    public function __construct()
 {
     $this->question_model = new Question();
     $this->poll_question_model = new PollQuestion();
     $this->question_template=new QuestionConditionTemplate();
 }

    /**
     * @param string $title
     * @param int $type
     * @param int|null $parent_id
     * @return array
     */
    public function Create(string $title, int $type, int $parent_id = null,array $details = null): array
    {

        DB::beginTransaction();
        try {
            $this->LoadQuestion($this->question_model->query()->create(['title'=>$title,'question_type_id'=>$type,'question_parent_id'=>null]));
            $template= $this->AddTemplate('مقدار بله - خیر',1);
            DB::commit();
            return $this->response(true,'با موفقیت ایجاد شد',['question'=>$this->current_question->toArray(),'template'=>$template]);

        }catch (Exception $exception)
        {
            DB::rollBack();
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }
    }
    public function AddCondition($start,$end,$creator_frotel_id,$condition_templates)
    {
        $template=$this->question_template->query()->where('question_id',$this->current_question->id)->whereNull('parent_question_answerd_condition_id')->first();
        $can=$this->check_condition_range_overlap($start,$end,$this->current_conditions($this->current_question->id),$template->value);
        if ($can)
        {
            DB::beginTransaction();
            try {

                $condition=\App\Models\QuestionCondition::query()->create(['start_range'=>$start,'end_range'=>$end,'question_id'=>$this->current_question->id,'creator_frotel_id'=>$creator_frotel_id]);
                $templates=[];
                foreach ($condition_templates as $key => $value)
                {
                    $templates[]= $this->AddTemplate($value,$key,$condition->id);
                }
                DB::commit();
                return $this->response(true,'با موفقیت ایجاد شد',['conditin'=>$condition->toArray(),'condition_templates'=>$templates]);
            }catch (Exception $exception)
            {
                DB::rollBack();
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,$this->ErrorMassage($exception->getCode())),500));
            }

        }
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'بازه وارد شده با بازه های موجود تداخل دارد'),403));


    }

    /**
     * @return mixed
     */
    public function CanCondition()
    {
        return true;
    }

    /**
     * @param string $title
     * @param int $value
     * @param int|null $parent_id
     * @return array|\HttpResponseException|bool
     */
    public function CreateAnswerTemplate(string $title, int $value, int $parent_id = null): array|\HttpResponseException
    {
        return [];
    }

    /**
     * @param int $poll_id
     * @param int $frotel_id
     * @return array
     */
    public function AddToPoll(int $poll_id, int $frotel_id): array
    {
        return [];
    }

    /**
     * @param Model $model
     * @return mixed
     */
    public function LoadQuestion(Model $model)
    {
        $this->current_question=$model;
    }
    private function AddTemplate($title,$value,$parent_id=null)
    {
        try {
            return \App\Models\QuestionConditionTemplate::query()->create(['title'=>$title,'value'=>$value,'question_id'=>$this->current_question->id,'parent_question_answerd_condition_id'=>$parent_id])->toArray();

        }catch (Exception $exception){
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * @return bool|\HttpResponseException
     */
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

    /**
     * @param array $details
     * @return bool|\HttpResponseException
     */
    public function ValidateQuestionDetails(array $details): bool|\HttpResponseException
    {
        return true;
    }

    /**
     * @param string $title
     * @param int|null $parent_id
     * @return mixed
     */
    public function Update(string $title, int $parent_id = null)
    {
        // TODO: Implement Update() method.
    }
    private function response(bool $status,string $msg,mixed $data):array
    {
        return ['status'=>$status,'msg'=>$msg,'data'=>$data];
    }
}
