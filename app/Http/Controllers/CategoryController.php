<?php

namespace App\Http\Controllers;

use App\Models\CategoryModal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $limit = $request->get("limit", 10); // Default limit 10
        $page = $request->get("page", 1); // Default page 1
        $search = $request->get("search");
    
        // Start query
        $query = CategoryModal::with('MainCategory');

    
        // Apply search filter if provided
        if ($search) {
            $query->where("name", "LIKE", "%$search%");
        }
    
        // Get total count before applying pagination
        $total = $query->count();
    
        // Apply pagination
        $result = $query->skip(($page - 1) * $limit)->take($limit)->orderBy('created_at', 'desc')->get();
    
        return response()->json([
            'success' => true,
            'data' => $result,
            'pagination' => [
                'total' => $total,
                'limit' => (int)$limit,
                'page' => (int)$page
            ],
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string',
                'app_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'main_category_id' => 'nullable|integer', // Validating main_category_id as integer
            ]);

            $imagePath = $request->file('image') ? $request->file('image')->store('images', 'public') : null;
            $appIconPath = $request->file('app_icon') ? $request->file('app_icon')->store('images', 'public') : null;

            $category = CategoryModal::create([
                'name' => $request->name,
                'image' => $imagePath,
                'app_icon' => $appIconPath,
                'description' => $request->description,
                'main_category_id' => $request->main_category_id, // Storing main_category_id
            ]);

            return response()->json($category, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image ? asset('storage/' . $category->image) : null,
            'app_icon' => $category->app_icon ? asset('storage/' . $category->app_icon) : null,
            'description' => $category->description,
            'main_category_id' => $category->main_category_id, // Added main_category_id
        ]);
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

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'app_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_category_id' => 'required|integer', // Validating main_category_id
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($category->image);
            $category->image = $request->file('image')->store('categories', 'public');
        }

        if ($request->hasFile('app_icon')) {
            Storage::disk('public')->delete($category->app_icon);
            $category->app_icon = $request->file('app_icon')->store('icons', 'public');
        }

        $category->update($request->except(['image', 'app_icon']));

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image ? asset('storage/' . $category->image) : null,
            'app_icon' => $category->app_icon ? asset('storage/' . $category->app_icon) : null,
            'description' => $category->description,
            'main_category_id' => $category->main_category_id, // Added main_category_id
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        CategoryModal::find($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
