<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function get_token()
    {
        $user=\Illuminate\Support\Facades\Auth::loginUsingId(6);
        $user=auth()->user();
        $token=$user->createToken('test')->plainTextToken;
    }
}
