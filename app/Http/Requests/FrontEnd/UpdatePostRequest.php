<?php

namespace App\Http\Requests\FrontEnd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UpdatePostRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
            'user_id' => 'sometimes|uuid|exists:users,id',
            'product_id' => 'sometimes|uuid|exists:products,id',
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
            'title.string' => 'يجب أن يكون العنوان نصيًا.',
            'title.max' => 'يجب ألا يزيد العنوان عن 255 حرفًا.',
            'body.string' => 'يجب أن يكون المحتوى نصيًا.',
            'product_id.uuid' => 'يجب أن يكون معرف المنتج UUID صحيح.',
            'product_id.exists' => 'المنتج المحدد غير موجود.',
        ];
    }
}
