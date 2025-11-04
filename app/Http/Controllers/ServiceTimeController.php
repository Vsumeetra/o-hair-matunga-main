<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceTime;
use App\Models\Service;

use Illuminate\Support\Facades\DB;

class ServiceTimeController extends Controller
{
    /**
     * Store multiple service times at once.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'service_times' => ['required', 'array', 'min:1'],
            'service_times.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'service_times.*.day' => ['required', 'string', 'max:10'],
            'service_times.*.slot' => ['required', 'string', 'max:50'],
        ]);

        
        $now = now();
        $dataToInsert = collect($validated['service_times'])->map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->toArray();

        
        try {
            DB::transaction(function () use ($dataToInsert) {
                ServiceTime::insert($dataToInsert);
            });

            return response()->json([
                'message' => 'Service times created successfully.',
                'count' => count($dataToInsert)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create service times.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
public function index(Request $request)
{
    $serviceId = $request->query('service_id');
    $perPage = $request->query('per_page', 10);
    $page = $request->query('page', 1);
    $search = $request->query('search'); // fixed typo "serach" → "search"
    $main_category_id = $request->query('main_category_id');
    $category_id = $request->query('category_id');

    // Base query


    // Build query
    $query = Service::with(['SubCategory', 'SubCategory.MainCategory','serviceTimes'])
        ->whereHas('serviceTimes'); // ✅ Only services having related serviceTimes

    // Optional: Filter by specific service ID
    if ($serviceId) {
        $query->where('id', $serviceId);
    }

    // Optional: Search by name or title (if column exists)
    if ($search) {
        $query->where('name', 'like', "%{$search}%");
    }
    if ($main_category_id) {
        $query->whereHas('SubCategory', function ($q) use ($main_category_id) {
            $q->where('main_category_id', $main_category_id);
        });
    }
        if ($category_id) {
        $query->whereHas('SubCategory', function ($q) use ($category_id) {
            $q->where('id', $category_id);
        });
    }

    


    // Pagination
    $services = $query->paginate($perPage, ['*'], 'page', $page);

    return response()->json($services);
}



    /**
     * List & filter service times
     * Filters: service_name, day, slot, main_category_id, category_id
     */
// public function index(Request $request)
// {
//     $mainCategoryId = $request->query('main_category_id');
//     $serviceName = $request->query('service_name');
//     $day = $request->query('day');
//     $slot = $request->query('slot');
//     $perPage = $request->query('per_page', 10);

//     // Base query
//     // $query = ServiceTime::with(['service.SubCategory.MainCategory']);
//    $query = ServiceTime::with(['service.SubCategory']);

//    if ($mainCategoryId) {
//     $query->whereHas('service.SubCategory', function ($q) use ($mainCategoryId) {
//         $q->where('main_category_id', $mainCategoryId);
//     });
// }


//     if ($serviceName) {
//         $query->whereHas('service', function ($q) use ($serviceName) {
//             $q->where('name', 'like', '%' . $serviceName . '%');
//         });
//     }

//     if ($day) {
//         $query->where('day', $day);
//     }

//     if ($slot) {
//         $query->where('slot', 'like', '%' . $slot . '%');
//     }

//     $serviceTimes = $query->orderBy('day')->paginate($perPage);

//     if ($serviceTimes->isEmpty()) {
//         return response()->json([
//             'message' => 'No services available for the selected filters.'
//         ], 200);
//     }

//     // Group
//     $grouped = [];
//     foreach ($serviceTimes as $item) {
//         $service = $item->service;

//         if (!isset($grouped[$service->id])) {
//             $grouped[$service->id] = [
//                 'service_id' => $service->id,
//                 'service_name' => $service->name,
//                 'description' => $service->description,
//                 'price' => $service->price,
//                 'category' => optional($service->SubCategory)->name ?? null,
//                 'main_category' => optional($service->SubCategory->MainCategory)->name ?? null,
//                 'slots' => [],
//             ];
//         }

//         $grouped[$service->id]['slots'][] = [
//             'id' => $item->id,
//             'day' => $item->day,
//             'slot' => $item->slot,
//         ];
//     }

   
//     $serviceTimes->setCollection(collect(array_values($grouped)));

//     return response()->json([
//         'message' => 'Service times grouped by service fetched successfully.',
//         'data' => $serviceTimes
//     ], 200);
// }




    /**
     * Update an existing service time.
     */
    public function update(Request $request, ServiceTime $serviceTime)
    {
        // Validate optional update fields
        $validated = $request->validate([
            'day' => ['sometimes', 'required', 'string', 'max:10'],
            'slot' => ['sometimes', 'required', 'string', 'max:50'],
        ]);

        // Check for duplicates only if both day & slot are provided
        if ($request->has(['day', 'slot'])) {
            $isDuplicate = ServiceTime::where('service_id', $serviceTime->service_id)
                ->where('day', $request->input('day'))
                ->where('slot', $request->input('slot'))
                ->where('id', '!=', $serviceTime->id)
                ->exists();

            if ($isDuplicate) {
                return response()->json([
                    'message' => 'This combination of service, day, and slot already exists.'
                ], 422);
            }
        }

        //  Update the record
        $serviceTime->update($validated);

        return response()->json([
            'message' => 'Service time updated successfully.',
            'service_time' => $serviceTime
            // 'service_time' => $serviceTime->fresh('service')
        ]);
    }

    /**
     * Delete a service time
     */
    public function destroy(ServiceTime $serviceTime)
    {
        $serviceTime->delete();

        return response()->json(['message' => 'Service time deleted successfully.'], 200);
    }
}
