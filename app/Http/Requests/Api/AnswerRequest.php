<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'poll_id'=>['required','numeric'],
            'system_id'=>['required','numeric'],
            'category_id'=>['required','numeric'],
            'data'=>['required','array'],
            'data.*.question_id'=>['required','numeric','exists:questions,id'],
            'data.*.value'=>['required','numeric'],
            'data.*.details'=>['required','array'],
            'data.*.details.*.value'=>['nullable','numeric'],
            'data.*.details.*.title'=>['nullable','string'],
            'frotel_id'=>['nullable','numeric'],
            'refrence_id'=>['nullable','numeric'],
            'voter_frotel_id'=>['required','numeric']
//            'start_at'=>['required','date_format:Y-m-d H:i:s'],
//            'expire_at'=>['required','date_format:Y-m-d H:i:s'],
//            'description'=>'nullable|min:3|max:1000',
//            'creator_frotel_id'=>'required'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'خطا در داده های ورودی',$validator->errors()->getMessages()),422));
    }
}
