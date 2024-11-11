<?php

namespace App\Http\Requests\ShopSide;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'season' => 'required|in:winter,summer,all',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric',
            'category_id' => 'required|uuid|exists:categories,id',
            'status' => 'required|numeric',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'name.unique' => 'اسم المنتج مستخدم بالفعل.',
            'description.string' => 'يجب أن يكون الوصف نصيًا.',
            'season.required' => 'حقل الموسم مطلوب.',
            'season.in' => 'يجب أن يكون الموسم "شتاء"، "صيف"، أو "كل الفصول".',
            'price.required' => 'حقل السعر مطلوب.',
            'price.numeric' => 'يجب أن يكون السعر رقمًا.',
            'price.min' => 'يجب أن يكون السعر قيمة موجبة.',
            'quantity.required' => 'حقل الكمية مطلوب.',
            'quantity.numeric' => 'يجب أن تكون الكمية رقمًا.',
            'category_id.required' => 'حقل الفئة مطلوب.',
            'category_id.uuid' => 'يجب أن يكون معرف الفئة UUID صحيحًا.',
            'category_id.exists' => 'الفئة المحددة غير موجودة.',
            'status.required' => 'حقل الحالة مطلوب.',
            'status.numeric' => 'يجب أن تكون الحالة رقمًا.',
            'images.required' => 'حقل الصورة مطلوب.',
            'images.image' => 'يجب أن تكون الصورة بتنسيق صورة.',
            'images.mimes' => 'يجب أن تكون الصورة بامتداد: jpeg، png، jpg، gif.',
            'images.max' => 'يجب ألا يزيد حجم الصورة عن 2048 كيلوبايت.',
        ];
    }
}
