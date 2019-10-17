<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
      'email' => 'required|email|unique:admins',
      'firstname' => 'required|min:2',
      'lastname' => 'required|min:2'
    ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
      'email' => 'Please enter a valid email',
      'firstname' => 'Please enter a valid first name',
      'lastname' => 'Please enter a valid last name'
    ];
    }

    /**
     * Return validation errors in json format
     *
     * @return void
     * @phan-file-suppress PhanTypeMismatchArgument
     */
    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json([
      'success' => false,
      'errors' => $errors
    ], 422));
    }
}
