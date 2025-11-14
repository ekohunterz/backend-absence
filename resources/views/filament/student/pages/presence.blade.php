<x-filament-panels::page>

    <div class="space-y-4">
        {{-- Header Info --}}
        @include('filament.student.pages.presence.partials.header')

        {{-- Map Container --}}
        @include('filament.student.pages.presence.partials.map')

        {{-- Location Info --}}
        @include('filament.student.pages.presence.partials.location-info')

        {{-- Error Message --}}
        @include('filament.student.pages.presence.partials.location-error')

        {{-- Presence Button --}}
        @if ($hasPermission)
            {{-- Permission Notice --}}
            @include('filament.student.pages.presence.partials.permission-notice')


            {{-- Presence Status & Actions --}}
        @elseif (!$presenceToday)
            {{-- Belum Check-In --}}
            @include('filament.student.pages.presence.partials.check-in-button')
        @else
            {{-- Sudah Check-In --}}
            @include('filament.student.pages.presence.partials.presence-card')
        @endif

    </div>

    {{-- Camera Modal --}}
    @include('filament.student.pages.presence.partials.camera-in-modal')

    {{-- Check-Out Modal --}}
    @include('filament.student.pages.presence.partials.camera-out-modal')

    @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endassets

    @script
        <script type="text/javascript">
            // Configuration
            SCHOOL_LAT = {{ $setting->latitude }};
            SCHOOL_LNG = {{ $setting->longitude }};
            RADIUS = {{ $setting->radius }};

            // Global state
            let map, userMarker, schoolMarker, radiusCircle;
            let userLocation = null;
            let cameraStreams = {
                checkIn: null,
                checkOut: null
            };
            let capturedPhotos = {
                checkIn: null,
                checkOut: null
            };

            // Initialize map
            function initMap() {
                map = L.map('map').setView([SCHOOL_LAT, SCHOOL_LNG], 16);

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

                schoolMarker = L.marker([SCHOOL_LAT, SCHOOL_LNG], {
                        icon: schoolIcon
                    })
                    .addTo(map)
                    .bindPopup('<b>Lokasi Sekolah</b><br>Radius presensi');

                // Radius circle
                radiusCircle = L.circle([SCHOOL_LAT, SCHOOL_LNG], {
                    color: 'blue',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.1,
                    radius: RADIUS
                }).addTo(map);

                getUserLocation();
            }

            // Calculate distance
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

                const distance = calculateDistance(SCHOOL_LAT, SCHOOL_LNG, lat, lng);

                document.getElementById('current-coords').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                document.getElementById('distance-info').textContent = `Jarak dari sekolah: ${distance.toFixed(0)} meter`;
                document.getElementById('location-info').classList.remove('hidden');


                updateButtons(distance);

                const bounds = L.latLngBounds([
                    [SCHOOL_LAT, SCHOOL_LNG],
                    [lat, lng]
                ]);
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            // Update buttons
            function updateButtons(distance) {
                const checkInBtn = document.getElementById('check-in-btn');
                const btnText = document.getElementById('btn-text');
                const checkOutBtn = document.getElementById('check-out-btn');
                const checkoutBtnText = document.getElementById('checkout-btn-text');

                const isWithinRadius = distance <= RADIUS;

                if (checkInBtn && btnText) {
                    checkInBtn.disabled = !isWithinRadius;
                    btnText.textContent = isWithinRadius ? 'Check In Sekarang' : `Terlalu jauh (${distance.toFixed(0)}m)`;
                }

                if (checkOutBtn && checkoutBtnText) {
                    checkOutBtn.disabled = !isWithinRadius;
                    checkoutBtnText.textContent = isWithinRadius ? 'Tap untuk Check-Out' :
                        `Terlalu jauh (${distance.toFixed(0)}m)`;
                }
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
                                    errorMsg += 'Izin lokasi ditolak.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMsg += 'Informasi lokasi tidak tersedia.';
                                    break;
                                case error.TIMEOUT:
                                    errorMsg += 'Waktu permintaan lokasi habis.';
                                    break;
                            }

                            document.getElementById('error-message').textContent = errorMsg;
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
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
                setTimeout(() => startCamera('checkIn'), 300);
            }

            window.openCheckOutModal = function() {
                if (!userLocation) {
                    alert('Lokasi belum terdeteksi');
                    return;
                }
                $wire.dispatch('open-modal', {
                    id: 'check-out-modal'
                });
                setTimeout(() => startCamera('checkOut'), 300);
            }

            async function startCamera(type) {
                const videoId = type === 'checkIn' ? 'camera-preview' : 'camera-preview-checkout';
                const errorId = type === 'checkIn' ? 'camera-error' : 'camera-error-checkout';

                try {
                    cameraStreams[type] = await navigator.mediaDevices.getUserMedia({
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
                    document.getElementById(videoId).srcObject = cameraStreams[type];
                    document.getElementById(errorId).classList.add('hidden');
                } catch (err) {
                    document.getElementById(errorId).classList.remove('hidden');
                }
            }

            function stopCamera(type) {
                if (cameraStreams[type]) {
                    cameraStreams[type].getTracks().forEach(track => track.stop());
                    cameraStreams[type] = null;
                }
            }

            window.capturePhoto = function() {
                capturePhotoInternal('checkIn');
            }
            window.capturePhotoCheckout = function() {
                capturePhotoInternal('checkOut');
            }

            function capturePhotoInternal(type) {
                const videoId = type === 'checkIn' ? 'camera-preview' : 'camera-preview-checkout';
                const canvasId = type === 'checkIn' ? 'photo-canvas' : 'photo-canvas-checkout';
                const previewId = type === 'checkIn' ? 'photo-preview' : 'photo-preview-checkout';
                const previewContainerId = type === 'checkIn' ? 'photo-preview-container' : 'photo-preview-container-checkout';
                const captureBtnId = type === 'checkIn' ? 'capture-btn' : 'capture-btn-checkout';
                const retakeBtnId = type === 'checkIn' ? 'retake-btn' : 'retake-btn-checkout';
                const submitBtnId = type === 'checkIn' ? 'submit-btn' : 'submit-checkout-btn';
                const camContainerId = type === 'checkIn' ? 'cam-container' : 'cam-container-checkout';

                const video = document.getElementById(videoId);
                const canvas = document.getElementById(canvasId);
                const context = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);

                capturedPhotos[type] = canvas.toDataURL('image/jpeg', 0.8);

                document.getElementById(previewId).src = capturedPhotos[type];
                document.getElementById(previewContainerId).classList.remove('hidden');
                document.getElementById(videoId).classList.add('hidden');
                document.getElementById(captureBtnId).classList.add('hidden');
                document.getElementById(retakeBtnId).classList.remove('hidden');
                document.getElementById(submitBtnId).classList.remove('hidden');
                document.getElementById(camContainerId).classList.add('hidden');

                stopCamera(type);
            }

            window.retakePhoto = function() {
                retakePhotoInternal('checkIn');
            }
            window.retakePhotoCheckout = function() {
                retakePhotoInternal('checkOut');
            }

            function retakePhotoInternal(type) {
                const videoId = type === 'checkIn' ? 'camera-preview' : 'camera-preview-checkout';
                const previewContainerId = type === 'checkIn' ? 'photo-preview-container' : 'photo-preview-container-checkout';
                const captureBtnId = type === 'checkIn' ? 'capture-btn' : 'capture-btn-checkout';
                const retakeBtnId = type === 'checkIn' ? 'retake-btn' : 'retake-btn-checkout';
                const submitBtnId = type === 'checkIn' ? 'submit-btn' : 'submit-checkout-btn';
                const camContainerId = type === 'checkIn' ? 'cam-container' : 'cam-container-checkout';

                capturedPhotos[type] = null;

                document.getElementById(previewContainerId).classList.add('hidden');
                document.getElementById(videoId).classList.remove('hidden');
                document.getElementById(captureBtnId).classList.remove('hidden');
                document.getElementById(retakeBtnId).classList.add('hidden');
                document.getElementById(submitBtnId).classList.add('hidden');
                document.getElementById(camContainerId).classList.remove('hidden');

                startCamera(type);
            }

            window.submitCheckIn = function() {
                if (!capturedPhotos.checkIn || !userLocation) {
                    alert('Foto atau lokasi belum tersedia');
                    return;
                }

                const submitBtn = document.getElementById('submit-btn');
                const submitText = document.getElementById('submit-btn-text');

                submitBtn.disabled = true;
                submitText.textContent = 'Memproses...';

                $wire.call('checkIn', userLocation.lat, userLocation.lng, capturedPhotos.checkIn)
                    .then(() => {
                        stopCamera('checkIn');
                        $wire.dispatch('close-modal', {
                            id: 'check-in-modal'
                        });
                        setTimeout(() => resetModal('checkIn'), 500);
                    })
                    .catch(() => {
                        submitBtn.disabled = false;
                        submitText.textContent = 'Check In';
                    });
            }

            window.submitCheckOut = function() {
                if (!capturedPhotos.checkOut || !userLocation) {
                    alert('Foto atau lokasi belum tersedia');
                    return;
                }

                const submitBtn = document.getElementById('submit-checkout-btn');
                const submitText = document.getElementById('submit-checkout-btn-text');

                submitBtn.disabled = true;
                submitText.textContent = 'Memproses...';

                $wire.call('checkOut', userLocation.lat, userLocation.lng, capturedPhotos.checkOut)
                    .then(() => {
                        stopCamera('checkOut');
                        $wire.dispatch('close-modal', {
                            id: 'check-out-modal'
                        });
                        setTimeout(() => resetModal('checkOut'), 500);
                    })
                    .catch(() => {
                        submitBtn.disabled = false;
                        submitText.textContent = 'Check Out';
                    });
            }

            function resetModal(type) {
                capturedPhotos[type] = null;
                const videoId = type === 'checkIn' ? 'camera-preview' : 'camera-preview-checkout';
                const previewContainerId = type === 'checkIn' ? 'photo-preview-container' : 'photo-preview-container-checkout';
                const captureBtnId = type === 'checkIn' ? 'capture-btn' : 'capture-btn-checkout';
                const retakeBtnId = type === 'checkIn' ? 'retake-btn' : 'retake-btn-checkout';
                const submitBtnId = type === 'checkIn' ? 'submit-btn' : 'submit-checkout-btn';

                document.getElementById(previewContainerId).classList.add('hidden');
                document.getElementById(videoId).classList.remove('hidden');
                document.getElementById(captureBtnId).classList.remove('hidden');
                document.getElementById(retakeBtnId).classList.add('hidden');
                document.getElementById(submitBtnId).classList.add('hidden');
            }

            // Cleanup on modal close
            Livewire.on('close-modal', (event) => {
                if (event.id === 'check-in-modal') {
                    stopCamera('checkIn');
                    resetModal('checkIn');
                } else if (event.id === 'check-out-modal') {
                    stopCamera('checkOut');
                    resetModal('checkOut');
                }
            });

            // Initialize
            initMap();
        </script>
    @endscript
</x-filament-panels::page>
