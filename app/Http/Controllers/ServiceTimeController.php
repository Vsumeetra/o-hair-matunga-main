<?php

// In: app/Http/Controllers/ServiceTimeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceTime;
use Illuminate\Support\Facades\DB;

class ServiceTimeController extends Controller
{
    /**
     * Store created service times in storage (multiple insertion).
     */
    public function store(Request $request)
    {
        // 1. Validation (valid array of timeslots)
        
        $validated = $request->validate([
            // Must be an array, and keys are not strictly defined
            'service_times' => ['required', 'array', 'min:1'],
            
            // Validate each item inside the 'service_times' array
            'service_times.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'service_times.*.day' => ['required', 'string', 'max:10'],
            'service_times.*.slot' => ['required', 'string', 'max:50'],
        ]);

        // 2.Data Insertion.
        $dataToInsert = [];
        $now = now(); //  current time

        foreach ($validated['service_times'] as $timeSlot) {
            $dataToInsert[] = array_merge($timeSlot, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        
        // 3. To Insert multiple records in a single query.
      

        try {
            DB::beginTransaction();
            
            // constraint $table->unique(['service_id', 'day', 'slot']); 
            
            ServiceTime::insert($dataToInsert); 

            DB::commit();

            return response()->json([
                'message' => 'Service times created successfully.',
                'count' => count($dataToInsert)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json(['message' => 'Failed to create service times.', 'error' => $e->getMessage()], 500);
        }
       
    }
    
    /**
     * to fetch the listing of the service times with filtering options.
     * Filters: service_name, day, slot.
     */
    public function index(Request $request)
    {
        // 1. Get filter parameters from the request query
        $serviceName = $request->query('service_name');
        $day = $request->query('day');
        $slot = $request->query('slot');
        
        // 2. Start the query on the ServiceTime model
        $query = ServiceTime::query();
        
        //  load the service relationship to use for filtering and for the final output
        $query->with('service'); 

        // 3. Apply Filtering Logic

        // Filter by Service Name (requires joining or using a whereHas clause)
        if ($serviceName) {
            $query->whereHas('service', function ($q) use ($serviceName) {
                // Search the 'name' column in the 'services' table
                $q->where('name', 'like', '%' . $serviceName . '%');
            });
        }
        
        // Filter by Day
        if ($day) {
            $query->where('day', $day);
        }

        // Filter by Slot
        if ($slot) {
            $query->where('slot', 'like', '%' . $slot . '%');
        }

        // 4. Execute the query and return results
        $serviceTimes = $query->orderBy('day')->get();

        return response()->json($serviceTimes);
    }


     /**
     * Update the specific time slot .
     */
    public function update(Request $request, ServiceTime $serviceTime)
    {
        // 1. Validation: Only require fields that might be updated
        $validated = $request->validate([
            'day' => ['sometimes', 'required', 'string', 'max:10'],
            'slot' => ['sometimes', 'required', 'string', 'max:10'],
        ]);

        
        
    
        if ($request->has(['day', 'slot'])) {
            $isDuplicate = ServiceTime::where('service_id', $serviceTime->service_id)
                                    ->where('day', $request->input('day'))
                                    ->where('slot', $request->input('slot'))
                                    ->where('id', '!=', $serviceTime->id) 
                                    ->exists();
            
            if ($isDuplicate) {
                return response()->json([
                    'message' => 'The combination of service, day, and slot already exists.'
                ], 422);
            }
        }

        // 3. Update the Record
        $serviceTime->update($validated);

        return response()->json([
            'message' => 'Service time updated successfully.',
            'service_time' => $serviceTime
        ]);
    }

      /**
     * delete service timetable slot from db.
     */
    public function destroy(ServiceTime $serviceTime)
    {
        // 1. Delete the Record
        $serviceTime->delete();

        return response()->json(['message' => 'Service time deleted successfully.'], 200);
    }
}