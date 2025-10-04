<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    // Fetch all reminders
    public function index()
    {
        return response()->json(Reminder::all(), 200);
    }

    // Store a new reminder
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'reminder_type' => 'required|in:greetings,offers,appointment,reminderNext',
            'reminder_message' => 'required|string',
        ]);

        $reminder = Reminder::create($validated);
        return response()->json(['message' => 'Reminder created successfully', 'data' => $reminder], 201);
    }

    // Get a single reminder by ID
    public function show($id)
    {
        $reminder = Reminder::find($id);
        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }
        return response()->json($reminder);
    }

    // Update a reminder
    public function update(Request $request, $id)
    {
        $reminder = Reminder::find($id);
        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|integer',
            'reminder_type' => 'sometimes|in:greetings,offers,appointment,reminderNext',
            'reminder_message' => 'sometimes|string',
        ]);

        $reminder->update($validated);
        return response()->json(['message' => 'Reminder updated successfully', 'data' => $reminder]);
    }

    // Delete a reminder
    public function destroy($id)
    {
        $reminder = Reminder::find($id);
        if (!$reminder) {
            return response()->json(['message' => 'Reminder not found'], 404);
        }

        $reminder->delete();
        return response()->json(['message' => 'Reminder deleted successfully']);
    }
}

