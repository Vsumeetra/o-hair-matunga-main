<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    

    /**
     * Display a listing of the services.
     */
 public function index(Request $request)
    {
        try {
            $limit = $request->get("limit", 10); // Default limit 10
            $page = $request->get("page", 1); // Default page 1
            $search = $request->get("search");
        
            // Start query
            $query = Service::with('SubCategory');
    
        
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
        } catch (\Exception $e) {
            Log::error('Fetching services failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch services',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'nullable|numeric',
                'price' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $validatedData['image'] = $imagePath;
            }
    
            // Create service
            $service = Service::create($validatedData);
    
            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);
        } catch (\Exception $e) {
            Log::error('Service creation failed: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $service->load('category'); // Load category relationship
        return response()->json($service);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'price' => 'sometimes|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            
            $imagePath = $request->file('image')->store('services', 'public');
            $validatedData['image'] = $imagePath;
        }
    
        $service->update($validatedData);
    
        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy($id)
    {
        Service::find($id)->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
}
