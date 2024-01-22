<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PollRequest extends FormRequest
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
        return[
            'title'=>'required|max:80',
            'description'=>'nullable|min:3|max:1000'];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'خطا در داده های ورودی',$validator->errors()->getMessages()),422));
    }
}
