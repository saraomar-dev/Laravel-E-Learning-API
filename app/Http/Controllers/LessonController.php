<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Notifications\AddLessonNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    //instructor
    public function store(LessonRequest $request)
    {
        $course_id=$request->course_id;
        $course=Course::Where('id',$course_id)->first();
        if(auth()->id()!==$course->user_id){
        return response()->json(['message'=>'You are not allowed to add lessons to this course.'], 403);
        }
        $data = $request->validated();
        $data['video'] = null;
        if($request->hasFile('video')){
        $data['video']=$request->file('video')->store('videos','public');
        }
        $lesson=Lesson::Create(['title'=>$data['title'],
                                'order'=>$data['order'],
                                'video'=>$data['video'],
                                'course_id'=>$data['course_id'],
        ]);
        $students=$course->students()->get();
        Notification::send($students, new AddLessonNotification($course));
        $lesson->load('course');
        return response()->json(['message'=>'lesson was added successfully',
                                'lesson'=>new LessonResource($lesson)], 201);
    }

    /**
     * Display the specified resource.
     */
    //auth
    public function show(Lesson $lesson)
    {
        $course=$lesson->course;
        $enrolled = Enrollment::where('user_id', auth()->id())
    ->where('course_id', $course->id)
    ->exists();

        if (! $enrolled&&auth()->user()->role==='student') {
                abort(403, 'You are not enrolled in this course.');
            }
            if (auth()->user()->role==='instructor'&&$course->user_id !== auth()->id()) {
                abort(403, 'You are not enrolled in this course.');
            }
        $lesson->load('course');
        return response()->json(['lesson'=>new LessonResource($lesson)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    //instructor
    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $course=$lesson->course;
        if(auth()->id()!==$course->user_id){
        return response()->json(['message'=>'You are not allowed to update this lesson'], 403);
        }
        $data=$request->validated();
        if($request->hasFile('video')){
            if ($lesson->video) {
            Storage::disk('public')->delete($lesson->video);
            }
        $data['video']=$request->file('video')->store('videos','public');
        }
        $lesson->update($data);
        $lesson->load('course');
        return response()->json(['message'=>'lesson updated successfully','lesson'=>new LessonResource($lesson)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    //instructor
    public function destroy(Lesson $lesson)
    {
        $course=$lesson->course;
        if(auth()->id()!==$course->user_id){
        return response()->json(['message'=>'You are not allowed to delete this lesson'], 403);
        }
        if ($lesson->video) {
    Storage::disk('public')->delete($lesson->video);
    }
        $lesson->delete();
        return response()->json([
                            'message'=>'lesson deleted successfully'], 200);

    }
    //auth
    public function show_lessons_of_course(Course $course)
    {
        $enrolled = Enrollment::where('user_id', auth()->id())
    ->where('course_id', $course->id)
    ->exists();

        if (! $enrolled&&auth()->user()->role==='student') {
                abort(403, 'You are not enrolled in this course.');
            }
            if (auth()->user()->role==='instructor'&&$course->user_id !== auth()->id()) {
                abort(403, 'You are not allowed to see this course.');
            }
        $lessons=$course->lessons()->with('course')->paginate(5);
        return  LessonResource::collection($lessons);
    }

}
