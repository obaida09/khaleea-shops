<?php

namespace App\Http\Requests\ShopSide;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
                Rule::unique('shops')->ignore($this->route('shop')),
            ],
            'mobile' => [
                'sometimes',
                'required',
                'string',
                'numaric',
                'max:255',
                Rule::unique('shops')->ignore($this->route('shop')),
            ],
            'password' => 'sometimes|string|min:8',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'season' => 'sometimes|in:winter,summer,all',
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
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصيًا.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفًا.',
            'mobile.required' => 'حقل رقم الجوال مطلوب.',
            'mobile.string' => 'يجب أن يكون رقم الجوال نصيًا.',
            'mobile.max' => 'يجب ألا يزيد رقم الجوال عن 255 حرفًا.',
            'mobile.unique' => 'رقم الجوال مسجل بالفعل.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.string' => 'يجب أن يكون البريد الإلكتروني نصيًا.',
            'email.email' => 'يجب أن يكون البريد الإلكتروني صالحًا.',
            'email.max' => 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
            'email.unique' => 'البريد الإلكتروني مسجل بالفعل.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string' => 'يجب أن تكون كلمة المرور نصية.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
        ];
    }
}
