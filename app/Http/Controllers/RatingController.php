<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Get all ratings.
     */
    public function getAllRatings()
    {
        return response()->json(Rating::all());
    }

    /**
     * Get a single rating by ID.
     */
    public function getRatingById($id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }
        return response()->json($rating);
    }

    /**
     * Create a new rating.
     */
    public function createRating(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'service_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating = Rating::create($validated);

        return response()->json(['message' => 'Rating added successfully', 'rating' => $rating], 201);
    }

    /**
     * Update an existing rating.
     */
    public function updateRating(Request $request, $id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'review' => 'sometimes|nullable|string',
        ]);

        $rating->update($validated);

        return response()->json(['message' => 'Rating updated successfully', 'rating' => $rating]);
    }

    /**
     * Delete a rating.
     */
    public function deleteRating($id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $rating->delete();
        return response()->json(['message' => 'Rating deleted successfully']);
    }
}
