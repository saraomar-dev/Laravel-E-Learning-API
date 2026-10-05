<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*auth routes*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


/*user mangment routes*/
Route::apiResource('users', UserController::class)->middleware(['auth:sanctum', 'isAdmin']);


/*profile routes*/
Route::apiResource('profiles', ProfileController::class)->middleware('auth:sanctum');
Route::get('/showProfile', [ProfileController::class, 'showProfile'])->middleware('auth:sanctum');
Route::post('addImage', [ProfileController::class, 'addImage'])->middleware('auth:sanctum');
Route::post('addPhone', [ProfileController::class, 'addphone'])->middleware('auth:sanctum');
Route::patch('/editProfile', [ProfileController::class, 'editProfile'])->middleware('auth:sanctum');

/*course routes*/
Route::apiResource('courses', CourseController::class)->only(['store', 'update'])->middleware(['auth:sanctum', 'isInstructor']);
Route::apiResource('courses', CourseController::class)->only(['index'])->middleware(['auth:sanctum', 'isAdmin']);
Route::apiResource('courses', CourseController::class)->only(['show', 'destroy'])->middleware('auth:sanctum');
Route::get('my-courses', [CourseController::class, 'my_courses'])->middleware(['auth:sanctum', 'isInstructor']);
Route::get('instructor_courses/{user}/courses', [CourseController::class, 'instructor_courses'])->middleware('auth:sanctum');
Route::get('approve_course/{course}', [CourseController::class, 'approve_course'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('reject_course/{course}', [CourseController::class, 'reject_course'])->middleware(['auth:sanctum', 'isAdmin']);
Route::get('/active_courses', [CourseController::class, 'show_active_courses'])->middleware('auth:sanctum');
Route::get('/pending_courses', [CourseController::class, 'show_pending_courses'])->middleware(['auth:sanctum', 'isAdmin']);


/*category routes*/
Route::get('category/{category}/courses', [CategoryController::class, 'show_coursesOfCategory'])->middleware('auth:sanctum');
Route::apiResource('categories', CategoryController::class)->only(['store', 'destroy', 'update'])->middleware(['auth:sanctum', 'isAdmin']);
Route::apiResource('categories', CategoryController::class)->only(['show', 'index'])->middleware('auth:sanctum');

/*notification routes*/
Route::get('/all_notification', [NotificationController::class, 'all_notifications'])->middleware('auth:sanctum');
Route::get('/unread_notifications', [NotificationController::class, 'unread_notifications'])->middleware('auth:sanctum');
Route::patch('{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth:sanctum');
Route::patch('readAll', [NotificationController::class, 'markAllAsRead'])->middleware('auth:sanctum');


/*lesson routes*/
Route::apiResource('lessons', LessonController::class)->only(['destroy', 'store', 'update'])->middleware(['auth:sanctum', 'isInstructor']);
Route::apiResource('lessons', LessonController::class)->only(['show'])->middleware('auth:sanctum');
Route::get('courses/{course}/lesson', [LessonController::class, 'show_lessons_of_course'])->middleware('auth:sanctum');


/*enrollment routes*/
Route::apiResource('enrollments', EnrollmentController::class)->only(['store'])->middleware(['auth:sanctum', 'isStudent']);
Route::get('enrollments/my-courses', [EnrollmentController::class, 'show_my_enrolled_courses'])->middleware(['auth:sanctum', 'isStudent']);
Route::get('enrollments/{course}/students', [EnrollmentController::class, 'show_students_in_course'])->middleware(['auth:sanctum', 'isInstructor']);
Route::apiResource('enrollments', EnrollmentController::class)->only(['destroy', 'show'])->middleware('auth:sanctum');
Route::apiResource('enrollments', EnrollmentController::class)->only(['index'])->middleware(['auth:sanctum', 'isAdmin']);
