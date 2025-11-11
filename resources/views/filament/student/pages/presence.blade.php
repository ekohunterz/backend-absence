<x-filament-panels::page>

    <div class="space-y-4">
        {{-- Header Info --}}
        <div class="flex items-center justify-between ">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Presensi Hari Ini
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            @if ($presenceToday)
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Sudah Absen
                    </span>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        Belum Absen
                    </span>
                </div>
            @endif
        </div>

        {{-- Map Container --}}
        <div class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 mt-4">
            <div id="map" class="w-full h-64" wire:ignore style="width: auto; height: 300px"></div>

            {{-- Loading Overlay --}}
            <div id="map-loading" class="absolute inset-0 bg-white dark:bg-gray-900 flex items-center justify-center">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Memuat peta...</p>
                </div>
            </div>
        </div>

        {{-- Location Info --}}
        <div id="location-info" class="hidden mt-6">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start gap-3 ">
                    <x-filament::icon icon="heroicon-s-map-pin"
                        class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                    <div class="flex-1 space-y-1">
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Lokasi Anda</p>
                        <p id="current-coords" class="text-xs text-blue-700 dark:text-blue-300 mt-1"></p>
                        <p id="distance-info" class="text-xs text-blue-700 dark:text-blue-300 mt-1"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Error Message --}}
        <div id="location-error" class="hidden mt-4">
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-900 dark:text-red-100">Gagal Mengakses Lokasi</p>
                        <p id="error-message" class="text-xs text-red-700 dark:text-red-300 mt-1"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Presence Button --}}

        {{-- Presence Status & Actions --}}
        @if (!$presenceToday)
            {{-- Belum Check-In --}}
            <div class="space-y-3 mt-6">
                <div id="check-in-btn" onclick="openCheckInModal()"
                    class="relative overflow-hidden group cursor-pointer rounded-xl border-2 border-primary-500 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                    </div>

                    <div class="relative p-6 flex items-center justify-center gap-3">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center group-hover:rotate-12 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-xl font-bold text-white uppercase tracking-wide">
                                <span id="btn-text">Memuat lokasi...</span>
                            </p>
                            <p class="text-sm text-white/80 mt-1">
                                Tap untuk melakukan presensi masuk
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-white group-hover:translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Pastikan Anda berada dalam radius {{ $setting->radius ?? 100 }} meter dari sekolah</span>
                </div>
            </div>
        @else
            {{-- Sudah Check-In --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                {{-- Check-In Card --}}
                <div
                    class="relative overflow-hidden rounded-xl border-2 border-green-200 dark:border-green-800 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-green-200/30 dark:bg-green-700/20 rounded-full -mr-16 -mt-16">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-24 h-24 bg-green-200/20 dark:bg-green-700/10 rounded-full -ml-12 -mb-12">
                    </div>

                    <div class="relative p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <p
                                        class="text-sm font-semibold text-green-900 dark:text-green-100 uppercase tracking-wide">
                                        Presensi Masuk
                                    </p>
                                    <span class="px-2 py-0.5 text-xs font-medium bg-green-500 text-white rounded-full">
                                        ✓ Tercatat
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                    {{ $presenceToday->check_in_time ? \Carbon\Carbon::parse($presenceToday->check_in_time)->format('H:i') : 'N/A' }}
                                </p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    {{ \Carbon\Carbon::parse($presenceToday->check_in_time)->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Check-Out Card --}}
                @if (!$presenceToday->check_out_time)
                    {{-- Button Check-Out --}}
                    <div id="check-out-btn" onclick="openCheckOutModal()"
                        class="relative overflow-hidden group cursor-pointer rounded-xl border-2 border-orange-500 bg-gradient-to-br from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
                        </div>

                        <div class="relative p-5 h-full flex flex-col justify-center">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center group-hover:rotate-12 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-white uppercase tracking-wide mb-1">
                                        Presensi Keluar
                                    </p>
                                    <p class="text-lg font-bold text-white">
                                        <span id="checkout-btn-text">Tap untuk Check-Out</span>
                                    </p>
                                    <p class="text-xs text-white/80 mt-1">
                                        Jangan lupa presensi keluar
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-white group-hover:translate-x-1 transition-transform duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Already Checked Out --}}
                    <div
                        class="relative overflow-hidden rounded-xl border-2 border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-blue-200/30 dark:bg-blue-700/20 rounded-full -mr-16 -mt-16">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-24 h-24 bg-blue-200/20 dark:bg-blue-700/10 rounded-full -ml-12 -mb-12">
                        </div>

                        <div class="relative p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p
                                            class="text-sm font-semibold text-blue-900 dark:text-blue-100 uppercase tracking-wide">
                                            Presensi Keluar
                                        </p>
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium bg-blue-500 text-white rounded-full">
                                            ✓ Tercatat
                                        </span>
                                    </div>
                                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                        {{ $presenceToday->check_out_time ? \Carbon\Carbon::parse($presenceToday->check_out_time)->format('H:i') : 'N/A' }}
                                    </p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        {{ \Carbon\Carbon::parse($presenceToday->check_out_time)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>



        @endif

    </div>

    {{-- Camera Modal --}}
    <x-filament::modal id="check-in-modal" width="2xl" :close-by-clicking-away="false" class="!z-[9999]">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-camera" class="w-6 h-6 text-primary-600" />
                <span>Ambil Foto Selfie</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            {{-- Camera Preview --}}
            <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                <video id="camera-preview" autoplay playsinline class="w-full h-full object-cover"></video>

                <canvas id="photo-canvas" class="hidden"></canvas>

                {{-- Camera Overlay Guide --}}
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-56 h-64 border-4 border-white/50 rounded-full"></div>
                </div>

                {{-- Camera Error --}}
                <div id="camera-error" class="hidden absolute inset-0 items-center justify-center bg-gray-900">
                    <div class="text-center text-white p-6">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle"
                            class="w-16 h-16 mx-auto mb-4 text-red-400" />
                        <p class="text-sm" id="camera-error-text">Gagal mengakses kamera</p>
                    </div>
                </div>
            </div>

            {{-- Photo Preview (after capture) --}}
            <div id="photo-preview-container" class="hidden">
                <img id="photo-preview" src="" alt="Preview"
                    class="w-full rounded-lg border-2 border-gray-300 dark:border-gray-700">
            </div>

            {{-- Instructions --}}
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex gap-3">
                    <x-filament::icon icon="heroicon-o-information-circle"
                        class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-900 dark:text-blue-100">
                        <p class="font-medium mb-2">Petunjuk:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-blue-800 dark:text-blue-200">
                            <li>Pastikan wajah Anda terlihat jelas</li>
                            <li>Posisikan wajah di tengah lingkaran</li>
                            <li>Pastikan pencahayaan cukup</li>
                            <li>Jangan gunakan masker atau kacamata hitam</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <x-filament::button id="capture-btn" onclick="capturePhoto()" color="primary" class="flex-1"
                    icon="heroicon-o-camera">
                    Ambil Foto
                </x-filament::button>

                <x-filament::button id="retake-btn" onclick="retakePhoto()" color="gray" class="hidden flex-1"
                    icon="heroicon-o-arrow-path">
                    Ulangi
                </x-filament::button>

                <x-filament::button id="submit-btn" onclick="submitCheckIn()" color="success" class="hidden flex-1"
                    icon="heroicon-o-check">
                    <span id="submit-btn-text">Check In</span>
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>

    {{-- Check-Out Modal --}}
    <x-filament::modal id="check-out-modal" width="2xl" :close-by-clicking-away="false" class="!z-[9999]">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-camera" class="w-6 h-6 text-orange-600" />
                <span>Presensi Keluar - Ambil Foto Selfie</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            {{-- Camera Preview --}}
            <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                <video id="camera-preview-checkout" autoplay playsinline class="w-full h-full object-cover"></video>

                <canvas id="photo-canvas-checkout" class="hidden"></canvas>

                {{-- Camera Overlay Guide --}}
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-56 h-64 border-4 border-orange-500/50 rounded-full"></div>
                </div>

                {{-- Camera Error --}}
                <div id="camera-error-checkout"
                    class="hidden absolute inset-0  items-center justify-center bg-gray-900">
                    <div class="text-center text-white p-6">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle"
                            class="w-16 h-16 mx-auto mb-4 text-red-400" />
                        <p class="text-sm" id="camera-error-text-checkout">Gagal mengakses kamera</p>
                    </div>
                </div>
            </div>

            {{-- Photo Preview (after capture) --}}
            <div id="photo-preview-container-checkout" class="hidden">
                <img id="photo-preview-checkout" src="" alt="Preview"
                    class="w-full rounded-lg border-2 border-orange-300 dark:border-orange-700">
            </div>

            {{-- Instructions --}}
            <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <div class="flex gap-3">
                    <x-filament::icon icon="heroicon-o-information-circle"
                        class="w-5 h-5 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-orange-900 dark:text-orange-100">
                        <p class="font-medium mb-2">Petunjuk Check-Out:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-orange-800 dark:text-orange-200">
                            <li>Pastikan wajah Anda terlihat jelas</li>
                            <li>Posisikan wajah di tengah lingkaran</li>
                            <li>Foto akan digunakan untuk verifikasi kehadiran</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <x-filament::button id="capture-btn-checkout" onclick="capturePhotoCheckout()" color="warning"
                    class="flex-1" icon="heroicon-o-camera">
                    Ambil Foto
                </x-filament::button>

                <x-filament::button id="retake-btn-checkout" onclick="retakePhotoCheckout()" color="gray"
                    class="hidden flex-1" icon="heroicon-o-arrow-path">
                    Ulangi
                </x-filament::button>

                <x-filament::button id="submit-checkout-btn" onclick="submitCheckOut()" color="success"
                    class="hidden flex-1" icon="heroicon-o-check">
                    <span id="submit-checkout-btn-text">Check Out</span>
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>

    @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endassets

    @script
        <script type="text/javascript">
            let schoolLat = {{ $setting->latitude }};
            let schoolLng = {{ $setting->longitude }};
            let radius = {{ $setting->radius }};

            let userMarker;
            let schoolMarker;
            let radiusCircle;
            let userLocation = null;
            let cameraStream = null;
            let capturedPhoto = null;

            let map = L.map('map').setView([schoolLat, schoolLng], 16);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // School marker
            const schoolIcon = L.divIcon({
                html: `<div class="flex items-center justify-center w-10 h-10 bg-red-600 rounded-full border-4 border-white shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                    </svg>
                </div>`,
                className: '',
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            schoolMarker = L.marker([schoolLat, schoolLng], {
                    icon: schoolIcon
                })
                .addTo(map)
                .bindPopup('<b>Lokasi Sekolah</b><br>Radius presensi');

            // Radius circle
            radiusCircle = L.circle([schoolLat, schoolLng], {
                color: 'blue',
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                radius: radius
            }).addTo(map);

            // Calculate distance between two points (Haversine formula)
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3;
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;

                const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                    Math.cos(φ1) * Math.cos(φ2) *
                    Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c;
            }

            // Update user location
            function updateUserLocation(lat, lng) {
                userLocation = {
                    lat,
                    lng
                };

                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                const userIcon = L.divIcon({
                    html: `<div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-full border-4 border-white shadow-lg animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#0039e3" class="size-6">
                         <path fill-rule="evenodd" d="m11.54 22.351.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                        </svg>
                    </div>`,
                    className: '',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                userMarker = L.marker([lat, lng], {
                        icon: userIcon
                    })
                    .addTo(map)
                    .bindPopup('<b>Lokasi Anda</b>');

                const distance = calculateDistance(schoolLat, schoolLng, lat, lng);

                document.getElementById('current-coords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                document.getElementById('distance-info').textContent = `Jarak dari sekolah: ${distance.toFixed(0)} meter`;
                document.getElementById('location-info').classList.remove('hidden');

                const checkInBtn = document.getElementById('check-in-btn');
                const btnText = document.getElementById('btn-text');

                if (distance <= radius) {
                    checkInBtn.disabled = false;
                    btnText.textContent = 'Check In Sekarang';
                } else {
                    checkInBtn.disabled = true;
                    btnText.textContent = `Terlalu jauh (${distance.toFixed(0)}m)`;
                }

                const bounds = L.latLngBounds([
                    [schoolLat, schoolLng],
                    [lat, lng]
                ]);
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            // Get user location
            function getUserLocation() {
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            document.getElementById('map-loading').classList.add('hidden');
                            updateUserLocation(position.coords.latitude, position.coords.longitude);
                        },
                        (error) => {
                            document.getElementById('map-loading').classList.add('hidden');
                            document.getElementById('location-error').classList.remove('hidden');

                            let errorMsg = 'Gagal mendapatkan lokasi. ';
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMsg += 'Izin lokasi ditolak. Aktifkan izin lokasi di browser Anda.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMsg += 'Informasi lokasi tidak tersedia.';
                                    break;
                                case error.TIMEOUT:
                                    errorMsg += 'Waktu permintaan lokasi habis.';
                                    break;
                                default:
                                    errorMsg += 'Terjadi kesalahan tidak dikenal.';
                            }

                            document.getElementById('error-message').textContent = errorMsg;
                            document.getElementById('btn-text').textContent = 'Lokasi tidak tersedia';
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                } else {
                    document.getElementById('map-loading').classList.add('hidden');
                    document.getElementById('location-error').classList.remove('hidden');
                    document.getElementById('error-message').textContent = 'Browser Anda tidak mendukung geolocation.';
                }
            }

            // Camera functions
            window.openCheckInModal = function() {
                if (!userLocation) {
                    alert('Lokasi belum terdeteksi');
                    return;
                }

                $wire.dispatch('open-modal', {
                    id: 'check-in-modal'
                });

                setTimeout(() => {
                    startCamera();
                }, 300);
            }

            async function startCamera() {
                try {
                    const video = document.getElementById('camera-preview');
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 960
                            }
                        }
                    });
                    video.srcObject = cameraStream;
                    document.getElementById('camera-error').classList.add('hidden');
                } catch (err) {
                    console.error('Camera error:', err);
                    document.getElementById('camera-error').classList.remove('hidden');
                    document.getElementById('camera-error-text').textContent =
                        'Gagal mengakses kamera. Pastikan izin kamera diaktifkan.';
                }
            }

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }
            }

            window.capturePhoto = function() {
                const video = document.getElementById('camera-preview');
                const canvas = document.getElementById('photo-canvas');
                const context = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);

                document.getElementById('photo-preview').src = capturedPhoto;
                document.getElementById('photo-preview-container').classList.remove('hidden');
                document.getElementById('camera-preview').classList.add('hidden');

                document.getElementById('capture-btn').classList.add('hidden');
                document.getElementById('retake-btn').classList.remove('hidden');
                document.getElementById('submit-btn').classList.remove('hidden');

                stopCamera();
            }

            window.retakePhoto = function() {
                capturedPhoto = null;

                document.getElementById('photo-preview-container').classList.add('hidden');
                document.getElementById('camera-preview').classList.remove('hidden');

                document.getElementById('capture-btn').classList.remove('hidden');
                document.getElementById('retake-btn').classList.add('hidden');
                document.getElementById('submit-btn').classList.add('hidden');

                startCamera();
            }

            window.submitCheckIn = function() {
                if (!capturedPhoto || !userLocation) {
                    alert('Foto atau lokasi belum tersedia');
                    return;
                }

                const submitBtn = document.getElementById('submit-btn');
                const submitBtnText = document.getElementById('submit-btn-text');

                submitBtn.disabled = true;
                submitBtnText.textContent = 'Memproses...';

                $wire.call('checkIn', userLocation.lat, userLocation.lng, capturedPhoto)
                    .then(() => {
                        stopCamera();
                        $wire.dispatch('close-modal', {
                            id: 'check-in-modal'
                        });

                        setTimeout(() => {
                            resetModal();
                        }, 500);
                    })
                    .catch((error) => {
                        console.error('Check-in error:', error);
                        submitBtn.disabled = false;
                        submitBtnText.textContent = 'Check In';
                    });
            }

            function resetModal() {
                capturedPhoto = null;
                document.getElementById('photo-preview-container').classList.add('hidden');
                document.getElementById('camera-preview').classList.remove('hidden');
                document.getElementById('capture-btn').classList.remove('hidden');
                document.getElementById('retake-btn').classList.add('hidden');
                document.getElementById('submit-btn').classList.add('hidden');
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = false;
                document.getElementById('submit-btn-text').textContent = 'Check In';
            }

            // Cleanup on modal close
            Livewire.on('close-modal', (event) => {
                if (event.id === 'check-in-modal') {
                    stopCamera();
                    resetModal();
                }
            });

            // Initialize
            getUserLocation();



            // Check-Out Modal Function
            window.openCheckOutModal = function() {
                if (!userLocation) {
                    alert('Lokasi belum terdeteksi');
                    return;
                }

                $wire.dispatch('open-modal', {
                    id: 'check-out-modal'
                });

                setTimeout(() => {
                    startCamera();
                }, 300);
            }

            // Submit Check-Out
            window.submitCheckOut = function() {
                if (!capturedPhoto || !userLocation) {
                    alert('Foto atau lokasi belum tersedia');
                    return;
                }

                const submitBtn = document.getElementById('submit-checkout-btn');
                const submitBtnText = document.getElementById('submit-checkout-btn-text');

                submitBtn.disabled = true;
                submitBtnText.textContent = 'Memproses...';

                $wire.call('checkOut', userLocation.lat, userLocation.lng, capturedPhoto)
                    .then(() => {
                        stopCamera();
                        $wire.dispatch('close-modal', {
                            id: 'check-out-modal'
                        });

                        setTimeout(() => {
                            resetModal();
                        }, 500);
                    })
                    .catch((error) => {
                        console.error('Check-out error:', error);
                        submitBtn.disabled = false;
                        submitBtnText.textContent = 'Check Out';
                    });
            } // Separate camera streams
            let cameraStreamCheckout = null;
            let capturedPhotoCheckout = null;

            window.openCheckOutModal = function() {
                if (!userLocation) {
                    alert('Lokasi belum terdeteksi');
                    return;
                }

                $wire.dispatch('open-modal', {
                    id: 'check-out-modal'
                });

                setTimeout(() => {
                    startCameraCheckout();
                }, 300);
            }

            async function startCameraCheckout() {
                try {
                    const video = document.getElementById('camera-preview-checkout');
                    cameraStreamCheckout = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 960
                            }
                        }
                    });
                    video.srcObject = cameraStreamCheckout;
                    document.getElementById('camera-error-checkout').classList.add('hidden');
                } catch (err) {
                    console.error('Camera error:', err);
                    document.getElementById('camera-error-checkout').classList.remove('hidden');
                    document.getElementById('camera-error-text-checkout').textContent =
                        'Gagal mengakses kamera. Pastikan izin kamera diaktifkan.';
                }
            }

            function stopCameraCheckout() {
                if (cameraStreamCheckout) {
                    cameraStreamCheckout.getTracks().forEach(track => track.stop());
                    cameraStreamCheckout = null;
                }
            }

            window.capturePhotoCheckout = function() {
                const video = document.getElementById('camera-preview-checkout');
                const canvas = document.getElementById('photo-canvas-checkout');
                const context = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                capturedPhotoCheckout = canvas.toDataURL('image/jpeg', 0.8);

                document.getElementById('photo-preview-checkout').src = capturedPhotoCheckout;
                document.getElementById('photo-preview-container-checkout').classList.remove('hidden');
                document.getElementById('camera-preview-checkout').classList.add('hidden');

                document.getElementById('capture-btn-checkout').classList.add('hidden');
                document.getElementById('retake-btn-checkout').classList.remove('hidden');
                document.getElementById('submit-checkout-btn').classList.remove('hidden');

                stopCameraCheckout();
            }

            window.retakePhotoCheckout = function() {
                capturedPhotoCheckout = null;

                document.getElementById('photo-preview-container-checkout').classList.add('hidden');
                document.getElementById('camera-preview-checkout').classList.remove('hidden');

                document.getElementById('capture-btn-checkout').classList.remove('hidden');
                document.getElementById('retake-btn-checkout').classList.add('hidden');
                document.getElementById('submit-checkout-btn').classList.add('hidden');

                startCameraCheckout();
            }

            window.submitCheckOut = function() {
                if (!capturedPhotoCheckout || !userLocation) {
                    alert('Foto atau lokasi belum tersedia');
                    return;
                }

                const submitBtn = document.getElementById('submit-checkout-btn');
                const submitBtnText = document.getElementById('submit-checkout-btn-text');

                submitBtn.disabled = true;
                submitBtnText.textContent = 'Memproses...';

                $wire.call('checkOut', userLocation.lat, userLocation.lng, capturedPhotoCheckout)
                    .then(() => {
                        stopCameraCheckout();
                        $wire.dispatch('close-modal', {
                            id: 'check-out-modal'
                        });

                        setTimeout(() => {
                            resetModalCheckout();
                        }, 500);
                    })
                    .catch((error) => {
                        console.error('Check-out error:', error);
                        submitBtn.disabled = false;
                        submitBtnText.textContent = 'Check Out';
                    });
            }

            function resetModalCheckout() {
                capturedPhotoCheckout = null;
                document.getElementById('photo-preview-container-checkout').classList.add('hidden');
                document.getElementById('camera-preview-checkout').classList.remove('hidden');
                document.getElementById('capture-btn-checkout').classList.remove('hidden');
                document.getElementById('retake-btn-checkout').classList.add('hidden');
                document.getElementById('submit-checkout-btn').classList.add('hidden');
                const submitBtn = document.getElementById('submit-checkout-btn');
                submitBtn.disabled = false;
                document.getElementById('submit-checkout-btn-text').textContent = 'Check Out';
            }

            // Cleanup on modal close
            Livewire.on('close-modal', (event) => {
                if (event.id === 'check-out-modal') {
                    stopCameraCheckout();
                    resetModalCheckout();
                }
            });
        </script>
    @endscript
</x-filament-panels::page>
