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
        Schema::create('service_times', function (Blueprint $table) {
            $table->id(); // Primary key

            
            $table->foreignId('service_id') 
                  ->constrained('services') 
                  ->onDelete('cascade'); 

            $table->string('day', 10); 

            $table->string('slot', 10);

            // Unique Constraint: A service cannot have the same day and slot combination multiple times.
            $table->unique(['service_id', 'day', 'slot']); 

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_times');
    }
};