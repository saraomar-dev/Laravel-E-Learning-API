<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
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
            'user_id' => [
            'required',
            'integer',
            'min:1',
            'exists:users,id',
            Rule::unique('enrollments')->where(function ($query) {
                return $query->where('course_id', $this->course_id);
            }),
        ],

        'course_id' => [
            'required',
            'integer',
            'min:1',
            'exists:courses,id',
        ],
        ];
    }
}
