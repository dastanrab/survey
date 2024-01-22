<?php

namespace App\Classes\Question_type;

use App\Models\Poll;
use App\Models\PollQuestion;
use App\Models\Question;
use App\Trait\HandleModelError;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class TextQuestion implements QuestionTypeInterface
{
    use HandleModelError;
    private Question $question_model;
    private PollQuestion $poll_question_model;
    public $current_question;
    public function __construct()
    {
        $this->question_model = new Question();
        $this->poll_question_model = new PollQuestion();
    }

    public function Create(string $title, int $type, int $parent_id = null,array $details = []): array
    {
        try {
            $this->LoadQuestion($this->question_model->query()->create(['title'=>$title,'question_type_id'=>$type,'question_parent_id'=>null]));
            return $this->response(true,'با موفقیت ایجاد شد',['question'=>$this->current_question->toArray()]);
        }catch (Exception $exception)
        {
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }

    }

    public function AddToPoll(int $poll_id, int $frotel_id): array
    {
        try {
            $poll=Poll::query()->where('id',$poll_id)->first();
            if (!$poll)
            {
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'نظری یافت نشد'),404));
            }
            $check=$this->poll_question_model->query()->where('poll_id',$poll_id)->where('question_id',$this->question_model->id)->exists();
            if ($check){
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'این سوال قبلا در این نظر سنجی استفاده شده است'),409));
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

        catch (Exception $exception)
        {
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }

    }
    private function response(bool $status,string $msg,mixed $data):array
    {
        return ['status'=>$status,'msg'=>$msg,'data'=>$data];
    }

    public function CanCondition()
    {
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'نوع سوال متنی نمی تواند شرطی باشد'),403));

    }
    public function LoadQuestion(Model $model)
    {
        $this->current_question=$model;
    }

    public function CanPoll(): bool|\HttpResponseException
    {
        return true;
    }

    public function ValidateQuestionDetails(array $details): bool|\HttpResponseException
    {
        return true;
    }

    public function CreateAnswerTemplate(string $title, int $value, int $parent_id = null): array|\HttpResponseException
    {
        return [];
    }

    public function Update(string $title, int $parent_id = null,array $details = null)
    {
        DB::beginTransaction();
        try {
            $this->question_model->query()->where('id',$this->current_question->id)->update(['title'=>$title,'question_type_id'=>$this->current_question->question_type_id,'question_parent_id'=>$this->current_question->question_parent_id]);
            DB::commit();
            return $this->response(true,'با موفقیت ایجاد شد',['question'=>$this->current_question->refresh()->toArray()]);

        }catch (Exception $exception)
        {
            DB::rollBack();
            return $this->response(false,$this->ErrorMassage($exception->getCode()),[]);
        }
    }
}
