<aside
  x-data="{
    isMobile: window.innerWidth < 768,
    mobileActive: null,
    init() {
      window.addEventListener('resize', () => this.isMobile = window.innerWidth < 768);
    },
    toggleMobile(section) {
      this.mobileActive = this.mobileActive === section ? null : section;
    }
  }"
  x-init="init()"
  class="z-50"
>

  {{-- DESKTOP / TABLET SIDEBAR --}}
  <div
    class="hidden md:flex md:flex-col fixed left-0 top-0 h-screen w-64 bg-white border-r border-gray-200 text-gray-700 shadow-md no-scrollbar"
  >
    {{-- Header / Logo --}}
    <div class="p-4 border-b">
      <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-2">
        <img src="{{ asset('assets/LOGO_URBAN_OFFICE.png') }}" alt="Logo" class="h-24">
        <span class="font-semibold text-orange-600">Superadmin Panel</span>
      </a>
    </div>

    {{-- Nav --}}
    <nav class="p-4 flex-1 overflow-y-auto space-y-4">
        {{-- 📊 Dashboard (sekarang di bawah the new sections) --}}
      <div x-data="{ open: false }">
        <a href="#"
           class="block px-3 py-2 rounded-md hover:bg-gray-50 font-semibold">📊 Dashboard</a>
      </div>
      {{-- 🤝 Mitra Management (NEW!) --}}
      <div x-data="{ open: false }">
        <button @click="open = !open"
          class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">🤝 Mitra Management 
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.mitra.all') }}" class="block px-2 py-1 rounded hover:bg-gray-100">All Mitra</a>
          <a href="{{ route('superadmin.mitra.revenue-mitra') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Revenue Share Report</a>
        </div>
      </div>

      {{-- 🏢 Branch Management (Updated) --}}
      <div x-data="{ open: false }">
        <button @click="open = !open"
          class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">🏢 Branch Management 
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.branches.all') }}" class="block px-2 py-1 rounded hover:bg-gray-100">All Branches (Grouped by Mitra)</a>
        </div>
      </div>

      {{-- 👥 User Management (Updated) --}}
      <div x-data="{ open: false }">
        <button @click="open = !open"
          class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">👥 User Management <span class="ml-2 text-xs bg-indigo-600 text-white px-2 py-0.5 rounded">Updated</span></span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.user-management.user-manage-all') }}" class="block px-2 py-1 rounded hover:bg-gray-100">All Admins (Filter by Mitra/Branch)</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Add Admin</a>
          <a href="{{ route('superadmin.user-management.user-activitylog-manage') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Activity Log</a>
        </div>
      </div>

      {{-- 📅 Booking --}}
      <div x-data="{ open: false }">
        <button @click="open = !open"
          class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">📅 Booking</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.booking.all') }}" class="block px-2 py-1 rounded hover:bg-gray-100">All Bookings (All Branches)</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Booking Statistics</a>
          <a href="{{ route('superadmin.booking.reports') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Export Reports</a>
        </div>
      </div>

      {{-- selanjutnya: Room Management, Content, Pricing, Voucher, Reports, Approval, Settings, Activity, Profile --}}
      {{-- Room Management --}}
      <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">🏢 Room Management</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.room-management.all') }}" class="block px-2 py-1 rounded hover:bg-gray-100">All Rooms (All Branches)</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Room Statistics</a>
        </div>
      </div>

      {{-- Content Management --}}
      <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">🎨 Content Management</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.content-management.photos') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Service Photos (Global)</a>
          <a href="{{ route('superadmin.content-management.banner') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Banner Promo (Global)</a>
          <a href="{{ route('superadmin.content-management.highlight') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Service Highlights (Global)</a>
        </div>
      </div>

      {{-- Pricing Management --}}
      <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">💰 Pricing Management</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.pricing-management.master') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Master Pricing</a>
          <a href="{{ route('superadmin.pricing-management.branch') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Branch Pricing Override</a>
          <a href="{{ route('superadmin.pricing-management.approval') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Approval Requests</a>
        </div>
      </div>

      {{-- Voucher & Promo --}}
      <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">🎟️ Voucher & Promo</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.voucher-promo.create') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Create Voucher</a>
          <a href="{{ route('superadmin.voucher-promo.active') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Active Vouchers</a>
          <a href="{{ route('superadmin.voucher-promo.usage') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Usage Reports</a>
        </div>
      </div>

      {{-- Reports & Analytics --}}
      <!-- <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">📊 Reports & Analytics</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.reports.revenue') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Revenue Reports</a>
          <a href="{{ route('superadmin.reports.occupancy') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Occupancy Reports</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Customer Reports</a>
        </div>
      </div> -->

      {{-- Approval Center --}}
      <!-- <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">✅ Approval Center</span>
          <span class="ml-2 text-xs bg-red-600 text-white px-2 py-0.5 rounded-full">3</span>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="{{ route('superadmin.approval.pending') }}" class="block px-2 py-1 rounded hover:bg-gray-100">Pending Approvals</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Approval History</a>
        </div>
      </div> -->

      {{-- System Settings --}}
      <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">⚙️ System Settings</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">General Settings</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Payment Gateway</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Notifications</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Integrations</a>
        </div>
      </div>

      {{-- Activity Log --}}
      <!-- <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50">
          <span class="font-semibold">📜 Activity Log</span>
          <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-cloak x-transition class="pl-4 mt-2 space-y-1 text-sm">
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">Audit Trail</a>
          <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100">System Logs</a>
        </div>
      </div> -->
    </nav>
  </div>

  {{-- MOBILE BOTTOM BAR --}}
  <div class="md:hidden fixed left-0 right-0 bottom-0 z-50">
    <div class="bg-white border-t border-gray-200 flex items-center justify-between px-2 py-1 space-x-1 overflow-x-auto no-scrollbar">
      {{-- Buttons for main sections (order: Mitra, Branch, User, Dashboard, Booking, ...) --}}
      <button @click="toggleMobile('mitra')"
        class="flex-0 px-3 py-2 text-xs text-center rounded-md hover:bg-gray-50">
        🤝<div class="text-[10px]">Mitra</div>
      </button>

      <button @click="toggleMobile('branch')"
        class="flex-0 px-3 py-2 text-xs text-center rounded-md hover:bg-gray-50">
        🏢<div class="text-[10px]">Branch</div>
      </button>

      <button @click="toggleMobile('user')"
        class="flex-0 px-3 py-2 text-xs text-center rounded-md hover:bg-gray-50">
        👥<div class="text-[10px]">Users</div>
      </button>

      <button @click="toggleMobile('dashboard')"
        class="flex-0 px-3 py-2 text-xs text-center rounded-md hover:bg-gray-50">
        📊<div class="text-[10px]">Dashboard</div>
      </button>

      <button @click="toggleMobile('booking')"
        class="flex-0 px-3 py-2 text-xs text-center rounded-md hover:bg-gray-50">
        📅<div class="text-[10px]">Booking</div>
      </button>

      <button @click="toggleMobile('more')"
        class="flex-0 px-3 py-2 text-xs text-center rounded-md hover:bg-gray-50">
        ⋯<div class="text-[10px]">More</div>
      </button>
    </div>

    {{-- Mobile panel — muncul ketika mobileActive diset --}}
    <div x-show="mobileActive" x-cloak x-transition class="fixed left-0 right-0 bottom-12 bg-white border-t border-gray-200 z-50 shadow-lg max-h-[60vh] overflow-y-auto no-scrollbar">
      <div class="p-3 flex items-center justify-between border-b">
        <strong class="text-sm">
          <span x-text="mobileActive === 'mitra' ? '🤝 Mitra Management' : (mobileActive === 'branch' ? '🏢 Branch Management' : (mobileActive === 'user' ? '👥 User Management' : (mobileActive === 'dashboard' ? '📊 Dashboard' : (mobileActive === 'booking' ? '📅 Booking' : 'More'))))"></span>
        </strong>
        <button @click="mobileActive = null" class="text-gray-600">Close</button>
      </div>

      <div class="p-3">
        {{-- konten dinamis berdasarkan mobileActive --}}
        <template x-if="mobileActive === 'mitra'">
          <div class="space-y-2">
            <a href="{{ route('superadmin.mitra.all') }}" class="block px-2 py-2 rounded hover:bg-gray-50">All Mitra</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Add New Mitra</a>
            <a href="{{ route('superadmin.mitra.revenue-mitra') }}" class="block px-2 py-2 rounded hover:bg-gray-50">Revenue Share Report</a>
          </div>
        </template>

        <template x-if="mobileActive === 'branch'">
          <div class="space-y-2">
            <a href="{{ route('superadmin.branches.all') }}" class="block px-2 py-2 rounded hover:bg-gray-50">All Branches (Grouped by Mitra)</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Add New Branch</a>
            <a href="{{ route('superadmin.branches.settings') }}" class="block px-2 py-2 rounded hover:bg-gray-50">Branch Settings</a>
          </div>
        </template>

        <template x-if="mobileActive === 'user'">
          <div class="space-y-2">
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">All Admins (Filter by Mitra/Branch)</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Add Admin</a>
            <a href="{{ route('superadmin.user-management.user-activitylog-manage') }}" class="block px-2 py-2 rounded hover:bg-gray-50">Activity Log</a>
          </div>
        </template>

        <template x-if="mobileActive === 'dashboard'">
          <div>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">📊 Dashboard</a>
          </div>
        </template>

        <template x-if="mobileActive === 'booking'">
          <div class="space-y-2">
            <a href="{{ route('superadmin.booking.all') }}" class="block px-2 py-2 rounded hover:bg-gray-50">All Bookings (All Branches)</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Booking Statistics</a>
            <a href="{{ route('superadmin.booking.reports') }}" class="block px-2 py-2 rounded hover:bg-gray-50">Export Reports</a>
          </div>
        </template>

        <template x-if="mobileActive === 'more'">
          <div class="space-y-2">
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Room Management</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Content Management</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Pricing Management</a>
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Voucher & Promo</a>
            <!-- <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Reports & Analytics</a> -->
            <!-- <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Approval Center</a> -->
            <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">System Settings</a>
            <!-- <a href="#" class="block px-2 py-2 rounded hover:bg-gray-50">Activity Log</a> -->
          </div>
        </template>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // Toggle Branch Groups
          window.toggleGroup = function(groupId) {
              const content = document.getElementById(groupId + '-content');
              const icon = document.getElementById(groupId + '-icon');
              
              if (content.style.display === 'none') {
                  content.style.display = 'block';
                  icon.classList.remove('rotate-[-90deg]');
                  icon.classList.add('rotate-0');
              } else {
                  content.style.display = 'none';
                  icon.classList.remove('rotate-0');
                  icon.classList.add('rotate-[-90deg]');
              }
          };

          // Dynamic Branch Filter based on Mitra
          const mitraFilter = document.getElementById('mitraFilter');
          const branchFilter = document.getElementById('branchFilter');

          const branchData = {
              '1': [
                  { value: '1', text: 'Surabaya - Gubeng' },
                  { value: '2', text: 'Surabaya - HR Muhammad' },
                  { value: '3', text: 'Sidoarjo - Delta' }
              ],
              '2': [
                  { value: '4', text: 'Jakarta - Senayan' },
                  { value: '5', text: 'Jakarta - Sudirman' }
              ],
              '3': [
                  { value: '6', text: 'Bandung - Dago' },
                  { value: '7', text: 'Bandung - Riau' }
              ]
          };

          if (mitraFilter && branchFilter) {
              mitraFilter.addEventListener('change', function() {
                  const mitraId = this.value;
                  
                  // Clear and add default option
                  branchFilter.innerHTML = '<option value="">All Branches</option>';
                  
                  // Add branches based on selected mitra
                  if (mitraId && branchData[mitraId]) {
                      branchData[mitraId].forEach(branch => {
                          const option = document.createElement('option');
                          option.value = branch.value;
                          option.textContent = branch.text;
                          branchFilter.appendChild(option);
                      });
                  }

                  // Show notification
                  showNotification('Branch filter updated based on selected Mitra', 'info');
              });
          }

          // View Room Details Modal
          const viewRoomBtns = document.querySelectorAll('.viewRoomBtn');
          const roomDetailModal = document.getElementById('roomDetailModal');
          const closeRoomDetailBtns = document.querySelectorAll('.closeRoomDetailBtn');

          viewRoomBtns.forEach(btn => {
              btn.addEventListener('click', function(e) {
                  e.preventDefault();
                  roomDetailModal.classList.remove('hidden');
                  document.body.style.overflow = 'hidden';
                  
                  // Simulate loading room data
                  showNotification('Loading room details...', 'info');
                  setTimeout(() => {
                      showNotification('Room details loaded successfully', 'success');
                  }, 500);
              });
          });

          closeRoomDetailBtns.forEach(btn => {
              btn.addEventListener('click', function(e) {
                  e.preventDefault();
                  roomDetailModal.classList.add('hidden');
                  document.body.style.overflow = 'auto';
              });
          });

          // Close modal on backdrop click
          roomDetailModal?.addEventListener('click', function(e) {
              if (e.target === roomDetailModal) {
                  roomDetailModal.classList.add('hidden');
                  document.body.style.overflow = 'auto';
              }
          });

          // Apply Filter Button
          const applyFilterBtn = document.querySelector('.bg-blue-600.text-white');
          if (applyFilterBtn && applyFilterBtn.textContent.includes('Apply')) {
              applyFilterBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  
                  // Get filter values
                  const mitra = document.getElementById('mitraFilter')?.value;
                  const branch = document.getElementById('branchFilter')?.value;
                  const serviceType = document.querySelector('select[class*="focus:ring-blue-500"]:nth-of-type(3)')?.value;
                  const status = document.querySelector('select[class*="focus:ring-blue-500"]:nth-of-type(4)')?.value;
                  const search = document.querySelector('input[placeholder*="Room number"]')?.value;

                  // Build filter message
                  let filterMsg = 'Applying filters: ';
                  let filters = [];
                  
                  if (mitra) {
                      const mitraText = document.getElementById('mitraFilter').selectedOptions[0].text;
                      filters.push(`Mitra: ${mitraText}`);
                  }
                  if (branch) {
                      const branchText = document.getElementById('branchFilter').selectedOptions[0].text;
                      filters.push(`Branch: ${branchText}`);
                  }
                  if (serviceType) filters.push(`Service: ${serviceType}`);
                  if (status) filters.push(`Status: ${status}`);
                  if (search) filters.push(`Search: ${search}`);

                  if (filters.length > 0) {
                      filterMsg += filters.join(', ');
                  } else {
                      filterMsg = 'No filters applied - showing all rooms';
                  }

                  showNotification(filterMsg, 'success');

                  // Simulate filtering
                  setTimeout(() => {
                      showNotification('Rooms filtered successfully', 'success');
                  }, 800);
              });
          }

          // Reset Filter Button
          const resetFilterBtn = document.querySelector('.bg-gray-200.text-gray-700');
          if (resetFilterBtn && resetFilterBtn.textContent.includes('Reset')) {
              resetFilterBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  
                  // Reset all filters
                  if (mitraFilter) mitraFilter.value = '';
                  if (branchFilter) branchFilter.innerHTML = '<option value="">All Branches</option>';
                  
                  const selects = document.querySelectorAll('select[class*="focus:ring-blue-500"]');
                  selects.forEach(select => {
                      if (select.id !== 'mitraFilter' && select.id !== 'branchFilter') {
                          select.value = '';
                      }
                  });
                  
                  const searchInput = document.querySelector('input[placeholder*="Room number"]');
                  if (searchInput) searchInput.value = '';

                  showNotification('All filters have been reset', 'info');
              });
          }

          // Group By Change
          const groupBySelect = document.querySelector('select[class*="focus:ring-blue-500"]');
          if (groupBySelect) {
              groupBySelect.addEventListener('change', function() {
                  const groupType = this.value;
                  showNotification(`Grouping rooms by: ${groupType}`, 'info');
                  
                  // Simulate regrouping
                  setTimeout(() => {
                      showNotification('Rooms regrouped successfully', 'success');
                  }, 500);
              });
          }

          // Sort By Change
          const sortBySelect = document.querySelectorAll('select[class*="focus:ring-blue-500"]')[1];
          if (sortBySelect) {
              sortBySelect.addEventListener('change', function() {
                  const sortType = this.value;
                  showNotification(`Sorting rooms by: ${sortType}`, 'info');
                  
                  // Simulate sorting
                  setTimeout(() => {
                      showNotification('Rooms sorted successfully', 'success');
                  }, 500);
              });
          }

          // Status Dashboard Button
          const statusDashboardBtn = document.querySelector('button[class*="bg-blue-600"]:first-of-type');
          if (statusDashboardBtn && statusDashboardBtn.textContent.includes('Dashboard')) {
              statusDashboardBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  showNotification('Opening Room Status Dashboard...', 'info');
                  
                  setTimeout(() => {
                      showNotification('Feature coming soon!', 'warning');
                  }, 800);
              });
          }

          // Quick Actions in Room Detail Modal
          const setMaintenanceBtn = document.querySelector('.bg-orange-100');
          const viewCalendarBtn = document.querySelector('.bg-blue-100');
          const editDetailsBtn = document.querySelector('.bg-purple-100');

          if (setMaintenanceBtn) {
              setMaintenanceBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  if (confirm('Are you sure you want to set this room to maintenance mode?')) {
                      showNotification('Room set to maintenance mode', 'warning');
                      setTimeout(() => {
                          roomDetailModal.classList.add('hidden');
                          document.body.style.overflow = 'auto';
                      }, 1500);
                  }
              });
          }

          if (viewCalendarBtn) {
              viewCalendarBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  showNotification('Opening full calendar view...', 'info');
                  setTimeout(() => {
                      showNotification('Feature coming soon!', 'warning');
                  }, 800);
              });
          }

          if (editDetailsBtn) {
              editDetailsBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  showNotification('Opening edit form...', 'info');
                  setTimeout(() => {
                      showNotification('Feature coming soon!', 'warning');
                  }, 800);
              });
          }

          // Pagination Buttons
          const paginationBtns = document.querySelectorAll('button[class*="border-gray-300"]');
          paginationBtns.forEach(btn => {
              btn.addEventListener('click', function(e) {
                  e.preventDefault();
                  if (!this.disabled) {
                      const pageText = this.textContent.trim();
                      showNotification(`Loading page: ${pageText}`, 'info');
                      
                      // Simulate page load
                      setTimeout(() => {
                          showNotification('Page loaded successfully', 'success');
                          window.scrollTo({ top: 0, behavior: 'smooth' });
                      }, 500);
                  }
              });
          });

          // Search Input - Real-time search simulation
          const searchInput = document.querySelector('input[placeholder*="Room number"]');
          let searchTimeout;
          
          if (searchInput) {
              searchInput.addEventListener('input', function() {
                  clearTimeout(searchTimeout);
                  
                  const query = this.value;
                  
                  if (query.length > 0) {
                      searchTimeout = setTimeout(() => {
                          showNotification(`Searching for: ${query}...`, 'info');
                      }, 500);
                  }
              });
          }

          // Notification Function
          function showNotification(message, type = 'info') {
              // Remove existing notification
              const existingNotif = document.getElementById('notification');
              if (existingNotif) {
                  existingNotif.remove();
              }

              // Create notification element
              const notification = document.createElement('div');
              notification.id = 'notification';
              notification.className = 'fixed top-4 right-4 z-[60] px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in';
              
              // Set colors based on type
              const colors = {
                  success: 'bg-green-500 text-white',
                  error: 'bg-red-500 text-white',
                  warning: 'bg-orange-500 text-white',
                  info: 'bg-blue-500 text-white'
              };
              
              notification.className += ' ' + (colors[type] || colors.info);
              
              // Set icon based on type
              const icons = {
                  success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                  error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                  warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                  info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
              };
              
              notification.innerHTML = `
                  ${icons[type] || icons.info}
                  <span>${message}</span>
              `;
              
              document.body.appendChild(notification);
              
              // Auto remove after 3 seconds
              setTimeout(() => {
                  notification.style.opacity = '0';
                  notification.style.transform = 'translateX(100%)';
                  setTimeout(() => {
                      notification.remove();
                  }, 300);
              }, 3000);
          }

          // Add CSS animation for notification
          const style = document.createElement('style');
          style.textContent = `
              @keyframes slide-in {
                  from {
                      transform: translateX(100%);
                      opacity: 0;
                  }
                  to {
                      transform: translateX(0);
                      opacity: 1;
                  }
              }
              .animate-slide-in {
                  animation: slide-in 0.3s ease-out;
              }
              #notification {
                  transition: all 0.3s ease-out;
              }
          `;
          document.head.appendChild(style);

          console.log('All Rooms page initialized successfully!');
      });
  </script>

  {{-- utility CSS: hide scrollbar --}}
  <style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* small tweak: prevent page horizontal scrolling on mobile due to bottom bar */
    @media (max-width: 767px) {
      html, body { overflow-x: hidden !important; }
    }
  </style>
</aside>
