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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('first_name'); // First name of the user
            $table->string('last_name'); // Last name of the user
            $table->string('email')->unique(); // Unique email address
            $table->string('phone_number')->nullable(); // Phone number (nullable)
            $table->date('date_of_birth')->nullable(); // Date of birth (nullable)
            $table->string('image')->nullable(); // Profile image (nullable)
            $table->text('description')->nullable(); // Description (nullable)
            $table->string('specialization')->nullable(); // Specialization (nullable)
            $table->enum('role', ['admin', 'user', 'stylist'])->default('user'); // Role with default 'user'
            $table->string('token')->nullable(); // Token field (nullable)
            $table->string('gender')->nullable(); // Gender field (nullable)
            $table->string('password'); // Password field (required)
            $table->timestamps(); // Created at and updated at timestamps
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};