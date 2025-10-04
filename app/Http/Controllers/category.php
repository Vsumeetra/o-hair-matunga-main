<?php

namespace App\Http\Controllers;

use App\Models\CategoryModal;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = CategoryModal::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'app_icon' => 'nullable|string',
        ]);

        $category = CategoryModal::create($request->all());

        return response()->json($category, 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $category = CategoryModal::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = CategoryModal::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->update($request->all());

        return response()->json($category);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = CategoryModal::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
