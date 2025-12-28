<?php
// app/Exports/BookingsExport.php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $bookings;

    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    public function collection()
    {
        return $this->bookings;
    }

    public function headings(): array
    {
        return [
            'Booking ID',
            'Transaction Date',
            'Booking Date',
            'Booking Time',
            'Customer Name',
            'Phone',
            'Email',
            'Service Type',
            'Package',
            'Duration',
            'Participants',
            'Payment Status',
            'Base Price',
            'Lunch Total',
            'Total Amount',
            'Location',
            'Payment Type'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->order_id,
            $transaction->transaction_time 
                ? $transaction->transaction_time->format('d M Y, H:i')
                : $transaction->created_at->format('d M Y, H:i'),
            $transaction->booking_date 
                ? $transaction->booking_date->format('d M Y')
                : 'Not set',
            $this->formatBookingTime($transaction),
            $transaction->nama_lengkap,
            $transaction->phone,
            $transaction->email,
            $transaction->room_type,
            $transaction->paket ?? 'Standard',
            $this->getDuration($transaction),
            $transaction->jumlah_orang ? $transaction->jumlah_orang . ' people' : 'Not specified',
            ucfirst($transaction->status),
            number_format($transaction->gross_amount, 0),
            number_format($transaction->lunch_total, 0),
            number_format($transaction->gross_amount + $transaction->lunch_total, 0),
            $transaction->location ? $transaction->location->name : 'Not assigned',
            $transaction->payment_type ?? 'Not specified'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function formatBookingTime($transaction): string
    {
        if ($transaction->start_time && $transaction->jam) {
            return $transaction->start_time . ' (' . $transaction->jam . ' hours)';
        }
        
        if ($transaction->start_time) {
            return $transaction->start_time;
        }
        
        return 'Flexible';
    }

    private function getDuration($transaction): string
    {
        if ($transaction->jam) return $transaction->jam . ' Hours';
        if ($transaction->hari) return $transaction->hari . ' Days';
        if ($transaction->minggu) return $transaction->minggu . ' Weeks';
        if ($transaction->bulan) return $transaction->bulan . ' Months';
        if ($transaction->tahun) return $transaction->tahun . ' Years';
        
        return 'Custom Duration';
    }
}