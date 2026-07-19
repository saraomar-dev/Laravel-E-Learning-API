<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentRequest;
use App\Http\Resources\CourseRecource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments=Enrollment::paginate(5);
        return  EnrollmentResource::collection($enrollments);
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
    public function store(EnrollmentRequest $request)
    {

        $data=$request->validated();
        $course = Course::findOrFail($data['course_id']);
        if ($course->status !=='active') {
        return response()->json(['message'=>'this course is not active'], 403);
        }
        $data['user_id'] = auth()->id();
        $enrollment=Enrollment::Create($data);
        return response()->json(['message'=>'enrollment has been successfully',
                                'enrollment'=>new EnrollmentResource($enrollment)], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {

        $course = $enrollment->course;
        //instructor of the course or student of he enrollment
        if($enrollment->user_id!==auth()->id()&&auth()->id()!==$course->user_id&&auth()->user()->role!=='admin'){
        return response()->json(['message'=>'You are not allowed to show this enrollment'], 403);
        }
        return response()->json(['enrollment'=>new EnrollmentResource($enrollment)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy(Enrollment $enrollment)
    {
        $course = $enrollment->course;
        //instructor of the course or student of he enrollment
        if($enrollment->user_id!==auth()->id()&&auth()->id()!==$course->user_id){
        return response()->json(['message'=>'You are not allowed to delete this enrollment'], 403);
        }
        $enrollment->delete();
        return response()->json([
                            'message'=>'enrollment deleted successfully'], 200);
    }



    public function show_my_enrolled_courses(){
    $courses=auth()->user()->enrolledCourses()->paginate(10);
    return  CourseRecource::collection($courses);
    }



    public function show_students_in_course(Course $course){
    if(auth()->id()!==$course->user_id){
    return response()->json(['message'=>'You are not allowed to see this info'], 403);
    }
    $students=$course->students()->paginate(10);
    return  UserResource::collection($students);
    }
}
