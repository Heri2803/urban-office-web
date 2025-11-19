<?php
namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServicePrice;


class ServicePriceController extends Controller
{
    // get private office and meeting room
    public function getServicePrice(Request $request)
{
    $roomTypeId = $request->input('room_type_id');
    $roomId = $request->input('room_id'); // ✅ Tambahkan ini
    $serviceCategoryId = $request->input('service_category_id');
    $durationType = $request->input('duration_type');
    $duration = $request->input('duration');
    $coffeeBreakOption = $request->input('coffee_break_option');
    $people = request('people');
    
    // Logika otomatis pilih kategori HANYA untuk Meeting Room (room_type_id = 2)
    if ($roomTypeId == 2 && $people > 6) {
        $serviceCategoryId = 2; 
    }
    
    $query = DB::table('service_prices')
        ->select('id', 'base_price', 'coffee_break_price', 'deposit', 'duration_type', 'duration')
        ->where('room_type_id', $roomTypeId)
        ->where('duration_type', $durationType) 
        ->where('duration', $duration);
    
    // ✅ Filter berdasarkan room_id jika tersedia
    if (!empty($roomId)) {
        $query->where('room_id', $roomId);
    }
    
    // service_category_id HANYA untuk Meeting Room
    if ($roomTypeId == 2) {
        $query->where('service_category_id', $serviceCategoryId);
    }
    
    if (!empty($coffeeBreakOption)) {
        $query->where('coffee_break_option', $coffeeBreakOption);
    }
    
    $price = $query->first();
    
    if (!$price) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }
    
    return response()->json($price);
}
    
    // Ambil semua paket untuk Virtual Office (room_type_id = 5)
    public function getVirtualOfficePackages()
    {
        $packages = ServicePrice::with('category')
            ->where('room_type_id', 5) // ✅ TIDAK HARDCODE NAMA PAKET, hanya tipe layanan
            ->get([
                'id', 
                'service_category_id', 
                'base_price', 
                'deposit', 
                'duration_type', 
                'duration'
            ]);
    
        if ($packages->isEmpty()) {
            return response()->json(['message' => 'No packages found'], 404);
        }
    
        $formatted = $packages->map(function ($item) {
            return [
                'id' => $item->id,
                'value' => strtolower(str_replace(' ', '_', $item->category->name)),
                'name' => $item->category->name ?? 'Tanpa Nama',
                'service_category_id' => $item->service_category_id,
                'duration_type' => $item->duration_type,
                'duration' => $item->duration,
                'base_price' => $item->base_price,
                'deposit' => $item->deposit,
            ];
        });
    
        return response()->json($formatted);
    }
    
    //Ambil semua paket untuk Coworking Space (room_type_id = 3)
    public function getCoworkingPasses()
    {
        $passes = \App\Models\ServicePrice::where('room_type_id', 3)
            ->get(['id', 'service_category_id', 'duration_type', 'duration', 'base_price']); // ðŸ†• Tambahkan service_category_id
    
        if ($passes->isEmpty()) {
            return response()->json(['message' => 'No coworking passes found'], 404);
        }
    
        // Format respons agar mudah digunakan di frontend
        $formatted = $passes->map(function ($item) {
            $name = '';
            $category = ''; //Untuk membedakan normal vs student
    
            // ðŸ†• Tentukan kategori berdasarkan service_category_id
            if ($item->service_category_id == 7) {
                $category = 'normal';
            } elseif ($item->service_category_id == 8) {
                $category = 'student';
            }
    
            // Tentukan nama otomatis berdasarkan tipe durasi
            if ($item->duration_type === 'hour') {
                if ($item->duration == 1) {
                    $name = 'Per Jam';
                } elseif ($item->duration == 6) {
                    $name = ($category === 'student' ? 'Student Pass 6 Jam' : 'Daily Pass 6 Jam');
                } elseif ($item->duration == 8) {
                    $name = ($category === 'student' ? 'Student Pass 8 Jam' : 'Daily Pass 8 Jam');
                } else {
                    $name = 'Pass ' . $item->duration . ' Jam';
                }
            } elseif ($item->duration_type === 'month') {
                $name = 'Membership Bulanan';
            }
    
            // ðŸ†• Buat value yang unique dengan menambahkan kategori
            $baseValue = strtolower(str_replace(' ', '_', $name));
            
            return [
                'id' => $item->id,
                'service_category_id' => $item->service_category_id,
                'value' => $baseValue, // ðŸ†• Value sudah unique karena nama sudah beda
                'name' => $name,
                'category' => $category, // ðŸ†• Tambahkan field category
                'duration_type' => $item->duration_type,
                'duration' => $item->duration,
                'price' => $item->base_price,
            ];
        });
    
        return response()->json($formatted);
    }
    
    //Ambil data untuk event space
    public function getEventSpacePrices()
    {
        $prices = ServicePrice::where('room_type_id', 4)
            ->orderBy('duration')
            ->get(['id', 'duration_type', 'duration', 'base_price', 'coffee_break_price', 'coffee_break_option']);

        if ($prices->isEmpty()) {
            return response()->json(['message' => 'No event space prices found'], 404);
        }

        // Format response untuk frontend
        $formatted = $prices->map(function ($item) {
            // Tentukan value berdasarkan duration
            $value = '';
            if ($item->duration_type === 'hour') {
                if ($item->duration == 4) $value = '4h';
                elseif ($item->duration == 8) $value = '8h';
                else $value = $item->duration . 'h';
            } elseif ($item->duration_type === 'day') {
                $value = 'daily';
            } elseif ($item->duration_type === 'week') {
                $value = 'weekly';
            } elseif ($item->duration_type === 'month') {
                $value = 'monthly';
            }

            // Parse coffee break option (format: "1x" atau "2x")
            $coffeeBreakCount = null;
            if ($item->coffee_break_option) {
                // Extract number dari format "1x" atau "2x"
                preg_match('/(\d+)x?/i', $item->coffee_break_option, $matches);
                $coffeeBreakCount = isset($matches[1]) ? (int)$matches[1] : null;
            }

            return [
                'id' => $item->id,
                'value' => $value,
                'duration_type' => $item->duration_type,
                'duration' => $item->duration,
                'base_price' => $item->base_price, // Tanpa coffee break
                'coffee_break_price' => $item->coffee_break_price, // Dengan coffee break
                'coffee_break_option' => $item->coffee_break_option, // "1x" atau "2x"
                'coffee_break_count' => $coffeeBreakCount, // 1 atau 2
            ];
        });

        return response()->json($formatted);
    }
    
}
