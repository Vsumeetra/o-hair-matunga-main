<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTime extends Model
{
    use HasFactory;
    
   
    protected $fillable = [
        'service_id',
        'day',
        'slot',
    ];

    /**
     * Get the service that owns the ServiceTime.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}