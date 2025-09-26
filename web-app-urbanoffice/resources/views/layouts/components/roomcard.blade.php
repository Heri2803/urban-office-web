{{-- resources/views/layouts/components/roomcard.blade.php --}}

@php
    // Data untuk setiap room type dengan gambar dan benefit yang spesifik
    $allRoomData = $allRoomData ?? [
        'Virtual Office' => [
            'images' => [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop&auto=format',
                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=800&h=600&fit=crop&auto=format'
            ],
            'benefits' => [
                'Alamat bisnis prestisius untuk perusahaan Anda',
                'Layanan resepsionis profesional dan penerimaan surat',
                'Akses ke meeting room dan fasilitas kantor premium'
            ],
        ],
        'Meeting Room' => [
            'images' => [
                'https://images.unsplash.com/photo-1556761175-b413da4baf72?w=800&h=600&fit=crop&auto=format',
                'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=800&h=600&fit=crop&auto=format'
            ],
            'benefits' => [
                'Ruang meeting modern dengan kapasitas 8-12 orang',
                'Dilengkapi proyektor, whiteboard, dan sistem audio visual',
                'Wi-Fi berkecepatan tinggi dan layanan catering tersedia'
            ],
        ],
        'Coworking Space' => [
            'images' => [
                'https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=800&h=600&fit=crop&auto=format',
                'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&h=600&fit=crop&auto=format'
            ],
            'benefits' => [
                'Workspace fleksibel dengan suasana kerja yang produktif',
                'Akses 24/7 dengan keamanan terjamin dan cleaning service',
                'Komunitas profesional dan networking opportunities'
            ],
        ],
        'Event Space' => [
            'images' => [
                'https://images.unsplash.com/photo-1505236858219-8359eb29e329?w=800&h=600&fit=crop&auto=format',
                'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=600&fit=crop&auto=format'
            ],
            'benefits' => [
                'Ruang event luas untuk seminar, workshop, dan acara besar',
                'Kapasitas hingga 100 orang dengan tata letak fleksibel',
                'Sound system profesional, lighting, dan dukungan teknis'
            ],
        ]
    ];
    $roomType = $roomType ?? 'Virtual Office';
@endphp

<div 
    x-data="roomCardComponent(@js($allRoomData), @js($roomType))" 
    x-init="init()" 
    x-cloak
    class="bg-white shadow-lg hover:shadow-xl rounded-xl overflow-hidden transition-all duration-700 ease-out transform"
    :class="isVisible ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 translate-y-8 scale-95'"
>
    <!-- Image slider -->
    <div class="relative h-48 sm:h-56 md:h-64 lg:h-72 overflow-hidden">
        <div 
            class="flex transition-transform duration-500 ease-out h-full"
            :style="`transform: translateX(-${currentImageIndex * 100}%)`">
            
            <template x-for="(image, index) in currentRoom.images" :key="index">
                <div class="w-full h-full flex-shrink-0">
                    <img 
                        :src="image" 
                        :alt="`${roomType} ${index + 1}`" 
                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                        x-on:error.once="handleError($event)"
                        loading="lazy"
                    >
                </div>
            </template>
        </div>

        <!-- Navigation buttons - hidden on mobile, shown on tablet+ -->
        <template x-if="currentRoom.images && currentRoom.images.length > 1">
            <div>
                <!-- Prev button -->
                <button 
                    @click="prevImage" 
                    class="absolute left-2 sm:left-3 top-1/2 transform -translate-y-1/2 
                           bg-black/40 hover:bg-black/60 backdrop-blur-sm text-white 
                           rounded-full p-1.5 sm:p-2 transition-all duration-200 
                           opacity-0 hover:opacity-100 group-hover:opacity-100
                           focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-orange-500"
                    aria-label="Previous image"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Next button -->
                <button 
                    @click="nextImage" 
                    class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2 
                           bg-black/40 hover:bg-black/60 backdrop-blur-sm text-white 
                           rounded-full p-1.5 sm:p-2 transition-all duration-200
                           opacity-0 hover:opacity-100 group-hover:opacity-100
                           focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-orange-500"
                    aria-label="Next image"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </template>

        <!-- Dots indicator -->
        <div x-show="currentRoom.images && currentRoom.images.length > 1" 
             class="absolute bottom-3 sm:bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <template x-for="(image, index) in currentRoom.images" :key="index">
                <button 
                    @click="setImage(index)" 
                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-orange-500"
                    :class="index === currentImageIndex ? 'bg-orange-500 scale-125' : 'bg-white/60 hover:bg-white/80'"
                    :aria-label="`Go to image ${index + 1}`">
                </button>
            </template>
        </div>

        <!-- Room type badge -->
        <div class="absolute top-3 sm:top-4 left-3 sm:left-4">
            <span class="bg-orange-500 text-white px-2 py-1 sm:px-3 sm:py-1.5 rounded-full text-xs sm:text-sm font-medium shadow-lg backdrop-blur-sm"
                  x-text="roomType">
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 sm:p-5 md:p-6">
        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-4 sm:mb-5 border-l-4 border-orange-500 pl-3 sm:pl-4" 
            x-text="roomType">
        </h3>
        
        <!-- Benefits -->
        <div class="space-y-3 sm:space-y-4">
            <template x-for="(benefit, index) in currentRoom.benefits" :key="index">
                <div 
                    class="flex items-start space-x-3 sm:space-x-4 transition-all duration-500 ease-out group"
                    :class="isVisible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-4'"
                    :style="`transition-delay: ${(index + 1) * 150}ms`"
                >
                    <div class="flex-shrink-0 w-2 h-2 sm:w-2.5 sm:h-2.5 bg-orange-500 rounded-full mt-2 sm:mt-2.5 
                                transition-transform duration-200 group-hover:scale-125"></div>
                    <p class="text-gray-700 text-sm sm:text-base md:text-lg leading-relaxed 
                             transition-colors duration-200 group-hover:text-gray-900" 
                       x-text="benefit"></p>
                </div>
            </template>
        </div>

        <!-- Extra Info -->
        <div 
            class="mt-5 sm:mt-6 p-3 sm:p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg 
                   transition-all duration-500 ease-out hover:shadow-md border-l-2 border-orange-200"
            :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            :style="`transition-delay: ${(currentRoom.benefits.length + 1) * 150}ms`"
        >
            <p class="text-orange-800 text-sm sm:text-base font-medium flex items-center">
                <span class="text-lg mr-2">✨</span>
                Fasilitas premium dengan standar internasional
            </p>
        </div>

        <!-- Additional features for larger screens -->
        <div class="hidden md:block mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        Available Now
                    </span>
                </div>
                <div class="text-orange-600 font-semibold">
                    Premium Quality
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function roomCardComponent(allRoomData, roomType) {
    return {
        allRoomData,
        roomType,
        currentRoom: {},
        currentImageIndex: 0,
        isVisible: false,
        autoSlideInterval: null,

        init() {
            this.updateRoom();
            // Trigger entrance animation
            this.$nextTick(() => {
                setTimeout(() => {
                    this.isVisible = true;
                }, 100);
            });
            
            // Start auto-slide for images if more than 1
            this.startAutoSlide();
        },

        updateRoom() {
            this.currentRoom = this.allRoomData[this.roomType] || this.allRoomData['Virtual Office'];
            this.currentImageIndex = 0;
        },

        nextImage() {
            if (this.currentRoom.images && this.currentRoom.images.length > 1) {
                this.currentImageIndex = (this.currentImageIndex + 1) % this.currentRoom.images.length;
            }
        },

        prevImage() {
            if (this.currentRoom.images && this.currentRoom.images.length > 1) {
                this.currentImageIndex = this.currentImageIndex === 0 
                    ? this.currentRoom.images.length - 1 
                    : this.currentImageIndex - 1;
            }
        },

        setImage(index) {
            this.currentImageIndex = index;
            this.resetAutoSlide();
        },

        startAutoSlide() {
            if (this.currentRoom.images && this.currentRoom.images.length > 1) {
                this.autoSlideInterval = setInterval(() => {
                    this.nextImage();
                }, 5000); // Change image every 5 seconds
            }
        },

        resetAutoSlide() {
            if (this.autoSlideInterval) {
                clearInterval(this.autoSlideInterval);
                this.startAutoSlide();
            }
        },

        handleError(event) {
            console.log('Image failed to load:', event.target.src);
            // You can set a fallback image here if needed
            event.target.src = '/images/fallback-room.jpg';
        },

        // Method to be called from parent component when room type changes
        changeRoomType(newRoomType) {
            this.roomType = newRoomType;
            this.updateRoom();
            this.resetAutoSlide();
        }
    }
}
</script>