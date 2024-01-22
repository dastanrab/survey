<?php

namespace App\Http\Middleware;

use App\Classes\Helper;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParseJwt
{

    public function handle(Request $request, Closure $next)
    {
        $token=Helper::decode_jwt($request->bearerToken());
        if (!$token['status'])
        {
            return \response()->json(Helper::response_body(false,'دسترسی احراز نشد'),401);
        }
        $request->merge(['creator_frotel_id'=>$token['creator_frotel_id']]);
        return $next($request);
    }
}
