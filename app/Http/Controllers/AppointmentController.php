<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments.
     */
 public function index(Request $request)
{
    try {
        $page = $request->get("page", 1); // Default page is 1
        $limit = $request->get("limit", 10); // Default limit is 10
        $search = $request->get("search");

        // Use route parameter $date unless overridden by request
        $date = $request->get("date");

        // Start query
        $query = Appointment::with(['user']);
        
        if ($date) {
            $query->where('appointment_date', $date);
        }

        // Apply search filter
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where("first_name", "LIKE", "%$search%")
                  ->orWhere("last_name", "LIKE", "%$search%")
                  ->orWhere("phone_number", "LIKE", "%$search%")
                  ->orWhere("email", "LIKE", "%$search%")
                  ->orWhere("appointment_time", "LIKE", "%$search%")
                  ->orWhere("appointment_date", "LIKE", "%$search%");
            });
        }

        // Count after search filtering
        $total = $query->count();

        // Apply pagination
        $result = $query->orderBy('appointment_time', 'desc')
                        ->skip(($page - 1) * $limit)
                        ->take($limit)
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $result,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'page' => $page,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
    }
}

    

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'service_id' => 'required|array',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'appointment_status' => 'required|string',
            'total' => 'required|numeric',
            'remaining' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
        ]);

        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'service_id' => json_encode($request->service_id),
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'appointment_status' => $request->appointment_status,
            'notes' => $request->notes ?? null,
            'total' => $request->total,
            'remaining' => $request->remaining,
            'subtotal' => $request->subtotal,
            'tax' => $request->tax,
        ]);
        
        $user = User::find($request->user_id);

        Mail::to('arbazansari9320@gmail.com')->send(new \App\Mail\AppointmentAdmin(
            $request->appointment_date, 
            $user->name, 
            $request->appointment_time, 
            $user->phone_number
        ));
        Mail::to($user->email)->send(new \App\Mail\AppointmentConfirmUser($request->appointment_date, $user->name, $request->appointment_time));


        return response()->json($appointment, 201);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Request $request, $id)
    {
        try {
            $page = $request->get("page", 1); // Default page is 1
            $limit = $request->get("limit", 10); // Default limit is 10
            $search = $request->get("search");
    
            // Use route parameter $date unless overridden by request
            $date = $request->get("date");
    
            // Start query
            $query = Appointment::with(['user'])->where("user_id", $id);
            
            if ($date) {
                $query->where('appointment_date', $date);
            }
    
            // Apply search filter
            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where("first_name", "LIKE", "%$search%")
                      ->orWhere("last_name", "LIKE", "%$search%")
                      ->orWhere("phone_number", "LIKE", "%$search%")
                      ->orWhere("email", "LIKE", "%$search%")
                      ->orWhere("appointment_time", "LIKE", "%$search%")
                      ->orWhere("appointment_date", "LIKE", "%$search%");
                });
            }
    
            // Count after search filtering
            $total = $query->count();
    
            // Apply pagination
            $result = $query->orderBy('appointment_date', 'desc')
                            ->skip(($page - 1) * $limit)
                            ->take($limit)
                            ->get();
    
            return response()->json([
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'page' => $page,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update([
            'user_id' => $request->user_id ?? $appointment->user_id,
            'service_id' => json_encode($request->service_id ?? json_decode($appointment->service_id, true)),
            'appointment_date' => $request->appointment_date ?? $appointment->appointment_date,
            'appointment_time' => $request->appointment_time ?? $appointment->appointment_time,
            'appointment_status' => $request->appointment_status ?? $appointment->appointment_status,
            'notes' => $request->notes ?? $appointment->notes,
            'total' => $request->total ?? $appointment->total,
            'remaining' => $request->remaining ?? $appointment->remaining,
            'subtotal' => $request->subtotal ?? $appointment->subtotal,
            'tax' => $request->tax ?? $appointment->tax,
        ]);

        return response()->json($appointment);
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy($id)
    {
        Appointment::findOrFail($id)->update(['appointment_status' => 'Cancelled']);
        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
