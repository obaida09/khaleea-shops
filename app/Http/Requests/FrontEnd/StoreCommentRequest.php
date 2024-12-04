<?php

namespace App\Http\Requests\FrontEnd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreCommentRequest extends FormRequest
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
            'content' => 'required|string',
            'post_id' => 'required|uuid|exists:posts,id',
            'parent_id' => 'nullable|uuid|exists:comments,id',
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
            'content.required' => 'حقل المحتوى مطلوب.',
            'content.string' => 'يجب أن يكون المحتوى نصيًا.',
            'post_id.required' => 'حقل معرف المنشور مطلوب.',
            'post_id.uuid' => 'يجب أن يكون معرف المنشور UUID صحيح.',
            'post_id.exists' => 'المعرف المقدم للمنشور غير موجود.',
            'parent_id.uuid' => 'يجب أن يكون معرف التعليق UUID صحيح.',
            'parent_id.exists' => 'المعرف المقدم للتعليق غير موجود.',
        ];
    }
}
