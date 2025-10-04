<?php

// In: database/seeders/ServiceTimeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceTime;
use App\Models\Service; 
use Illuminate\Support\Facades\DB; 

class ServiceTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. TEMPORARILY DISABLE FOREIGN KEY CONSTRAINTS
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Clear old data in the correct order: CHILD first, then PARENT
        DB::table('service_times')->truncate();
        DB::table('services')->truncate(); // This will now work!

        // 3. RE-ENABLE FOREIGN KEY CONSTRAINTS
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 

        // 4. Create the REQUIRED Service Records
        $service1 = Service::create([
            'name' => 'Haircut and Style',
            'description' => 'A standard haircut and style.', 
            'category_id' => 1, 
            'price' => 500
        ]);
        $service2 = Service::create([
            'name' => 'Haircut by vishal',
            'description' => 'A special haircut for men & women.', 
            'category_id' => 2,
            'price' => 300
        ]);

        // 5. Prepare and Insert the ServiceTime data
        $data = [
            ['service_id' => $service1->id, 'day' => 'Monday', 'slot' => '10:00 AM - 12:00 PM'],
            ['service_id' => $service1->id, 'day' => 'Monday', 'slot' => '02:00 PM - 04:00 PM'],
            ['service_id' => $service2->id, 'day' => 'Wednesday', 'slot' => '01:00 PM - 03:00 PM'],
        ];
        ServiceTime::insert($data);
    }
}