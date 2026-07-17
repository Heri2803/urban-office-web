<?php

namespace App\Imports;

use App\Models\Transaction;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VirtualOfficeImport implements ToCollection, WithHeadingRow
{
    private $errors = [];
    private $importedCount = 0;
    private $adminId;
    private $sequenceNumber = null;

    public function __construct($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * Parse Excel collection of rows.
     */
    public function collection(Collection $rows)
    {
        $rowNumber = 1; // Header is row 1, data starts at row 2

        foreach ($rows as $row) {
            $rowNumber++;

            // Skip completely empty rows
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            // Map and defensive-read headings
            $data = [
                'nama_lengkap' => trim($row['nama_lengkap'] ?? ''),
                'email' => trim($row['email'] ?? ''),
                'phone' => trim($row['no_telepon'] ?? $row['no_telp'] ?? $row['telepon'] ?? ''),
                'company_name' => trim($row['nama_perusahaan'] ?? $row['perusahaan'] ?? ''),
                'company_address' => trim($row['alamat_perusahaan'] ?? $row['alamat'] ?? ''),
                'nik' => trim($row['nik'] ?? ''),
                'npwp' => trim($row['npwp'] ?? ''),
                'status_pkp' => trim($row['status_pkp'] ?? ''),
                'location_name' => trim($row['nama_lokasi'] ?? $row['lokasi'] ?? ''),
                'booking_date' => $this->transformDate($row['tanggal_mulai'] ?? $row['tgl_mulai'] ?? ''),
                'paket' => trim($row['paket'] ?? ''),
                'duration' => trim($row['durasi_bulan'] ?? $row['durasi'] ?? ''),
                'gross_amount' => trim($row['harga_sewa_idr'] ?? $row['harga_sewa'] ?? $row['harga'] ?? ''),
                'deposit' => trim($row['deposit_idr'] ?? $row['deposit'] ?? ''),
                'contract_date' => $this->transformDate($row['tanggal_kontrak'] ?? $row['tgl_kontrak'] ?? ''),
                'status' => trim($row['status_transaksi'] ?? $row['status'] ?? ''),
                'notes' => trim($row['catatan'] ?? $row['keterangan'] ?? ''),
            ];

            // Validation rules
            $validator = Validator::make($data, [
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:50',
                'status_pkp' => 'required|in:PKP,Non PKP',
                'location_name' => 'required|string',
                'booking_date' => 'required|date_format:Y-m-d',
                'paket' => 'required|in:monthly,yearly',
                'duration' => 'required|integer|min:1',
                'gross_amount' => 'required|numeric|min:0',
                'deposit' => 'nullable|numeric|min:0',
                'contract_date' => 'nullable|date_format:Y-m-d',
                'status' => 'required|in:settlement,pending,expire',
            ], [
                'nama_lengkap.required' => 'Kolom Nama Lengkap wajib diisi.',
                'email.required' => 'Kolom Email wajib diisi.',
                'email.email' => 'Format Email tidak valid.',
                'phone.required' => 'Kolom No Telepon wajib diisi.',
                'status_pkp.required' => 'Kolom Status PKP wajib diisi.',
                'status_pkp.in' => 'Kolom Status PKP harus bernilai "PKP" atau "Non PKP".',
                'location_name.required' => 'Kolom Nama Lokasi wajib diisi.',
                'booking_date.required' => 'Kolom Tanggal Mulai wajib diisi.',
                'booking_date.date_format' => 'Format Tanggal Mulai tidak valid (Harus YYYY-MM-DD).',
                'paket.required' => 'Kolom Paket wajib diisi.',
                'paket.in' => 'Kolom Paket harus bernilai "monthly" atau "yearly".',
                'duration.required' => 'Kolom Durasi wajib diisi.',
                'duration.integer' => 'Kolom Durasi harus berupa angka bulat.',
                'gross_amount.required' => 'Kolom Harga Sewa wajib diisi.',
                'gross_amount.numeric' => 'Kolom Harga Sewa harus berupa nominal angka.',
                'deposit.numeric' => 'Kolom Deposit harus berupa nominal angka.',
                'contract_date.date_format' => 'Format Tanggal Kontrak tidak valid (Harus YYYY-MM-DD).',
                'status.required' => 'Kolom Status Transaksi wajib diisi.',
                'status.in' => 'Kolom Status Transaksi harus bernilai "settlement", "pending", atau "expire".',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $this->errors[] = "Baris {$rowNumber}: {$error}";
                }
                continue;
            }

            // Find Location
            $location = Location::withoutGlobalScopes()->with('city')->where('name', $data['location_name'])->first();
            if (!$location) {
                $this->errors[] = "Baris {$rowNumber}: Kolom 'Nama Lokasi' berisi '" . $data['location_name'] . "' yang tidak terdaftar di database.";
                continue;
            }
            $data['location_id'] = $location->id;
            $data['city_id'] = $location->city_id;
            $data['city'] = $location->city ? $location->city->name : '';

            // User handling (Opsi A: auto-create if not exists)
            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                try {
                    $user = User::create([
                        'name' => $data['nama_lengkap'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'password' => bcrypt('UrbanOffice123!'), // Default password
                        'role' => 'customer',
                    ]);
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNumber}: Gagal membuat akun customer baru (" . $e->getMessage() . ").";
                    continue;
                }
            }
            $data['user_id'] = $user->id;

            // Generate sequential order ID following MANUAL-ORDER format
            $data['order_id'] = $this->generateManualOrderId();

            // Map standard fields
            $data['room_type'] = 'Virtual Office';
            $data['created_by'] = $this->adminId;
            $data['payment_type'] = 'Imported';
            
            // Convert duration depending on paket
            if ($data['paket'] === 'yearly') {
                $data['tahun'] = (int)$data['duration'];
                $data['bulan'] = (int)$data['duration'] * 12;
            } else {
                $data['bulan'] = (int)$data['duration'];
                $data['tahun'] = 0;
            }

            // Save Transaction
            Transaction::create([
                'user_id' => $data['user_id'],
                'city_id' => $data['city_id'],
                'location_id' => $data['location_id'],
                'city' => $data['city'],
                'room_type' => $data['room_type'],
                'paket' => $data['paket'],
                'bulan' => $data['bulan'],
                'tahun' => $data['tahun'],
                'status_pkp' => $data['status_pkp'],
                'phone' => $data['phone'],
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'order_id' => $data['order_id'],
                'gross_amount' => $data['gross_amount'],
                'deposit' => $data['deposit'] ?: 0,
                'company_name' => $data['company_name'],
                'company_address' => $data['company_address'],
                'notes' => $data['notes'],
                'nik' => $data['nik'],
                'npwp' => $data['npwp'],
                'status' => $data['status'],
                'payment_type' => $data['payment_type'],
                'transaction_time' => Carbon::now(),
                'created_by' => $data['created_by'],
                'contract_date' => $data['contract_date'] ?: null,
                'booking_date' => $data['booking_date'],
            ]);

            $this->importedCount++;
        }
    }

    /**
     * Helper to parse Excel dates to standard string dates.
     */
    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Check if numeric (Excel serial date representation)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Normal parsing using Carbon
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return $value; // Return raw value so the validator can fail with date_format
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Generate manual order ID sequentially.
     * Format: MANUAL-ORDER-YYYYMMDD-XXX
     */
    private function generateManualOrderId(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'MANUAL-ORDER';
        
        if ($this->sequenceNumber === null) {
            $likePattern = "{$prefix}-%";
            $lastOrder = Transaction::withoutGlobalScopes()
                ->where('order_id', 'like', $likePattern)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastOrder) {
                $parts = explode('-', $lastOrder->order_id);
                $this->sequenceNumber = (int)end($parts) + 1;
            } else {
                $this->sequenceNumber = 1;
            }
        } else {
            $this->sequenceNumber++;
        }

        return "{$prefix}-{$date}-" . str_pad($this->sequenceNumber, 3, '0', STR_PAD_LEFT);
    }
}
