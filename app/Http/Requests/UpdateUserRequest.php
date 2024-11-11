<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'mobile' => [
                'sometimes',
                'required',
                'string',
                'numaric',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'password' => 'sometimes|required|string|min:8|confirmed',
            'location' => 'nullable',
            'gps' => 'nullable',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

    public function messages()
    {
        return [
            'name.string' => 'يجب أن يكون الاسم نصيًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'mobile.string' => 'يجب أن يكون رقم الجوال نصيًا.',
            'mobile.max' => 'يجب ألا يزيد رقم الجوال عن 255 حرفًا.',
            'mobile.unique' => 'رقم الجوال مسجل بالفعل.',
            'email.string' => 'يجب أن يكون البريد الإلكتروني نصيًا.',
            'email.email' => 'يجب أن يكون البريد الإلكتروني صالحًا.',
            'email.max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
            'email.unique' => 'البريد الإلكتروني مسجل بالفعل.',
            'password.string' => 'يجب أن تكون كلمة المرور نصية.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
        ];
    }
}
