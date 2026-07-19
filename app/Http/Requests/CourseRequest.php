<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
        'course_name'=>'string|required|max:25',
        'descreption'=>'string|required|max:400',
        'price'=>'required|integer|min:0',
        'image'=>'required|mimes:png,jpg,jpeg,gif|max:2048',
        'category_id'=>'required|exists:categories,id',
        ];
    }
}
