<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $appends = ['services']; // Auto-add services to JSON response

    protected $fillable = [
        'user_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'appointment_status',
        'notes',
        'total',
        'remaining',
        'subtotal',
        'tax',
    ];

    protected $casts = [
        'service_id' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getServicesAttribute()
    {
        // Decode the JSON array of service IDs
        $serviceIds = json_decode($this->service_id ?? '[]', true);
        
        // Fetch services based on IDs
        return Service::whereIn('id', $serviceIds)->get();
    }

}
