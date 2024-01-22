<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionUserAnswer extends Model
{
    use HasFactory;
    protected $table='question_users_answers';
    protected $fillable=['id','voter_frotel_id','question_id','condition_question_id','value_answer','text_answer','created_at','updated_at'];
}
