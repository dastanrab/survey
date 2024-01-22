<?php

namespace App\Classes\Question_type;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
interface QuestionTypeInterface
{
    /**
     * @param mixed $title
     * @param mixed $value
     * @return array
     */
    public function Create(string $title,int $type,int $parent_id=null):array;

    /**
     * @return mixed
     */
    public function CanCondition();


    /**
     * @param int $question_id
     * @param string $title
     * @param int $value
     * @param int|null $parent_id
     * @return bool|\HttpResponseException
     */
    public function CreateAnswerTemplate( string $title, int $value, int $parent_id = null):array|\HttpResponseException;

    /**
     * @param int $poll_id
     * @param int $question_id
     * @param int $frotel_id
     * @return array
     */
    public function AddToPoll(int $poll_id, int $frotel_id):array;

    public function LoadQuestion(Model $model);
    public function CanPoll():bool|\HttpResponseException;
    public function ValidateQuestionDetails(array $details):bool|\HttpResponseException;
    public function Update(string $title,int $parent_id=null);


}
