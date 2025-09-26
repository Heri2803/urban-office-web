@php
    use Carbon\Carbon;
    $date = Carbon::now()->format('d/m/Y'); // sama dengan "en-GB" format dd/mm/yyyy
@endphp

<div class="w-full bg-white shadow-md rounded-2xl px-4 sm:px-6 md:px-8 py-3 flex flex-row items-center justify-between">
    <!-- Left Side (Title + Divider + Date) -->
    <div class="flex items-center space-x-3">
        <h1 class="text-sm sm:text-base md:text-lg font-semibold text-gray-800 tracking-wide">
            Mails
        </h1>
        <span class="w-[2px] h-4 sm:h-5 bg-orange-500"></span>
        <span class="text-xs sm:text-sm md:text-base text-gray-500">{{ $date }}</span>
    </div>
</div>
