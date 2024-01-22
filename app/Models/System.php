<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class System extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'systems';

    protected $fillable =['client_secret','title','ip','creator_frotel_id','created_at','updated_at'];


    public function categories()
    {
        return $this->belongsToMany(Category::class,'system_category_polls','system_id','category_id');
    }
    public function polls()
    {
        return $this->belongsToMany(Poll::class,'system_category_polls','system_id','poll_id');
    }
    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(240)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }

}
