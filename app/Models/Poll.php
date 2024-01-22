<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;
    protected $table = 'polls';
    protected $hidden = ['pivot'];
    protected $fillable = ['id','title','description','has_comment','category_id','created_at','updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function questions()
    {
        return $this->belongsToMany(Question::class,'poll_questions','poll_id','question_id');
    }

}
