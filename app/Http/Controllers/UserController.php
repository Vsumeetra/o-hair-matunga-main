<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // =================== 

    public function index(Request $request)
    {
        $limit = $request->get("limit", 10); // Default limit 10
        $page = $request->get("page", 1); // Default page 1
        $search = $request->get("search");
    
        // Start query
        $query = User::with('appointments')->where('role', 'user');

    
        // Apply search filter if provided
        if ($search) {
            $query->where("first_name", "LIKE", "%$search%")->orWhere("last_name", "LIKE",  "%$search%")->orWhere("email", "LIKE",  "%$search%")->orWhere("phone_number", "LIKE",  "%$search%");
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
    

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|string|in:VVIP,VIP,Normal,Black List',
        ]);
    
        // Find the user
        $user = User::find($id);
    
        // Check if the user exists
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'success' => false,
            ], 404);
        }
    
        // Update the status
        $user->update(['status' => $request->status]);
    
        return response()->json([
            'message' => 'User updated successfully',
            'success' => true,
            'data' => $user,
        ]);
    }
    
    
    
}
