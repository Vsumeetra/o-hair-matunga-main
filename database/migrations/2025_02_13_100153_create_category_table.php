<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Category name
            $table->string('image')->nullable(); // Image path stored using Laravel storage
            $table->text('description')->nullable(); // Description of the category
            $table->string('app_icon')->nullable(); // App icon path stored using Laravel storage
            $table->integer('main_category_id')->nullable(); // Regular integer column without foreign key constraint
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
