<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;
    protected $table='score';
    protected $fillable=['frotel_id','refrence_id','system_id','score','count','created_at','updated_at'];
}
