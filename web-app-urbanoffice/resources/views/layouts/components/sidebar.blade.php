{{-- resources/views/components/sidebar.blade.php --}}
<div x-data="()" x-init="initSidebar()" class="sidebar-wrapper">
    <!-- Desktop & Tablet Sidebar -->
    <div class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 lg:w-64 md:w-52 md:bg-white md:border-r md:border-gray-200 md:shadow-sm">
        <!-- Logo -->
        <a href="{{ route('dashboard.home') }}" class="block">
            <div class="flex items-center justify-center p-4 border-b border-gray-200">
                <img
                    src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}"
                    alt="Urban Office Logo"
                    class="h-24 w-auto"
                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiByeD0iOCIgZmlsbD0iI0Y5N0EzNCIvPgo8dGV4dCB4PSIzMiIgeT0iMzgiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5VTzwvdGV4dD4KPHN2Zz4='"
                />
            </div>
        </a>

        <!-- Menu Items -->
        <nav class="flex-1 p-3 lg:p-4 space-y-2">
            <template x-for="(item, index) in menuItems" :key="item.name">
                <div>
                    <!-- Regular Link Items -->
                    <template x-if="!item.isAction">
                        <a :href="item.href" @click="setActiveItem(item.name)">
                            <button
                                :class="getDesktopButtonClass(item.name)"
                                class="w-full flex items-center gap-3 px-3 lg:px-4 py-2.5 lg:py-3 text-sm font-medium rounded-lg transition-all duration-200 hover:bg-gray-50 hover:scale-105 hover:shadow-sm group"
                            >
                                <span :class="getIconClass(item.name)" x-html="item.icon"></span>
                                <span :class="getTextClass(item.name)" x-text="item.name"></span>
                            </button>
                        </a>
                    </template>

                    <!-- Action Items (like Logout) -->
                    <template x-if="item.isAction">
                        <button
                            @click="setActiveItem(item.name); handleLogout()"
                            :class="getDesktopButtonClass(item.name)"
                            class="w-full flex items-center gap-3 px-3 lg:px-4 py-2.5 lg:py-3 text-sm font-medium rounded-lg transition-all duration-200 hover:bg-gray-50 hover:scale-105 hover:shadow-sm group"
                        >
                            <span :class="getIconClass(item.name)" x-html="item.icon"></span>
                            <span :class="getTextClass(item.name)" x-text="item.name"></span>
                        </button>
                    </template>
                </div>
            </template>
        </nav>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
        <nav class="relative flex justify-between items-center px-4 py-2">
            <!-- Grup kiri (2 item: Sewa, Mitra) -->
            <div class="flex gap-12 flex-1 justify-start">
                <template x-for="(item, index) in menuItems.slice(1, 3)" :key="item.name">
                    <div>
                        <template x-if="!item.isAction">
                            <a :href="item.href" @click="setActiveItem(item.name)">
                                <button :class="getMobileButtonClass(item.name)" class="flex flex-col items-center gap-2 text-xs font-medium transition-all duration-200">
                                    <span x-html="item.icon"></span>
                                    <span x-text="item.name"></span>
                                </button>
                            </a>
                        </template>
                        <template x-if="item.isAction">
                            <button
                                @click="setActiveItem(item.name); item.action()"
                                :class="getMobileButtonClass(item.name)"
                                class="flex flex-col items-center gap-2 text-xs font-medium transition-all duration-200"
                            >
                                <span x-html="item.icon"></span>
                                <span x-text="item.name"></span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Logo di tengah -->
            <div class="absolute left-1/2 -translate-x-1/2">
                <a href="{{ route('dashboard.home') }}">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center shadow-lg bg-white">
                        <img
                            src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}"
                            alt="Urban Office Logo"
                            class="w-12 h-12 object-contain"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiByeD0iOCIgZmlsbD0iI0Y5N0EzNCIvPgo8dGV4dCB4PSIyNCIgeT0iMjgiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxOCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5VTzwvdGV4dD4KPHN2Zz4='"
                        />
                    </div>
                </a>
            </div>

            <!-- Grup kanan (2 item: Akun, Logout) -->
            <div class="flex gap-12 flex-1 justify-end">
                <template x-for="(item, index) in menuItems.slice(3, 5)" :key="item.name">
                    <div>
                        <template x-if="!item.isAction">
                            <a :href="item.href" @click="setActiveItem(item.name)">
                                <button :class="getMobileButtonClass(item.name)" class="flex flex-col items-center gap-2 text-xs font-medium transition-all duration-200">
                                    <span x-html="item.icon"></span>
                                    <span x-text="item.name"></span>
                                </button>
                            </a>
                        </template>
                        <template x-if="item.isAction">
                            <button
                                @click="setActiveItem(item.name); handleLogout()"
                                :class="getMobileButtonClass(item.name)"
                                class="flex flex-col items-center gap-2 text-xs font-medium transition-all duration-200"
                            >
                                <span x-html="item.icon"></span>
                                <span x-text="item.name"></span>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </nav>
    </div>
</div>