<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemCategoryPoll extends Model
{
    use HasFactory;
    protected $table ='system_category_polls';
    protected $fillable=['id','system_id','poll_id','category_id','frotel_id','refrence_id','start_at','expire_at','description','creator_frotel_id','created_at','updated_at'];
    public function system()
    {return $this->belongsTo(System::class,'system_id','id');}
    public function category()
    {return $this->belongsTo(Category::class,'category_id','id');}
    public function poll()
    {return $this->belongsTo(Poll::class,'poll_id','id');}
}
