<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StorePostRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'product_id' => 'nullable|uuid|exists:products,id',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
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
            'title.required' => 'حقل العنوان مطلوب.',
            'title.string' => 'يجب أن يكون العنوان نصيًا.',
            'title.max' => 'يجب ألا يزيد العنوان عن 255 حرفًا.',
            'body.required' => 'حقل المحتوى مطلوب.',
            'body.string' => 'يجب أن يكون المحتوى نصيًا.',
            'product_id.uuid' => 'يجب أن يكون معرف المنتج UUID صحيح.',
            'product_id.exists' => 'المنتج المحدد غير موجود.',
            'images.required' => 'حقل الصورة مطلوب.',
            'images.image' => 'يجب أن تكون الصورة بتنسيق صورة.',
            'images.mimes' => 'يجب أن تكون الصورة بامتداد: jpeg، png، jpg، gif.',
            'images.max' => 'يجب ألا يزيد حجم الصورة عن 2048 كيلوبايت.',
        ];
    }
}
