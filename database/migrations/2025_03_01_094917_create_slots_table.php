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
        Schema::create('slots', function (Blueprint $table) {
            $table->id('id'); // Custom primary key
            $table->text('day'); // Date and time of the slot
            $table->integer('duration'); // Slot duration in minutes
            $table->text('type'); // Booking status
            $table->enum('status', ['active', 'deactive'])->default('active');
            $table->text('opening_time'); // Opening time
            $table->text('closing_time'); // Closing time
            $table->timestamps();
        });
    }


};
