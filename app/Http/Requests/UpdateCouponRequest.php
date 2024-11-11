<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|unique:coupons,code,' . $this->coupon->id,
            'discount' => 'sometimes|numeric',
            'discount_type' => 'sometimes|in:fixed,percentage',
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
            'code.string' => 'يجب أن يكون الكود نصيًا.',
            'code.unique' => 'الكود مستخدم من قبل.',
            'discount.numeric' => 'يجب أن يكون الخصم رقمًا.',
            'discount_type.in' => 'نوع الخصم يجب أن يكون ثابت أو نسبة مئوية.',
            'usage_limit.integer' => 'يجب أن يكون الحد الأقصى للاستخدام رقمًا صحيحًا.',
            'valid_from.date' => 'يجب أن يكون تاريخ البداية تاريخًا صالحًا.',
            'valid_until.date' => 'يجب أن يكون تاريخ الانتهاء تاريخًا صالحًا.',
            'valid_until.after_or_equal' => 'يجب أن يكون تاريخ الانتهاء مساويًا أو بعد تاريخ البداية.',
        ];
    }
}
