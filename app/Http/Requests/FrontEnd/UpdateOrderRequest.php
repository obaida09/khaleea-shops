<?php

namespace App\Http\Requests\FrontEnd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return auth()->user()->can('update orders');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|string',
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
            'products.array' => 'يجب أن يكون حقل المنتجات مصفوفة.',
            'products.*.id.uuid' => 'يجب أن يكون معرف المنتج UUID صحيح.',
            'products.*.id.exists' => 'المنتج المحدد غير موجود.',
            'products.*.quantity.integer' => 'يجب أن تكون الكمية عددًا صحيحًا.',
            'products.*.quantity.min' => 'يجب أن تكون الكمية واحدًا على الأقل.',
            'coupon_code.string' => 'يجب أن يكون رمز القسيمة نصيًا.',
            'coupon_code.exists' => 'رمز القسيمة غير موجود.',
        ];
    }
}
