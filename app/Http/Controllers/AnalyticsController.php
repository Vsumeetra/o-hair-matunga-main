<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MainCategory;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    // ================== Dashboard Analytics ==================
    public function DashboardAnalytics()
    {
        $users = User::all()->count();
        $appointments = Appointment::all()->count();
        $services = Service::all()->count();
        $categories = MainCategory::all()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users,
                'appointments' => $appointments,
                'services' => $services,
                'categories' => $categories,
            ],
        ]);
    }
}
