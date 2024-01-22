<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $hidden = ['pivot'];
    protected $fillable = ['id','title','description','image','creator_frotel_id','system_id','created_at','updated_at'];

    public function system()
    {
        return $this->belongsTo(System::class,'system_id','id');
    }

}
