<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreOrderRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*.id' => 'required|uuid|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->products as $productData) {
                $product = Product::find($productData['id']);

                if (!isset($product->quantity)) {
                    $validator->errors()->add('products', ' The product has insufficient stock. You can’t buy more than 0 units.');
                } elseif ($product->quantity < $productData['quantity']) {
                    $validator->errors()->add('products', ' The product "' . $product->name . '" has insufficient stock. You can’t buy more than ' . $product->quantity . ' units.');
                }
            }
        });
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
            'products.required' => 'حقل المنتجات مطلوب.',
            'products.array' => 'يجب أن يكون حقل المنتجات مصفوفة.',
            'products.*.id.required' => 'معرف المنتج مطلوب.',
            'products.*.id.uuid' => 'يجب أن يكون معرف المنتج UUID صحيح.',
            'products.*.id.exists' => 'المنتج المحدد غير موجود.',
            'products.*.quantity.required' => 'حقل الكمية للمنتج مطلوب.',
            'products.*.quantity.integer' => 'يجب أن تكون الكمية عددًا صحيحًا.',
            'products.*.quantity.min' => 'يجب أن تكون الكمية واحدًا على الأقل.',
            'coupon_code.string' => 'يجب أن يكون رمز القسيمة نصيًا.',
            'coupon_code.exists' => 'رمز القسيمة غير موجود.',
        ];
    }
}
