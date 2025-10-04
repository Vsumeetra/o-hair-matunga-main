<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\MainCategoryController;
// use App\Http\Controllers\RatingController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceTimeController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Service Time Routes
// Route to insert multiple service timetable at once
// Service Time Routes






Route::middleware('auth:sanctum')->group(function () {

    Route::get('/categories', [CategoryController::class, 'index']); // Get all categories
    Route::post('/categories', [CategoryController::class, 'store']); // Create a new category
    Route::get('/categories/{id}', [CategoryController::class, 'show']); // Get a single category
    Route::put('/categories/{id}', [CategoryController::class, 'update']); // Update a category
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']); // Delete a category

    Route::get('/fetch-category-services', [ServiceController::class, 'fetchCategoryAndServices']);

    Route::get('/services', [ServiceController::class, 'index']); // Get all services
    Route::post('/services', [ServiceController::class, 'store']); // Create a new service
    Route::get('/services/{service}', [ServiceController::class, 'show']); // Get a specific service
    Route::put('/services/{service}', [ServiceController::class, 'update']); // Update a service
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']); // Delete a service



    // Route to get all appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);

    // Route to create a new appointment
    Route::post('appointments', [AppointmentController::class, 'store']);

    // Route to get a single appointment by ID
    Route::get('appointments/{id}', [AppointmentController::class, 'show']);

    // Route to update an appointment
    Route::put('appointments/{id}', [AppointmentController::class, 'update']);

    // Route to delete an appointment
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy']);


    // Route::get('/ratings', [RatingController::class, 'index']); // Get all ratings
    // Route::post('/ratings', [RatingController::class, 'store']); // Create a new rating
    // Route::get('/ratings/{rating}', [RatingController::class, 'show']); // Get a specific rating
    // Route::put('/ratings/{rating}', [RatingController::class, 'update']); // Update a rating
    // Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']); // Delete a rating

    Route::get('/ratings', [RatingController::class, 'getAllRatings']);       // Get all ratings
    Route::get('/ratings/{id}', [RatingController::class, 'getRatingById']);  // Get single rating
    Route::post('/ratings', [RatingController::class, 'createRating']);       // Create rating
    Route::put('/ratings/{id}', [RatingController::class, 'updateRating']);   // Update rating
    Route::delete('/ratings/{id}', [RatingController::class, 'deleteRating']); // Delete rating

    Route::get('/slots', [SlotController::class, 'index']);       // Get all slots
    Route::post('/slots', [SlotController::class, 'store']);      // Create a new slot
    Route::get('/slots/{day}', [SlotController::class, 'show']);   // Get a specific slot
    Route::put('/slots/{id}', [SlotController::class, 'update']); // Update a slot
    Route::delete('/slots/{id}', [SlotController::class, 'destroy']); // Delete a slot

    Route::get('/reminders', [ReminderController::class, 'index']);

    // Create a new reminder
    Route::post('/reminders', [ReminderController::class, 'store']);

    // Get a single reminder by ID
    Route::get('/reminders/{id}', [ReminderController::class, 'show']);

    // Update a reminder
    Route::put('/reminders/{id}', [ReminderController::class, 'update']);

    // Delete a reminder
    Route::delete('/reminders/{id}', [ReminderController::class, 'destroy']);


    Route::get('maincategories', [MainCategoryController::class, 'index']); // Get all categories
    Route::post('maincategories', [MainCategoryController::class, 'store']); // Store a new category
    Route::get('maincategories-all', [MainCategoryController::class, 'show']); // Get a single category
    Route::put('maincategories/{category_id}', [MainCategoryController::class, 'update']); // Update a category
    Route::delete('maincategories/{category_id}', [MainCategoryController::class, 'destroy']); // Delete a category

    Route::get('users', [UserController::class, 'index']); // Get all categories
    Route::put('users-status-update/{id}', [UserController::class, 'update']); // Get all categories
    Route::get('dashboard-analytics', [AnalyticsController::class, 'DashboardAnalytics']); // Get all categories

    Route::put('user-update/{id}', [Authcontroller::class, 'updateUser']);

    


});

Route::prefix('slotcontrol')->group(function () {
    // Create a new service timetable
    Route::post('/', [ServiceTimeController::class, 'store']);

    // Get a list of service timetables (with optional service_id filter)
    Route::get('/', [ServiceTimeController::class, 'index']);

    // Update a service timetable by its ID
    Route::put('/{serviceTime}', [ServiceTimeController::class, 'update']);

    // Delete a specific service timetable by its ID
    Route::delete('/{serviceTime}', [ServiceTimeController::class, 'destroy']);
});