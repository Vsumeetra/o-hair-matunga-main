<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'duration',
        'type',
        'status',
        'opening_time',
        'closing_time'
    ];

    public $timestamps = true;

    /**
     * Relationship: A Slot belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
