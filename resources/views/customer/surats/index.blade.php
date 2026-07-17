@extends('layouts.app')

@section('title', 'Kotak Surat Saya')

@section('content')
<div class="flex min-h-screen bg-gray-50 mb-12">
    
    {{-- Main Content --}}
    <div class="flex-1 ml-0 md:ml-52 lg:ml-64 xl:ml-64 transition-all duration-500 ease-in-out">
        
        {{-- Header Section (Mirip Profile) --}}
        <div 
            class="relative h-48 sm:h-42 lg:h-64 bg-cover bg-center animate-fade-in"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=400&fit=crop')"
        >
            {{-- Orange Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/90 via-orange-400/70 to-transparent"></div>
            
            {{-- Header Content --}}
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between h-full p-4 sm:p-6 lg:p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-3 md:space-y-0 md:space-x-4 w-full">
                    {{-- Profile Image --}}
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-full overflow-hidden border-4 border-white shadow-lg animate-slide-in-left cursor-pointer"
                         onclick="toggleUserPopup()">
                        @auth
                        <img
                            src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://via.placeholder.com/150' }}"
                            alt="Profile"
                            class="w-full h-full object-cover"
                        />
                        @else
                        <img
                            src="https://via.placeholder.com/150"
                            alt="Guest"
                            class="w-full h-full object-cover"
                        />
                        @endauth
                    </div>
                    
                    {{-- Profile Info --}}
                    <div class="text-white flex-1 animate-slide-in-up">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold">
                            Selamat Datang {{ Auth::user()->name ?? 'Guest' }}
                        </h1>
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 mt-2">
                            <span class="inline-flex items-center bg-green-500 text-white text-xs sm:text-sm px-3 py-1 rounded-full font-medium w-fit">
                                {{ Auth::user()->paket ?? 'Virtual Office' }}
                            </span>
                            <span class="text-sm sm:text-base opacity-90 break-all sm:break-normal">
                                {{ Auth::user()->telephone ?? '-' }} / 
                                {{ Str::limit(Auth::user()->email ?? '-', 25) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Detail Button --}}
                <button 
                    onclick="toggleUserPopup()"
                    class="bg-white text-orange-500 px-4 py-2 sm:px-6 sm:py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-lg animate-slide-in-right hover:scale-105 absolute top-4 right-4 sm:relative sm:top-auto sm:right-auto">
                    Detail
                </button>
            </div>
        </div>

        {{-- User Info Popup --}}
        <div id="userPopup" 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in hidden" 
            onclick="toggleUserPopup()">
            <div 
                class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 lg:p-8 m-4 w-full max-w-sm sm:max-w-md lg:max-w-lg animate-slide-in-up"
                onclick="event.stopPropagation()"
            >
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Detail Pengguna</h3>
                    <button onclick="toggleUserPopup()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex flex-col items-center mb-4 sm:mb-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gray-100 rounded-full overflow-hidden border-4 border-orange-200 mb-3">
                        @auth
                        <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : 'https://via.placeholder.com/150' }}" alt="Profile" class="w-full h-full object-cover"/>
                        @else
                        <img src="https://via.placeholder.com/150" alt="Guest" class="w-full h-full object-cover"/>
                        @endauth
                    </div>
                    <h4 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-800">
                        {{ Auth::user()->name ?? 'Guest' }}
                    </h4>
                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs sm:text-sm px-2 sm:px-3 py-1 rounded-full font-medium mt-2">
                        Active
                    </span>
                </div>
                
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-400 text-sm sm:text-base">📧</span>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500">Email</p>
                            <p class="text-sm sm:text-base text-gray-800 break-all">{{ optional(Auth::user())->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-400 text-sm sm:text-base">📱</span>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500">Telepon</p>
                            <p class="text-sm sm:text-base text-gray-800">{{ optional(Auth::user())->telephone ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-400 text-sm sm:text-base">📍</span>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500">Alamat</p>
                            <p class="text-sm sm:text-base text-gray-800">{{ optional(Auth::user())->alamat ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-400 text-sm sm:text-base">📦</span>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500">Paket</p>
                            <p class="text-sm sm:text-base text-gray-800">{{ optional(Auth::user())->paket ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-400 text-sm sm:text-base">📅</span>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500">Bergabung Sejak</p>
                            <p class="text-sm sm:text-base text-gray-800">{{ optional(optional(Auth::user())->created_at)->format('d F Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dashboard Content --}}
        <div class="p-4 sm:p-6 lg:p-8 -mt-6 sm:-mt-8 relative z-20">
            <div class="max-w-7xl mx-auto space-y-4">
                
                {{-- Kembali & Header Content --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard.profile') }}" class="p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 leading-tight">Kotak Surat Saya</h2>
                            <p class="text-xs text-gray-500">Daftar surat fisik yang masuk dan dikelola Urban Office</p>
                        </div>
                    </div>
                    @php $unreadCount = auth()->user()->unreadSuratsCount(); @endphp
                    @if($unreadCount > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 text-xs font-bold rounded-full shadow-sm animate-pulse flex-shrink-0">
                        {{ $unreadCount }} Belum Dibaca
                    </span>
                    @endif
                </div>

                {{-- ===== FILTER (inline-fit, left) ===== --}}
                <div class="inline-flex w-full sm:w-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-4 py-3 w-full">
                        <form method="GET" action="{{ route('dashboard.surats.index') }}">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                {{-- Search --}}
                                <div class="relative flex-1 sm:flex-none">
                                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                           placeholder="Cari perihal, pengirim..."
                                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl w-full sm:w-52 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none transition-all">
                                </div>

                                {{-- Status --}}
                                <select name="read_status" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-400 outline-none bg-white text-gray-700">
                                    <option value="">Semua Status</option>
                                    <option value="unread" {{ request('read_status') === 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                                    <option value="read"   {{ request('read_status') === 'read'   ? 'selected' : '' }}>Sudah Dibaca</option>
                                </select>

                                {{-- Cari Button --}}
                                <button type="submit"
                                        class="px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-xl hover:bg-orange-600 transition-all shadow-sm hover:shadow-md whitespace-nowrap">
                                    Cari
                                </button>

                                {{-- Reset --}}
                                @if(request()->hasAny(['search','read_status']))
                                <a href="{{ route('dashboard.surats.index') }}"
                                   class="p-2 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-colors flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ===== RECORD COUNT (left) ===== --}}
                @if($surats->total() > 0)
                <div class="flex items-center gap-1.5 text-xs text-gray-500 px-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Menampilkan <strong class="text-gray-700 mx-1">{{ $surats->count() }}</strong> dari <strong class="text-gray-700 mx-1">{{ $surats->total() }}</strong> surat
                </div>
                @endif

                {{-- ===== SURAT GRID ===== --}}
                @forelse($surats as $surat)
                @if($loop->first)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @endif

                <a href="{{ route('dashboard.surats.show', $surat) }}"
                   class="group flex flex-col bg-white rounded-2xl border transition-all duration-200 hover:shadow-xl hover:-translate-y-1
                          {{ !$surat->pivot->is_read ? 'border-orange-300 ring-1 ring-orange-100 shadow-sm shadow-orange-100' : 'border-gray-100 shadow-sm' }}">

                    {{-- Top: Icon + Date --}}
                    <div class="p-4 pb-3 flex items-start justify-between gap-2">
                        <div class="relative w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                    {{ !$surat->pivot->is_read ? 'bg-orange-100' : 'bg-gray-100' }}">
                            <svg class="w-5 h-5 {{ !$surat->pivot->is_read ? 'text-orange-500' : 'text-gray-400' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            @if(!$surat->pivot->is_read)
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-orange-500 rounded-full border-2 border-white shadow-sm"></span>
                            @endif
                        </div>
                        <div class="text-right flex-1 min-w-0">
                            <div class="text-[11px] font-medium text-gray-400 leading-snug">{{ $surat->tanggal_surat->format('d M Y') }}</div>
                            @if(!$surat->pivot->is_read)
                            <span class="inline-block mt-0.5 text-[9px] font-black text-orange-600 bg-orange-100 px-1.5 py-0.5 rounded-md tracking-wide uppercase">Baru</span>
                            @endif
                        </div>
                    </div>

                    {{-- Body: Perihal + Pengirim + Nomor + Ringkasan --}}
                    <div class="px-4 pb-3 flex-1 flex flex-col gap-1.5">
                        <h3 class="text-sm {{ !$surat->pivot->is_read ? 'font-bold text-orange-900' : 'font-semibold text-gray-800' }}
                                   group-hover:text-orange-600 transition-colors line-clamp-2 leading-snug">
                            {{ $surat->perihal }}
                        </h3>
                        <div class="text-xs text-gray-500 truncate">
                            <span class="font-medium text-gray-600">{{ $surat->pengirim }}</span>
                        </div>
                        <div class="text-[11px] font-mono text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded truncate">
                            {{ $surat->nomor_surat }}
                        </div>
                        @if($surat->isi_ringkasan)
                        <p class="text-[11px] text-gray-400 line-clamp-2 leading-relaxed mt-0.5">{{ $surat->isi_ringkasan }}</p>
                        @endif
                    </div>

                    {{-- Footer: Badges --}}
                    <div class="px-4 py-3 border-t border-gray-50 flex flex-wrap gap-1.5 mt-auto">
                        @if($surat->status_pengambilan === 'sudah_diambil')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-semibold rounded-lg border border-emerald-200">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            @if($surat->metode_pengambilan === 'delivery') Delivery @else Diambil @endif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-semibold rounded-lg border border-amber-200">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Menunggu
                        </span>
                        @endif

                        @if($surat->file_path)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-50 text-orange-600 text-[10px] font-medium rounded-lg border border-orange-100">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            Lampiran
                        </span>
                        @endif
                    </div>

                    {{-- Arrow --}}
                    <div class="flex items-center justify-end px-4 pb-3">
                        <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-orange-400 group-hover:translate-x-1 transition-all"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                @if($loop->last)
                </div>
                @endif

                @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-14 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-semibold">Belum ada surat</p>
                    <p class="text-gray-400 text-sm mt-1.5">Surat yang dikirim kepada Anda akan muncul di sini</p>
                </div>
                @endforelse

                {{-- ===== PAGINATION (left) ===== --}}
                @if($surats->hasPages())
                <div class="flex justify-start py-2">
                    {{ $surats->withQueryString()->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
    function toggleUserPopup() {
        const popup = document.getElementById('userPopup');
        if (popup.classList.contains('hidden')) {
            popup.classList.remove('hidden');
        } else {
            popup.classList.add('hidden');
        }
    }
</script>
@endsection
