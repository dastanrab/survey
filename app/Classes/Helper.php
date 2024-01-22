<?php

namespace App\Classes;


use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;


class Helper
{
  public static function response_body(bool $status,string $msg,mixed $error=[],mixed $data=[],int $response_code = 200)
  {
      return response()->json(['status'=>$status,'msg'=>$msg,'error'=>$error,'data'=>$data],$response_code);
  }
  public static function check_poll_start_end($start,$end)
  {
      $start = Carbon::parse($start);
      $end = Carbon::parse($end);
      $now= Carbon::now();
      if (!$start->gt($now) or !$end->gt($start) )
      {
          throw new HttpResponseException(response()->json(self::response_body(false,'اشکال در زمان های وارد شده',[],[])));
      }
  }
  public static function decode_jwt($jwt)
  {
      try {
            $tokenPayload = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key(\Illuminate\Support\Facades\File::get(storage_path('jwt/public.pem')),'RS256'));
            return ['status'=>true,'creator_frotel_id'=>$tokenPayload->sub];
      }catch (\Exception $exception)
      {
          return ['status'=>false];
      }
  }
  public static function delete_file($path)
  {

     if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path))
     {
         \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
     }

  }
  public static function generate_client_key()
  {
      do {
          $client_secret= bin2hex(openssl_random_pseudo_bytes(32));
      } while (\App\Models\System::query()->where("client_secret", "=", $client_secret)->first() instanceof \App\Models\System);
      return $client_secret;
  }
    public static function EncryptData(mixed $data)
    {
        try {
            $privatekey = openssl_pkey_get_private(file_get_contents(storage_path('jwt/private.pem')));
            openssl_private_encrypt( json_encode($data), $encrypted, $privatekey);
            return $encrypted;
        }catch (\Exception $exception)
        {
            return false;
        }



    }
    public static function DecryptData($encrypted)
    {
        try {
            $publickey = openssl_pkey_get_public(file_get_contents(storage_path('jwt/public.pem')));
            openssl_public_decrypt($encrypted, $decrypted, $publickey);
            return json_decode($decrypted);
        }catch (\Exception $exception)
        {
            return false;
        }

    }

    public static function get_question_type_instance($question_type_id)
    {
        switch ($question_type_id){
            case 1:
                return new \App\Classes\Question_type\ScoreQuestion();
            case 2:
                return new \App\Classes\Question_type\TextQuestion();
            case 3:
                return new \App\Classes\Question_type\YesNoQuestion();
            default:
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'نوع سوالی برای ایجاد یافت نشد'),404));
        }
    }
    public static function get_question_answer_instance($question_type_id)
    {
        switch ($question_type_id){
            case 1:
                return new \App\Classes\Question_answer\ScoreAnswer();
            default:
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'نوع جوابی برای ایجاد یافت نشد'),404));
        }
    }
    public static function check_poll_expire($end)
    {
        $end = Carbon::parse($end);
        $now= Carbon::now();
        if ( !$end->gt($now) )
        {
            throw new HttpResponseException(response()->json(self::response_body(false,'مهلت پاسخ دهی به اتمام رسیده است',[],[])));
        }
        return true;
    }


}
