<?php

// In: database/migrations/YYYY_MM_DD_HHMMSS_update_slot_column_on_service_times_table.php

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
        Schema::table('service_times', function (Blueprint $table) {
            // Increasing the 'slot' column size to 50 characters to accommodate longer time slots.
            $table->string('slot', 50)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_times', function (Blueprint $table) {
            // Revert the change back to the original size (10)
            $table->string('slot', 10)->change();
        });
    }
};