<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    public mixed $user;
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $updateRules = match($this->method()){
            'PUT', 'PATCH' => '|sometimes',
            default => ''
        };

        return [
            'first_name' => 'required|string|max:255'. $updateRules,
            'last_name' => 'required|string|max:255'. $updateRules,
            'is_admin' => 'nullable|boolean',
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,'.$this->user. $updateRules,
            'password' => 'required|string|min:6'. $updateRules,
            'avatar' => 'nullable|string',
            'address' => 'required|string|max:255'. $updateRules,
            'phone_number' => 'required|string|max:255'. $updateRules,
            'is_marketing' => 'nullable|boolean',
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        throw new HttpResponseException(response()->json([
            'error' => $validator->errors()->first()
        ], 422));
    }

    protected function failedAuthorization(): HttpResponseException
    {
        throw new HttpResponseException(response()->json([
            'error' => 'Unauthorized'
        ], 422));
    }
}
