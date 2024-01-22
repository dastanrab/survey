<?php

namespace App\Trait;

trait HandleModelError
{
 public function ErrorMassage($code){
    switch ($code)
    {
        case "23000":
            return "شناسه مرتبط ارسالی نا معتبر است";
        default:
            return "مشکل در سمت پایگاه داده";
    }
 }
}
