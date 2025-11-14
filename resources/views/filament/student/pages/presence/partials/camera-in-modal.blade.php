<x-filament::modal id="check-in-modal" width="2xl" :close-by-clicking-away="false" class="!z-[9999]">
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-camera" class="w-6 h-6 text-primary-600" />
            <span>Ambil Foto Selfie</span>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Camera Preview --}}
        <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;" id="cam-container">
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
