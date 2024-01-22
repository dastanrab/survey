<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollQuestion extends Model
{
    use HasFactory;
    protected $table = 'poll_questions';
    protected $fillable = ['id','poll_id','question_id','creator_frotel_id','created_at','updated_at'];
    public function poll()
    {
        return $this->belongsTo(Poll::class,'poll_id','id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
