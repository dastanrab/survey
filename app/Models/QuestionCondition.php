<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCondition extends Model
{
    use HasFactory;
    protected $table='parent_question_answerd_conditions';
    protected $fillable=['start_range','end_range','question_id','creator_frotel_id','created_at','updated_at'];
    public function question_condition_templates()
    {
        return $this->hasMany(QuestionConditionTemplate::class,'parent_question_answerd_condition_id','id');
    }

}
