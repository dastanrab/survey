<?php

namespace App\Classes\Question_answer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

interface Question_Answer
{
   public function Answer($value,$voter_frotel_id,$system_id,$details=null);
   public function HaveCondition():bool|HttpResponseException;
   public function LoadQuestion(Model $model);
}
