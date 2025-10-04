<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'date_of_birth',
        'image',
        'description',
        'specialization',
        'role',
        'token',
        'gender',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id', 'id');
    }
}
