<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // TODO: Replace with actual database queries
        $transactions = $this->getDummyTransactions();
        
        return view('layouts.admin.dashboard', compact('transactions'));
    }

    /**
     * Generate dummy transaction data
     * TODO: Replace with actual database query
     */
    private function getDummyTransactions()
    {
        $services = [
            'meeting-room' => 'Meeting Room',
            'private-office' => 'Private Office',
            'sharing-room' => 'Sharing Room',
            'coworking-space' => 'Coworking Space',
            'virtual-office' => 'Virtual Office',
            'event-space' => 'Event Space'
        ];

        $statuses = ['settlement', 'pending', 'expired'];
        $names = ['John Doe', 'Jane Smith', 'Ahmad Rizki', 'Siti Nurhaliza', 'Budi Santoso', 'Lisa Anderson'];

        $allTransactions = [];

        foreach ($services as $key => $service) {
            $transactions = [];
            
            // Generate 3-7 random transactions per service
            $count = rand(3, 7);
            
            for ($i = 0; $i < $count; $i++) {
                $transactions[] = [
                    'id' => uniqid(),
                    'name' => $names[array_rand($names)],
                    'service' => $service,
                    'booking_date' => date('Y-m-d', strtotime('-' . rand(0, 30) . ' days')),
                    'booking_time' => $this->generateBookingTime(),
                    'payment_status' => $statuses[array_rand($statuses)],
                    'amount' => rand(100000, 5000000)
                ];
            }

            $allTransactions[$key] = $transactions;
        }

        return $allTransactions;
    }

    private function generateBookingTime()
    {
        $startHours = ['08:00', '09:00', '10:00', '13:00', '14:00'];
        $start = $startHours[array_rand($startHours)];
        
        $endTime = strtotime($start) + (rand(2, 8) * 3600); // 2-8 hours later
        $end = date('H:i', $endTime);
        
        return "$start - $end";
    }

    /**
     * Get filtered transaction data for AJAX
     */
    public function getTransactions(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $service = $request->input('service');

        // TODO: Query database with filters
        // Example:
        // $transactions = Transaction::query()
        //     ->when($service, fn($q) => $q->where('service_type', $service))
        //     ->when($startDate, fn($q) => $q->where('booking_date', '>=', $startDate))
        //     ->when($endDate, fn($q) => $q->where('booking_date', '<=', $endDate))
        //     ->get();

        return response()->json([
            'success' => true,
            'data' => $this->getDummyTransactions(),
            'message' => 'Data loaded successfully'
        ]);
    }

    /**
     * Get chart data for AJAX
     */
    public function getChartData(Request $request)
    {
        $period = $request->input('period', 'monthly');

        // TODO: Query database and aggregate data
        $chartData = [
            'labels' => [],
            'data' => []
        ];

        if ($period === 'daily') {
            // Last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = date('d M', strtotime("-$i days"));
                $chartData['labels'][] = $date;
                $chartData['data'][] = rand(5, 20);
            }
        } elseif ($period === 'monthly') {
            // Last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = date('M Y', strtotime("-$i months"));
                $chartData['labels'][] = $date;
                $chartData['data'][] = rand(100, 500);
            }
        } elseif ($period === 'yearly') {
            // Last 5 years
            for ($i = 4; $i >= 0; $i--) {
                $year = date('Y', strtotime("-$i years"));
                $chartData['labels'][] = $year;
                $chartData['data'][] = rand(1000, 5000);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }
}
