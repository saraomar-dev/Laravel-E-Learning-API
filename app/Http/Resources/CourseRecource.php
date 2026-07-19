<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseRecource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'course_id'=>$this->id,
        'course_name'=>$this->course_name,
        'descreption'=>$this->descreption,
        'price'=>$this->price,
        'image'=>$this->image
    ? asset('storage/' . $this->image)
    : null,
        'status'=>$this->status,
        'category_id'=>$this->category_id,
        'user_id'=>$this->user_id,
        ];
    }
}
