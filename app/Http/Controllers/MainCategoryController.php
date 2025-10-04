<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainCategory;
use Illuminate\Support\Facades\Storage;

class MainCategoryController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get("limit", 10); // Default limit 10
        $page = $request->get("page", 1); // Default page 1
        $search = $request->get("search");
    
        // Start query
        $query = MainCategory::query();
    
        // Apply search filter if provided
        if ($search) {
            $query->where("name", "LIKE", "%$search%");
        }
    
        // Get total count before applying pagination
        $total = $query->count();
    
        // Apply pagination
        $result = $query->orderByRaw("
        FIELD(name, 'Women', 'Men') 
        DESC, created_at DESC
    ")
    ->skip(($page - 1) * $limit)
    ->take($limit)
    ->get();
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
    

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
            ]);
    
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }
    
            $mainCategory = MainCategory::create([
                'name' => $request->name,
                'image' => $imagePath,
                'description' => $request->description,
            ]);
    
            return response()->json(['message' => 'Category created successfully', 'data' => $mainCategory], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function show()
    {
    
        // Start query
        $query = MainCategory::with('subCategories');
    
        // Apply pagination
        $result = $query->orderByRaw("
        FIELD(name, 'Women', 'Men') 
        DESC, created_at DESC
    ")
    ->get();
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function update(Request $request, $category_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        $mainCategory = MainCategory::find($category_id);

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($mainCategory->image) {
                Storage::disk('public')->delete($mainCategory->image);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('images', 'public');
            $mainCategory->image = $imagePath;
        }

        $mainCategory->name = $request->name;
        $mainCategory->description = $request->description;
        $mainCategory->save();

        return response()->json($mainCategory);
    }

    public function destroy(Request $request, $category_id)
    {
        MainCategory::find($category_id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
