<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = ['id','title','question_type_id','question_parent_id','created_at','updated_at'];
    protected $hidden=['pivot'];

    public function question_type()
    {
        return $this->belongsTo(QuestionType::class,'question_type_id','id');
    }
    public function question_conditions()
    {
        return $this->hasMany(QuestionCondition::class,'question_id','id');
    }
    public function question_templates()
    {
        return $this->hasMany(QuestionConditionTemplate::class,'question_id','id');
    }
}
