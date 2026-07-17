{{-- resources/views/layouts/publicpage/addendum-verify.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Addendum - {{ $addendum->addendum_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background-color: #F0FDFA;">

    <div class="w-full max-w-lg">

        {{-- Card Utama --}}
        <div class="bg-white rounded-2xl overflow-hidden" style="border: 0.5px solid #F97316;">

            {{-- Header Teal --}}
            <div class="px-8 pt-8 pb-7 text-center" style="background-color: #EA580C;">

                {{-- Logo --}}
                <div class="mx-auto mb-4 flex items-center justify-center bg-white rounded-2xl" 
                     style="width: 80px; height: 80px; border: 2px solid #FED7AA;">
                    <img src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}" 
                         alt="Urban Office" 
                         style="width: 60px; height: 60px; object-fit: contain;" />
                </div>

                {{-- Judul --}}
                <div class="flex items-center justify-center gap-2 mb-1">
                    {{-- Checkmark icon --}}
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h1 class="text-white font-semibold" style="font-size: 18px;">Addendum Terverifikasi</h1>
                </div>
                <p style="color: #FED7AA; font-size: 13px;">PT. Urban Kreasi Bersama</p>
            </div>

            {{-- Body --}}
            <div class="px-8 py-6">

                {{-- Badge Status --}}
                @php
                    $badgeStyle = match($addendum->status) {
                        'active'     => 'background:#F0FDF4; border-color:#86EFAC; color:#15803D;',
                        'expired'    => 'background:#FFF7ED; border-color:#FED7AA; color:#C2410C;',
                        'terminated' => 'background:#FEF2F2; border-color:#FECACA; color:#B91C1C;',
                        'draft'      => 'background:#F3F4F6; border-color:#E5E7EB; color:#4B5563;',
                        default      => 'background:#F9FAFB; border-color:#E5E7EB; color:#374151;',
                    };
                    $badgeDot = match($addendum->status) {
                        'active'     => '#16A34A',
                        'expired'    => '#F97316',
                        'terminated' => '#DC2626',
                        'draft'      => '#6B7280',
                        default      => '#9CA3AF',
                    };
                    $badgeLabel = match($addendum->status) {
                        'active'     => 'Aktif',
                        'expired'    => 'Expired',
                        'terminated' => 'Dibatalkan',
                        'draft'      => 'Draft',
                        default      => ucfirst($addendum->status),
                    };
                @endphp

                <div class="flex justify-center mb-6">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-medium"
                          style="border: 1px solid; {{ $badgeStyle }}">
                        <span class="inline-block rounded-full" 
                              style="width: 8px; height: 8px; background-color: {{ $badgeDot }}; flex-shrink: 0;"></span>
                        Status Addendum: {{ $badgeLabel }}
                    </span>
                </div>

                {{-- Data Addendum --}}
                <div class="space-y-0">

                    <div class="flex justify-between items-start py-3" style="border-bottom: 0.5px solid #F3F4F6;">
                        <span class="text-sm" style="color: #6B7280; flex-shrink: 0;">Nomor Addendum</span>
                        <span class="text-sm font-medium text-right ml-4" style="color: #111827; max-width: 260px;">
                            {{ $addendum->addendum_number }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-3" style="border-bottom: 0.5px solid #F3F4F6;">
                        <span class="text-sm" style="color: #6B7280; flex-shrink: 0;">Nama Perusahaan</span>
                        <span class="text-sm font-medium text-right ml-4" style="color: #111827;">
                            {{ strtoupper($transaction->company_name ?? $transaction->nama_lengkap) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-3" style="border-bottom: 0.5px solid #F3F4F6;">
                        <span class="text-sm" style="color: #6B7280; flex-shrink: 0;">Penyewa</span>
                        <span class="text-sm font-medium text-right ml-4" style="color: #111827;">
                            {{ strtoupper($transaction->nama_lengkap) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-3" style="border-bottom: 0.5px solid #F3F4F6;">
                        <span class="text-sm" style="color: #6B7280; flex-shrink: 0;">Lokasi</span>
                        <span class="text-sm font-medium text-right ml-4" style="color: #111827;">
                            {{ $transaction->location->name ?? '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-3" style="border-bottom: 0.5px solid #F3F4F6;">
                        <span class="text-sm" style="color: #6B7280; flex-shrink: 0;">Tanggal Terbit</span>
                        <span class="text-sm font-medium text-right ml-4" style="color: #111827;">
                            {{ \Carbon\Carbon::parse($addendum->addendum_date)->translatedFormat('d F Y') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm" style="color: #6B7280; flex-shrink: 0;">Periode Perpanjangan</span>
                        <span class="text-sm font-medium text-right ml-4" style="color: #111827;">
                            {{ $transaction->bulan ?? 12 }} Bulan
                        </span>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 pb-8 pt-4 text-center" style="border-top: 0.5px solid #F3F4F6;">
                <p class="text-xs mb-4" style="color: #9CA3AF; line-height: 1.7;">
                    Dokumen ini diterbitkan secara digital dan dapat diverifikasi keasliannya.<br>
                    © {{ date('Y') }} PT. Urban Kreasi Bersama
                </p>

                {{-- Link ke halaman login --}}
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center gap-2 text-sm font-medium px-5 py-2.5 rounded-lg transition-colors"
                   style="color: #EA580C; border: 0.5px solid #F97316; text-decoration: none;"
                   onmouseover="this.style.backgroundColor='#F0FDFA'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    Masuk ke Akun Anda
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

        </div>

    </div>
</body>
</html>
