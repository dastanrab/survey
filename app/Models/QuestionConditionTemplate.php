<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionConditionTemplate extends Model
{
    use HasFactory;
    protected $table='parent_question_answerd_condition_templates';
    protected $fillable =['title','value','question_id','parent_question_answerd_condition_id','created_at','updated_at'];
}
