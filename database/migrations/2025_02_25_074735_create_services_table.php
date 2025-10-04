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
        Schema::create('services', function (Blueprint $table) {
            $table->id(); // Primary Key (Auto-incremented ID)
            $table->string('name'); // Service Name
            $table->text('description')->nullable(); // Service Description
            $table->unsignedBigInteger('category_id'); // Category ID (without foreign key)
            $table->decimal('price', 10, 2); // Price with decimal support
            $table->string('image')->nullable(); // Image Path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
