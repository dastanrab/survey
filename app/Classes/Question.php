<?php

namespace App\Classes;




use Illuminate\Http\Exceptions\HttpResponseException;

class Question
{
    private $question_type;
    private $type;
    private $current_id;
    public $current_question;
  public function __construct(int $type)
  {
      $this->type=$type;
      switch ($type){
          case 1:
              $this->question=new \App\Classes\Question_type\TextQuestion();
              break;
          default:
              throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'نوع سوالی برای ایجاد یافت نشد',[],404)));
              break;
      }
  }
  public function create($title)
  {

      $this->current_question=$this->question->Create($title,$this->type);
      $this->current_id=$this->current_question['status']?$this->current_question['data']['id']:null;
      return $this;
  }
  public function add_to_poll(int $poll_id,int $question_id ,$frotel_id)
  {
      return $this->question->AddToPoll($poll_id,$question_id,$frotel_id);
  }
    public function add_current_to_poll(int $poll_id,$frotel_id)
    {
        return $this->question->AddToPoll($poll_id,$this->current_id,$frotel_id);
    }
}
