<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    /**
     * Display a listing of the slots.
     */
    public function index(Request $request)
    {
        $limit = $request->get("limit", 10); // Default limit 10
        $page = $request->get("page", 1); // Default page 1
        $search = $request->get("search");
    
        // Start query
        $query = Slot::query();
    
        // Apply search filter if provided
        if ($search) {
            $query->where("day", "LIKE", "%$search%")->orWhere("type", "LIKE", "%$search%");
        }
    
        // Get total count before applying pagination
        $total = $query->count();
    
        // Apply pagination
        $result = $query->skip(($page - 1) * $limit)->take($limit)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
    
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
     * Store a newly created slot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required',
            'duration' => 'required|integer',
            'type' => 'required',
            'status' => 'required|in:active,deactive',
            'opening_time' => 'required',
            'closing_time' => 'required',
        ]);

        $slot = Slot::create($validated);
        return response()->json($slot, 201);
    }

    /**
     * Display the specified slot.
     */
    public function show(Request $request, $day)
    {
        $type = $request->get('type');
        $date = $request->get('date');
        $slot = Slot::where(["day"=>$day, "status"=>"active", 'type'=>$type])->first();

        if (!$slot) {
            return response()->json([
                'success' => true,
                'message' => 'No booking slots available',
                'data' => [],
            ]);
        }

        $openTime = Carbon::parse($slot->opening_time);
        $closedTime = Carbon::parse($slot->closing_time);
        $duration = $slot->duration;

        $bookedAppointments = Appointment::where(['appointment_date'=> $date, 'appointment_status'=>'Success'])
        ->pluck('appointment_time')
        ->toArray();


        $availableSlots = [];

        while ($openTime->lt($closedTime)) {
            $formattedTime = $openTime->format("h:i a"); // 12-hour format
    
            // Add to available slots if not booked
            if (!in_array($formattedTime, $bookedAppointments)) {
                $availableSlots[] = $formattedTime;
            }
    
            $openTime->addMinutes($duration); // Increment by duration
        }
    

        return response()->json([
            'success' => true,
            'data' => $availableSlots,
        ]);
    }

    /**
     * Update the specified slot.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'day' => 'sometimes',
            'duration' => 'sometimes|integer',
            'type' => 'sometimes',
            'status' => 'sometimes|in:active,deactive',
            'opening_time' => 'sometimes',
            'closing_time' => 'sometimes',
        ]);

        $slot = Slot::findOrFail($id);
        $slot->update($validated);

        return response()->json($slot);
    }

    /**
     * Remove the specified slot.
     */
    public function destroy($id)
    {
        $slot = Slot::findOrFail($id);
        $slot->delete();

        return response()->json(['message' => 'Slot deleted successfully']);
    }
}
