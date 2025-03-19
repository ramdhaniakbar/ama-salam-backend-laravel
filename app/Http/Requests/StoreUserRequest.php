<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'phone' => 'required|string|min:10|unique:users,phone',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'phone.required' => 'The phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.unique' => 'This phone number is already registered.',
            'photo.image' => 'The photo must be an image file.',
            'photo.mimes' => 'Only JPEG, PNG, JPG, and GIF files are allowed.',
            'photo.max' => 'The photo size must not exceed 2MB.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'statusCode' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY, // 422
                'message' => 'Validation failed',
                'errors' => $errors,
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
