<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PollQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'question_id'=>'required|numeric|exists:questions,id',
            'creator_frotel_id'=>'required|numeric',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'خطا در داده های ورودی',$validator->errors()->getMessages()),422));
    }
}
