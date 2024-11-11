<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreCouponRequest extends FormRequest
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
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric',
            'discount_type' => 'required|in:fixed,percentage',
            'usage_limit' => 'nullable|integer',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
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
            'code.required' => 'حقل الكود مطلوب.',
            'code.string' => 'يجب أن يكون الكود نصيًا.',
            'code.unique' => 'الكود مستخدم من قبل.',
            'discount.required' => 'حقل الخصم مطلوب.',
            'discount.numeric' => 'يجب أن يكون الخصم رقمًا.',
            'discount_type.required' => 'نوع الخصم مطلوب.',
            'discount_type.in' => 'نوع الخصم يجب أن يكون ثابت أو نسبة مئوية.',
            'usage_limit.integer' => 'يجب أن يكون الحد الأقصى للاستخدام رقمًا صحيحًا.',
            'valid_from.date' => 'يجب أن يكون تاريخ البداية تاريخًا صالحًا.',
            'valid_until.date' => 'يجب أن يكون تاريخ الانتهاء تاريخًا صالحًا.',
            'valid_until.after_or_equal' => 'يجب أن يكون تاريخ الانتهاء مساويًا أو بعد تاريخ البداية.',
        ];
    }
}
