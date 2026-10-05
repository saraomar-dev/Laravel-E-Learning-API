<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\UpdateMyCourse;
use App\Http\Resources\CourseRecource;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Notifications\AprroveCourseNotification;
use App\Notifications\NewCourseNotification;
use App\Notifications\RejectCourseNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification as FacadesNotification;

use function Pest\Laravel\delete;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //admin
    public function index()
    {
        $courses=Course::paginate(5);
        return  CourseRecource::collection($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    //instructor
    public function store(CourseRequest $request)
    {
    if($request->hasFile('image')){
        $path=$request->file('image')->store('courses','public');
        }
        $course=Course::Create(['course_name'=>$request->course_name,
                                'descreption'=>$request->descreption,
                                'image'=>$path?$path:null,
                                'price'=>$request->price,
                                'category_id'=>$request->category_id,
                                'status'=>'pending',
                                'user_id'=>auth()->id(),
        ]);
        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new NewCourseNotification($course));
        return response()->json([
        'message' => 'Course created successfully and waiting for approval.',
        'course'=> new CourseRecource($course),
    ]);
    }

    /**
     * Display the specified resource.
     */
    //auth
    public function show(Course $course)
    {
        if(auth()->user()->role==='student'&&$course->status!=='active'){
    return response()->json(['message'=>'this course is not avilabale'], 403);
    }
    return response()->json([
        'course'=> new CourseRecource($course),
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    //instructor
    public function update(UpdateMyCourse $request, Course $course)
    {
        if(auth()->id()!==$course->user_id){
        return response()->json(['message'=>'You are not allowed to update this course.'], 403);
        }
        $data = $request->validated();
    if($request->hasFile('image')){
            if ($course->image) {
            Storage::disk('public')->delete($course->image);
            }
        $data['image']=$request->file('image')->store('courses','public');
        }
        $course->update($data);
        return response()->json(['course'=>new CourseRecource($course),'message'=>'course updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    //auth
    public function destroy(Course $course)
    {
        if(auth()->user()->role==='instructor' && $course->user_id !== auth()->id()){
    return response()->json(['message'=>'Unauthenticated'], 403);
    }
    if(auth()->user()->role==='student'){
    return response()->json(['message'=>'Unauthenticated'], 403);
    }
    if ($course->image) {
    Storage::disk('public')->delete($course->image);
}
    $course->delete();
        return response()->json([
                            'message'=>'course deleted successfully'], 201);
    }

    //instructor
    public function my_courses()
    {
    $courses = auth()->user()->courses()->paginate(5);
    return  CourseRecource::collection($courses);
    }

    //auth
    public function instructor_courses(User $user)
    {
        if(auth()->user()->role==='instructor' && auth()->id() !== auth()->id()){
    return response()->json(['message'=>'Unauthenticated'], 403);
    }
        $courses=$user->courses()->paginate(5);
        return  CourseRecource::collection($courses);
    }

    //admin
    public function approve_course(Course $course)
    {
        if($course->status==='active'){
        return response()->json(['message'=>'course was approved befor',
                            'course'=>new CourseRecource($course),], 200);
        }
    $user=$course->user;
    $course->update(['status'=>'active']);
    Notification::send($user, new AprroveCourseNotification($course));
    return response()->json(['message'=>'course was approved succussefully',
                            'course'=>new CourseRecource($course),], 200);
    }

    //admin
    public function reject_course(Course $course)
    {
        if($course->status!=='pending'){
        return response()->json(['message'=>'course was approved befor',
                            'course'=>new CourseRecource($course),], 200);
        }
    $user=$course->user;
    Notification::send($user, new RejectCourseNotification($course));
    $course->delete();
    return response()->json(['message'=>'course was rejected and deleted succussefully',
                            'course'=>new CourseRecource($course),], 200);
    }
    //auth
    public function show_active_courses()
    {
    $courses=Course::Where('status','active')->paginate(5);
    return  CourseRecource::collection($courses);
    }
    //admin
    public function show_pending_courses()
    {
    $courses=Course::Where('status','pending')->paginate(5);
    return  CourseRecource::collection($courses);
    }
    }


