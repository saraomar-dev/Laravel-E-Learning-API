<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMyCourse extends FormRequest
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
        'course_name'=>'string|sometimes|max:25',
        'descreption'=>'string|sometimes|max:400',
        'price'=>'sometimes|integer|min:0',
        'image'=>'sometimes|mimes:png,jpg,jpeg,gif|max:2048',
        'category_id'=>'sometimes|exists:categories,id',
        ];
    }
}
