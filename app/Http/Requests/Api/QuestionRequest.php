<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'title'=>'required|max:80',
            'details'=>'nullable|array',
            'question_type_id'=>'required|numeric|exists:question_types,id',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'خطا در داده های ورودی',$validator->errors()->getMessages()),422));
    }
}
