<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    use HasFactory;
    protected $table='question_types';
    protected $fillable=['id','title','created_at','updated_at'];
}
