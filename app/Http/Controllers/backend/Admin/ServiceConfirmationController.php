<?php
// app/Http\Controllers/backend/Admin/ServiceConfirmationController.php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ServiceConfirmationController extends Controller
{
    // Di ServiceConfirmationController.php - method index()
    public function index(Request $request)
    {
        logger('=== SERVICE CONFIRMATION - WITH USER RELATIONSHIP ===');
        
        // ✅ CORRECT: Use 'user' relationship instead of 'customer'
        $query = Transaction::with(['user' => function($q) {
                $q->select('id', 'name'); // Pastikan kolom 'name' ada di tabel users
            }])
            ->whereIn('room_type', ['Coworking Space', 'Event Space'])
            ->whereIn('status', ['settlement', 'confirmed']);

        logger('Updated query SQL:', ['sql' => $query->toSql()]);
        logger('Updated query bindings:', $query->getBindings());

        $bookings = $query->get();

        logger('=== QUERY RESULTS WITH USER RELATION ===');
        logger('Total transactions found: ' . $bookings->count());
        
        foreach ($bookings as $booking) {
            logger("Found Booking ID: {$booking->id}", [
                'room_type' => $booking->room_type,
                'status' => $booking->status,
                'booking_date' => $booking->booking_date,
                'nama_lengkap' => $booking->nama_lengkap, // Dari transaction
                'user_name' => $booking->user ? $booking->user->name : 'No user', // Dari relationship user
                'order_id' => $booking->order_id,
                'start_time' => $booking->start_time,
                'gross_amount' => $booking->gross_amount,
                'has_user' => !is_null($booking->user)
            ]);
        }

        // Group by service type
        $coworkingBookings = $bookings->where('room_type', 'Coworking Space')
            ->map(function($booking) {
                return $this->formatBookingData($booking);
            })->values();
            
        $eventSpaceBookings = $bookings->where('room_type', 'Event Space')
            ->map(function($booking) {
                return $this->formatBookingData($booking);
            })->values();

        logger('=== FINAL FORMATTED DATA ===');
        logger('Coworking bookings: ' . $coworkingBookings->count());
        logger('Event space bookings: ' . $eventSpaceBookings->count());
        
        return view('layouts.admin.service-confirmation', compact('coworkingBookings', 'eventSpaceBookings'));
    }
    
    public function getBookingData(Request $request)
    {
        $query = Transaction::with(['user' => function($q) {
                $q->select('id', 'name');
            }])
            ->whereIn('room_type', ['Coworking Space', 'Event Space'])
            ->whereIn('status', ['settlement', 'confirmed']);
            
        // Apply filters from request
        if ($request->has('filters')) {
            $filters = $request->filters;
            
            if (!empty($filters['startDate'])) {
                $query->whereDate('booking_date', '>=', $filters['startDate']);
            }
            
            if (!empty($filters['endDate'])) {
                $query->whereDate('booking_date', '<=', $filters['endDate']);
            }
            
            if (!empty($filters['serviceType'])) {
                $query->where('room_type', $filters['serviceType']);
            }
            
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
        }
        
        $bookings = $query->get();
        
        $coworkingBookings = $bookings->where('room_type', 'Coworking Space')
            ->map(function($booking) {
                return $this->formatBookingData($booking);
            })->values();
            
        $eventSpaceBookings = $bookings->where('room_type', 'Event Space')
            ->map(function($booking) {
                return $this->formatBookingData($booking);
            })->values();
            
        return response()->json([
            'success' => true,
            'coworkingBookings' => $coworkingBookings,
            'eventSpaceBookings' => $eventSpaceBookings,
            'total' => $bookings->count(),
            'timestamp' => now()
        ]);
    }
    
    private function formatBookingData($booking)
    {
        if (!$booking) {
            return null;
        }

        // ✅ Prioritize user name from relationship, fallback to nama_lengkap
        $customerName = $booking->user ? $booking->user->name : $booking->nama_lengkap;

        $formatted = [
            'id' => $booking->id,
            'booking_code' => $booking->order_id,
            'customer_name' => $customerName ?: 'Unknown Customer',
            'room_type' => $booking->room_type,
            'booking_date' => $booking->booking_date,
            'start_time' => $booking->start_time,
            'end_time' => $this->calculateEndTime($booking),
            'duration_type' => $this->determineDurationType($booking),
            'duration_value' => $this->determineDurationValue($booking),
            'amount' => $booking->gross_amount,
            'status' => $booking->status,
            'created_at' => $booking->created_at,
        ];

        logger("Formatted booking {$booking->id}:", [
            'customer_name_source' => $booking->user ? 'user_relationship' : 'nama_lengkap_field',
            'final_customer_name' => $formatted['customer_name']
        ]);

        return $formatted;
    }

    // Di ServiceConfirmationController.php - Perbaiki calculateEndTime
    private function calculateEndTime($booking)
    {
        if (!$booking->start_time) {
            return '17:00'; // Default end time
        }

        try {
            $startTime = Carbon::parse($booking->start_time);
            
            // Tentukan durasi berdasarkan paket atau field yang ada
            if ($booking->paket === 'hourly' && $booking->jam) {
                $durationHours = $booking->jam;
            } elseif ($booking->paket === 'daily') {
                $durationHours = 8; // 8 jam untuk daily
            } elseif ($booking->jam) {
                $durationHours = $booking->jam;
            } elseif ($booking->paket === 'event') {
                $durationHours = 4; // Default 4 jam untuk event
            } else {
                $durationHours = 4; // Default 4 jam
            }
            
            return $startTime->addHours($durationHours)->format('H:i');
            
        } catch (\Exception $e) {
            logger("Error calculating end time for booking {$booking->id}: " . $e->getMessage());
            return '17:00'; // Fallback
        }
    }

    private function determineDurationType($booking)
    {
        if ($booking->paket) {
            return $booking->paket; // hourly, daily, etc.
        }
        
        // Fallback berdasarkan field yang ada
        if ($booking->jam) return 'hour';
        if ($booking->hari) return 'day'; 
        if ($booking->minggu) return 'week';
        if ($booking->bulan) return 'month';
        
        return 'hour'; // Default
    }

    private function determineDurationValue($booking)
    {
        if ($booking->jam) return $booking->jam;
        if ($booking->hari) return $booking->hari;
        if ($booking->minggu) return $booking->minggu;
        if ($booking->bulan) return $booking->bulan;
        
        return 1; // Default 1 hour
    }
    
    public function confirmBooking(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->update(['status' => 'confirmed']);
            
            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm booking: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function cancelConfirmation(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->update(['status' => 'settlement']);
            
            return response()->json([
                'success' => true,
                'message' => 'Confirmation cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel confirmation: ' . $e->getMessage()
            ], 500);
        }
    }
}