<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CourseRecource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //auth
    public function index()
    {
        $category=Category::paginate(5);

    return  CategoryResource::collection($category);
    }

    /**
     * Store a newly created resource in storage.
     */
    //admin
    public function store(CategoryRequest $request)
    {
    $category=Category::Create(['name'=>$request->name,]);
    return response()->json(['message'=>'created successfully',
                            'category'=>new CategoryResource($category)], 201);
    }

    /**
     * Display the specified resource.
     */
    //auth
    public function show(Category $category)
    {
    return response()->json(['user'=>new CategoryResource($category)], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    //admin
    public function destroy(Category $category)
    {
    $category->delete();
        return response()->json([
                            'message'=>'category deleted successfully'], 201);
    }
    //auth
    public function show_coursesOfCategory(Category $category)
    {
        $courses=$category->courses()->paginate(5);
        return CourseRecource::collection($courses);
    }
}
