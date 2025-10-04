<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone_number' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ensure valid image file
                'description' => 'nullable|string',
                'specialization' => 'nullable|string',
                'role' => 'nullable|in:admin,user,stylist',
                'gender' => 'nullable|string',
                'password' => 'nullable|string|min:8',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }
    
            // Create a new user
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'date_of_birth' => $request->input('date_of_birth'),
                'image' => $imagePath, // Store path instead of direct URL
                'description' => $request->input('description'),
                'specialization' => $request->input('specialization'),
                'role' => $request->input('role', 'user'), // Defaults to 'user' if not provided
                'gender' => $request->input('gender'),
                'password' => Hash::make($request->input('password')),
            ]);
    
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ], 201);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log in a user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Log out a user.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Update user details.
     */
    public function updateUser(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone_number' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string',
                'specialization' => 'nullable|string',
                'gender' => 'nullable|string',
                'password' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::find($id);

            // Handle image upload
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $user->image = $request->file('image')->store('images', 'public');
            }

            // Update user details
            $user->update($request->except(['password', 'image']));

            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
                $user->save();
            }

            return response()->json([
                'message' => 'User details updated successfully',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate a reset token
        $token = Str::random(64);

        // Save the reset token and expiry in a database table (you need a password_resets table)
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Send the token via email
        Mail::to($user->email)->send(new \App\Mail\ForgotPasswordMail($token, $user->name, $user->email));

        return response()->json(['success' => true, 'message' => 'Password reset link sent to your email.'], 200);
    }

    public function resetPassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Check if the reset token is valid
        $passwordReset = \DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired reset token.'], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password reset successful.'], 200);
    }
}
