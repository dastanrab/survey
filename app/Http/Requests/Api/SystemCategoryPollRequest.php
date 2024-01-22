<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SystemCategoryPollRequest extends FormRequest
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
            'system_id'=>['required','numeric','exists:systems,id'],
            'category_id'=>['required','numeric','exists:categories,id'],
            'poll_id'=>['required','numeric','exists:polls,id',Rule::unique('system_category_polls','poll_id')->where('category_id',$this->input('category_id'))->where('system_id',$this->input('system_id'))],
            'frotel_id'=>['required','numeric'],
            'refrence_id'=>['required','numeric'],
            'start_at'=>['required','date_format:Y-m-d H:i:s'],
            'expire_at'=>['required','date_format:Y-m-d H:i:s'],
            'description'=>'nullable|min:3|max:1000',
            'creator_frotel_id'=>'required'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'خطا در داده های ورودی',$validator->errors()->getMessages()),422));
    }
}
