<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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
        'title'=>'string|required|max:25',
        'order'=>'required|integer|min:0',
        'video'=>'required|mimes:mp4,mov,avi,mkv,webm|max:102400|file',
        'course_id'=>'required|exists:courses,id',
        ];
    }
}
