{{-- resources/views/admin/booking/room-assignment.blade.php --}}

@extends('layouts.admin')

@section('title', 'Room Assignment')

@section('content')
<div x-data="roomAssignment()" x-init="init()" class="space-y-6 pb-20 md:pb-6">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">🚪 Room Assignment</h1>
            <p class="text-sm text-gray-500 mt-1">Assign customers to specific rooms</p>
        </div>
        <div class="flex gap-2">
            <button @click="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                🔄 Refresh
            </button>
            <button @click="showLegend = !showLegend" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                📖 Legend
            </button>
        </div>
    </div>

    {{-- Legend Modal/Dropdown --}}
    <div x-show="showLegend" x-transition class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h3 class="font-semibold text-gray-800 mb-3">Room Status Legend:</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-sm text-gray-700">Available - Ready to assign</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-500 rounded"></div>
                <span class="text-sm text-gray-700">Occupied - Room is booked</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                <span class="text-sm text-gray-700">Maintenance - Under repair</span>
            </div>
        </div>
    </div>

    {{-- Service Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        {{-- Tab Headers --}}
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex min-w-max md:min-w-0">
                <template x-for="service in services" :key="service.id">
                    <button 
                        @click="activeTab = service.id; selectedRoom = null; selectedCustomer = null;"
                        :class="activeTab === service.id ? 'border-blue-600 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 md:px-6 py-3 md:py-4 border-b-2 font-medium text-sm whitespace-nowrap transition"
                    >
                        <span x-text="service.icon"></span>
                        <span x-text="service.name" class="ml-2"></span>
                    </button>
                </template>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-4 md:p-6">
            <template x-for="service in services" :key="service.id">
                <div x-show="activeTab === service.id" x-transition>
                    
                    {{-- Info Bar --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">ℹ️</span>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">Assignment Instructions</h4>
                                <p class="text-sm text-blue-700">
                                    1. Select an available room by clicking the green button<br>
                                    2. Choose a customer with settlement status from dropdown<br>
                                    3. Click "Confirm Assignment" to assign customer to room
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Summary --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Total Rooms</p>
                            <p class="text-xl font-bold text-gray-800" x-text="getRoomsByService(service.id).length"></p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <p class="text-xs text-green-700 mb-1">Available</p>
                            <p class="text-xl font-bold text-green-600" x-text="getRoomsByStatus(service.id, 'available').length"></p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                            <p class="text-xs text-red-700 mb-1">Occupied</p>
                            <p class="text-xl font-bold text-red-600" x-text="getRoomsByStatus(service.id, 'occupied').length"></p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                            <p class="text-xs text-yellow-700 mb-1">Maintenance</p>
                            <p class="text-xl font-bold text-yellow-600" x-text="getRoomsByStatus(service.id, 'maintenance').length"></p>
                        </div>
                    </div>

                    {{-- Room Selection Grid --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <span x-text="service.icon"></span>
                            Select Room Number
                        </h3>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                            <template x-for="room in getRoomsByService(service.id)" :key="room.number">
                                <button
                                    @click="selectRoom(room)"
                                    :disabled="room.status !== 'available'"
                                    :class="{
                                        'bg-green-500 hover:bg-green-600 text-white': room.status === 'available' && selectedRoom?.number !== room.number,
                                        'bg-green-700 text-white ring-4 ring-green-300': room.status === 'available' && selectedRoom?.number === room.number,
                                        'bg-red-500 text-white cursor-not-allowed': room.status === 'occupied',
                                        'bg-yellow-500 text-white cursor-not-allowed': room.status === 'maintenance'
                                    }"
                                    class="relative p-4 rounded-lg font-semibold text-sm transition-all transform hover:scale-105 disabled:hover:scale-100 disabled:transform-none"
                                >
                                    <div x-text="room.number" class="text-lg mb-1"></div>
                                    <div class="text-xs opacity-90 capitalize" x-text="room.status"></div>
                                    
                                    {{-- Occupied Badge --}}
                                    <template x-if="room.status === 'occupied'">
                                        <div class="absolute top-1 right-1 w-2 h-2 bg-white rounded-full"></div>
                                    </template>
                                    
                                    {{-- Selected Checkmark --}}
                                    <template x-if="room.status === 'available' && selectedRoom?.number === room.number">
                                        <div class="absolute top-1 right-1">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Assignment Form --}}
                    <div x-show="selectedRoom" x-transition class="bg-gray-50 rounded-xl p-4 md:p-6 border-2 border-blue-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                <span x-text="selectedRoom?.number"></span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">
                                    Room <span x-text="selectedRoom?.number"></span>
                                </h3>
                                <p class="text-sm text-gray-600" x-text="service.name"></p>
                            </div>
                        </div>

                        {{-- Customer Selection --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Select Customer (Settlement Status Only)
                                </label>
                                <select 
                                    x-model="selectedCustomer"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                >
                                    <option value="">-- Choose Customer --</option>
                                    <template x-for="customer in getSettlementCustomers(service.id)" :key="customer.orderId">
                                        <option :value="customer.orderId">
                                            <span x-text="customer.orderId"></span> - 
                                            <span x-text="customer.name"></span> - 
                                            <span x-text="customer.duration"></span>
                                        </option>
                                    </template>
                                </select>
                                
                                {{-- Customer Details --}}
                                <template x-if="selectedCustomer">
                                    <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
                                        <template x-for="customer in getSettlementCustomers(service.id)" :key="customer.orderId">
                                            <div x-show="customer.orderId === selectedCustomer">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <span class="text-gray-500">Name:</span>
                                                        <span class="font-medium ml-2" x-text="customer.name"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Email:</span>
                                                        <span class="font-medium ml-2" x-text="customer.email"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Phone:</span>
                                                        <span class="font-medium ml-2" x-text="customer.phone"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Duration:</span>
                                                        <span class="font-medium ml-2" x-text="customer.duration"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Check-in:</span>
                                                        <span class="font-medium ml-2" x-text="customer.checkIn"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">Price:</span>
                                                        <span class="font-medium ml-2" x-text="'Rp ' + formatNumber(customer.price)"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- No Customers Available --}}
                                <div x-show="getSettlementCustomers(service.id).length === 0" class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">⚠️ No customers with settlement status available for this service.</p>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button 
                                    @click="confirmAssignment()"
                                    :disabled="!selectedCustomer"
                                    :class="selectedCustomer ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                                    class="flex-1 px-6 py-3 text-white rounded-lg font-semibold transition text-sm"
                                >
                                    ✅ Confirm Assignment
                                </button>
                                <button 
                                    @click="selectedRoom = null; selectedCustomer = null;"
                                    class="flex-1 sm:flex-initial px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition text-sm"
                                >
                                    ❌ Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Occupied Rooms List --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Currently Occupied Rooms</h3>
                        <div class="space-y-3">
                            <template x-for="room in getRoomsByStatus(service.id, 'occupied')" :key="room.number">
                                <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center text-white font-bold">
                                                <span x-text="room.number"></span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800" x-text="room.customerName"></p>
                                                <p class="text-sm text-gray-500" x-text="room.email"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-sm text-gray-600">
                                                <p>Check-in: <span class="font-medium" x-text="room.checkIn"></span></p>
                                                <p>Until: <span class="font-medium" x-text="room.checkOut"></span></p>
                                            </div>
                                            <button 
                                                @click="cancelAssignment(room)"
                                                class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition text-sm font-medium"
                                            >
                                                ✕ Release
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Empty State --}}
                            <div x-show="getRoomsByStatus(service.id, 'occupied').length === 0" class="text-center py-8">
                                <div class="text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <p class="text-sm font-medium">No occupied rooms</p>
                                    <p class="text-xs mt-1">All rooms are available for assignment</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showConfirmModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">✅</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Assignment</h3>
                    <p class="text-gray-600 mb-6">
                        Assign <span class="font-semibold" x-text="confirmData.customerName"></span> 
                        to Room <span class="font-semibold" x-text="confirmData.roomNumber"></span>?
                    </p>
                    <div class="flex gap-3">
                        <button 
                            @click="processAssignment()"
                            class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition"
                        >
                            Yes, Confirm
                        </button>
                        <button 
                            @click="showConfirmModal = false"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel/Release Modal --}}
    <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showCancelModal = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">⚠️</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Release Room</h3>
                    <p class="text-gray-600 mb-6">
                        Release Room <span class="font-semibold" x-text="cancelData.roomNumber"></span> 
                        from <span class="font-semibold" x-text="cancelData.customerName"></span>?
                    </p>
                    <div class="flex gap-3">
                        <button 
                            @click="processCancel()"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition"
                        >
                            Yes, Release
                        </button>
                        <button 
                            @click="showCancelModal = false"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Toast --}}
    <div 
        x-show="showToast" 
        x-transition
        class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-xl max-w-sm"
    >
        <div class="flex items-center gap-3">
            <span class="text-2xl">✅</span>
            <div>
                <p class="font-semibold" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function roomAssignment() {
    return {
        showLegend: false,
        showConfirmModal: false,
        showCancelModal: false,
        showToast: false,
        toastMessage: '',
        activeTab: 'meeting-room',
        selectedRoom: null,
        selectedCustomer: null,
        confirmData: {},
        cancelData: {},

        services: [
            { id: 'meeting-room', name: 'Meeting Room', icon: '🏢', rooms: '201-205' },
            { id: 'private-office', name: 'Private Office', icon: '🚪', rooms: '301-305' },
            { id: 'sharing-room', name: 'Sharing Room', icon: '👥', rooms: '306-308' }
        ],

        rooms: [],
        customers: [],

        init() {
            this.generateRooms();
            this.generateCustomers();
        },

        generateRooms() {
            const roomsData = [
                { service: 'meeting-room', start: 201, end: 205 },
                { service: 'private-office', start: 301, end: 305 },
                { service: 'sharing-room', start: 306, end: 308 }
            ];

            this.rooms = [];
            const statuses = ['available', 'available', 'available', 'occupied', 'maintenance'];

            roomsData.forEach(config => {
                for (let i = config.start; i <= config.end; i++) {
                    const status = statuses[Math.floor(Math.random() * statuses.length)];
                    const room = {
                        number: i,
                        service: config.service,
                        status: status
                    };

                    // Add customer data for occupied rooms
                    if (status === 'occupied') {
                        room.customerName = this.getRandomName();
                        room.email = this.getRandomEmail();
                        room.orderId = `ORD-${Math.floor(Math.random() * 9000) + 1000}`;
                        room.checkIn = this.getRandomDate(-5, 0);
                        room.checkOut = this.getRandomDate(1, 30);
                    }

                    this.rooms.push(room);
                }
            });
        },

        generateCustomers() {
            const names = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Brown', 'Charlie Wilson', 'Emma Davis', 'Michael Lee', 'Sarah Taylor'];
            const services = ['meeting-room', 'private-office', 'sharing-room'];
            const durations = ['1 Day', '3 Days', '1 Week', '2 Weeks', '1 Month'];

            this.customers = [];

            // Generate 15 customers with settlement status
            for (let i = 0; i < 15; i++) {
                const name = names[Math.floor(Math.random() * names.length)];
                this.customers.push({
                    orderId: `ORD-${Math.floor(Math.random() * 9000) + 1000}`,
                    name: name,
                    email: name.toLowerCase().replace(' ', '.') + '@example.com',
                    phone: `+62812${Math.floor(Math.random() * 90000000 + 10000000)}`,
                    service: services[Math.floor(Math.random() * services.length)],
                    status: 'settlement',
                    duration: durations[Math.floor(Math.random() * durations.length)],
                    checkIn: this.getRandomDate(0, 7),
                    price: Math.floor(Math.random() * 5000000 + 500000)
                });
            }
        },

        getRoomsByService(serviceId) {
            return this.rooms.filter(r => r.service === serviceId);
        },

        getRoomsByStatus(serviceId, status) {
            return this.rooms.filter(r => r.service === serviceId && r.status === status);
        },

        getSettlementCustomers(serviceId) {
            return this.customers.filter(c => c.service === serviceId && c.status === 'settlement');
        },

        selectRoom(room) {
            if (room.status === 'available') {
                this.selectedRoom = room;
                this.selectedCustomer = null;
            }
        },

        confirmAssignment() {
            const customer = this.customers.find(c => c.orderId === this.selectedCustomer);
            if (!customer || !this.selectedRoom) return;

            this.confirmData = {
                roomNumber: this.selectedRoom.number,
                customerName: customer.name
            };
            this.showConfirmModal = true;
        },

        processAssignment() {
            const customer = this.customers.find(c => c.orderId === this.selectedCustomer);
            
            // Update room status
            const roomIndex = this.rooms.findIndex(r => r.number === this.selectedRoom.number);
            this.rooms[roomIndex].status = 'occupied';
            this.rooms[roomIndex].customerName = customer.name;
            this.rooms[roomIndex].email = customer.email;
            this.rooms[roomIndex].orderId = customer.orderId;
            this.rooms[roomIndex].checkIn = customer.checkIn;
            this.rooms[roomIndex].checkOut = this.calculateCheckOut(customer.checkIn, customer.duration);

            // Remove customer from available list
            const customerIndex = this.customers.findIndex(c => c.orderId === this.selectedCustomer);
            this.customers.splice(customerIndex, 1);

            // Reset selection
            this.selectedRoom = null;
            this.selectedCustomer = null;
            this.showConfirmModal = false;

            // Show success toast
            this.toastMessage = `Room ${this.confirmData.roomNumber} assigned successfully!`;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        },

        cancelAssignment(room) {
            this.cancelData = {
                roomNumber: room.number,
                customerName: room.customerName,
                room: room
            };
            this.showCancelModal = true;
        },

        processCancel() {
            const room = this.cancelData.room;
            const roomIndex = this.rooms.findIndex(r => r.number === room.number);

            // Add customer back to available list
            this.customers.push({
                orderId: room.orderId,
                name: room.customerName,
                email: room.email,
                phone: `+62812${Math.floor(Math.random() * 90000000 + 10000000)}`,
                service: room.service,
                status: 'settlement',
                duration: '1 Week',
                checkIn: room.checkIn,
                price: 1500000
            });

            // Reset room to available
            this.rooms[roomIndex].status = 'available';
            delete this.rooms[roomIndex].customerName;
            delete this.rooms[roomIndex].email;
            delete this.rooms[roomIndex].orderId;
            delete this.rooms[roomIndex].checkIn;
            delete this.rooms[roomIndex].checkOut;

            this.showCancelModal = false;

            // Show success toast
            this.toastMessage = `Room ${this.cancelData.roomNumber} released successfully!`;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        },

        calculateCheckOut(checkIn, duration) {
            const date = new Date(checkIn);
            const days = {
                '1 Day': 1,
                '3 Days': 3,
                '1 Week': 7,
                '2 Weeks': 14,
                '1 Month': 30
            };
            date.setDate(date.getDate() + (days[duration] || 7));
            return date.toISOString().split('T')[0];
        },

        getRandomName() {
            const names = ['John Doe', 'Jane Smith', 'Bob Johnson', 'Alice Brown', 'Charlie Wilson'];
            return names[Math.floor(Math.random() * names.length)];
        },

        getRandomEmail() {
            const name = this.getRandomName();
            return name.toLowerCase().replace(' ', '.') + '@example.com';
        },

        getRandomDate(startOffset, endOffset) {
            const today = new Date();
            const days = Math.floor(Math.random() * (endOffset - startOffset + 1)) + startOffset;
            const date = new Date(today.setDate(today.getDate() + days));
            return date.toISOString().split('T')[0];
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        refreshData() {
            this.selectedRoom = null;
            this.selectedCustomer = null;
            this.generateRooms();
            this.generateCustomers();
            
            this.toastMessage = 'Data refreshed successfully!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3000);
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>
@endsection