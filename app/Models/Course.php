<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_name',
        'descreption',
        'price',
        'image',
        'status',
        'user_id',
        'category_id',

    ];

public function user()
{
    return $this->belongsTo(User::class);
}
public function category()
{
    return $this->belongsTo(Category::class);
}
public function lessons()
{
    return $this->hasMany(Lesson::class);
}
public function students()
{
    return $this->belongsToMany(User::class,'enrollments');
}
}
