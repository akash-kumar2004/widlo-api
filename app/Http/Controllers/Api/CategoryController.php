<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::select('id', 'category_name')->get();

        return response()->json([
            'status' => true,
            'data' => $categories
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',

        ]);
        $category = Category::create([
            'category_name' => ucfirst($request->category_name),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Category added successfully.',
            'data' => $category
        ], 201);
    }
}
