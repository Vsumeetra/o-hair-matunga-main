<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryModal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'categories'; // Updated to match the plural convention

    protected $fillable = [
        'name',
        'image',
        'description',
        'app_icon',
        'main_category_id', // Added the main_category_id field
    ];
    protected $dates = ['deleted_at']; // Ensure deleted_at is handled as a timestamp

    /**
     * Get the services that belong to this category.
     */
    public function MainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id', 'id');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }
}
