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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('service_id'); // Storing array as JSON
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('appointment_status');
            $table->text('notes')->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('remaining', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
