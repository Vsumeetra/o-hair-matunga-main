<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'name',
        'description',
        'category_id',
        'price',
        'image'
    ];

    protected $dates = ['deleted_at']; // Ensure deleted_at is handled as a timestamp

    /**
     * Get the category associated with the service.
     */
    public function SubCategory()
    {
        return $this->belongsTo(CategoryModal::class, 'category_id')->with("MainCategory");
    }
    /**
     * Get the ServiceTimes for the Service.
     */
    public function serviceTimes()
    {
        
        return $this->hasMany(ServiceTime::class);
    }
}
