<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceTime;
use Illuminate\Support\Facades\DB;

class ServiceTimeController extends Controller
{
    /**
     * Store multiple service times at once.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'service_times' => ['required', 'array', 'min:1'],
            'service_times.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'service_times.*.day' => ['required', 'string', 'max:10'],
            'service_times.*.slot' => ['required', 'string', 'max:50'],
        ]);

        
        $now = now();
        $dataToInsert = collect($validated['service_times'])->map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->toArray();

        
        try {
            DB::transaction(function () use ($dataToInsert) {
                ServiceTime::insert($dataToInsert);
            });

            return response()->json([
                'message' => 'Service times created successfully.',
                'count' => count($dataToInsert)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create service times.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List & filter service times
     * Filters: service_name, day, slot, main_category_id, category_id
     */
   public function index(Request $request)
{
    // Get query parameters
    $mainCategoryId = $request->query('main_category_id');
    $serviceName = $request->query('service_name');
    $day = $request->query('day');
    $slot = $request->query('slot');
    $perPage = $request->query('per_page', 10); 

   
    $query = ServiceTime::query()->with([
        'service.SubCategory.MainCategory' 
    ]);

    //  Filter by Main Category
    if ($mainCategoryId) {
        $query->whereHas('service.SubCategory.MainCategory', function ($q) use ($mainCategoryId) {
            $q->where('id', $mainCategoryId);
        });
    }

    // Filter by Service Name 
    if ($serviceName) {
        $query->whereHas('service', function ($q) use ($serviceName) {
            $q->where('name', 'like', '%' . $serviceName . '%');
        });
    }

    //  Filter by Day 
    if ($day) {
        $query->where('day', $day);
    }

    //  Filter by Slot 
    if ($slot) {
        $query->where('slot', 'like', '%' . $slot . '%');
    }

    
    $serviceTimes = $query->orderBy('day')->paginate($perPage);

    
    if ($serviceTimes->isEmpty()) {
        return response()->json([
            'message' => 'No services available for the selected filters.'
        ], 200);
    }

    //  Return the data
    return response()->json([
        'message' => 'Service times fetched successfully.',
        'data' => $serviceTimes
    ], 200);
}


    /**
     * Update an existing service time.
     */
    public function update(Request $request, ServiceTime $serviceTime)
    {
        // Validate optional update fields
        $validated = $request->validate([
            'day' => ['sometimes', 'required', 'string', 'max:10'],
            'slot' => ['sometimes', 'required', 'string', 'max:50'],
        ]);

        // Check for duplicates only if both day & slot are provided
        if ($request->has(['day', 'slot'])) {
            $isDuplicate = ServiceTime::where('service_id', $serviceTime->service_id)
                ->where('day', $request->input('day'))
                ->where('slot', $request->input('slot'))
                ->where('id', '!=', $serviceTime->id)
                ->exists();

            if ($isDuplicate) {
                return response()->json([
                    'message' => 'This combination of service, day, and slot already exists.'
                ], 422);
            }
        }

        //  Update the record
        $serviceTime->update($validated);

        return response()->json([
            'message' => 'Service time updated successfully.',
            'service_time' => $serviceTime
        ]);
    }

    /**
     * Delete a service time
     */
    public function destroy(ServiceTime $serviceTime)
    {
        $serviceTime->delete();

        return response()->json(['message' => 'Service time deleted successfully.'], 200);
    }
}
