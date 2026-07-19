<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'lesson id'=>$this->id,
        'title'=>$this->title,
        'order'=>$this->order,
        'video'=>$this->video
    ? asset('storage/' . $this->video)
    : null,
        'course id'=>$this->course_id,
        'course_name' => $this->course->course_name,
        ];
    }
}
