<x-filament::modal id="check-out-modal" width="2xl" :close-by-clicking-away="false" class="!z-[9999]">
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-camera" class="w-6 h-6 text-orange-600" />
            <span>Presensi Keluar - Ambil Foto Selfie</span>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Camera Preview --}}
        <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;"
            id="cam-container-checkout">
            <video id="camera-preview-checkout" autoplay playsinline class="w-full h-full object-cover"></video>

            <canvas id="photo-canvas-checkout" class="hidden"></canvas>

            {{-- Camera Overlay Guide --}}
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div class="w-56 h-64 border-4 border-orange-500/50 rounded-full"></div>
            </div>

            {{-- Camera Error --}}
            <div id="camera-error-checkout" class="hidden absolute inset-0  items-center justify-center bg-gray-900">
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
