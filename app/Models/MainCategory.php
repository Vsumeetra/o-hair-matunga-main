<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'description'
    ];

    protected $dates = ['deleted_at']; // Ensure deleted_at is handled as a timestamp

    public function subCategories()
    {
        return $this->hasMany(CategoryModal::class, 'main_category_id')->with("services");
    }
    
    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
