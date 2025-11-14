<div id="location-info" class="mt-6">
    <div
        class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 flex justify-between">
        <div class="flex items-start gap-3 ">
            <x-filament::icon icon="heroicon-s-map-pin"
                class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
            <div class="flex-1 space-y-1">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Lokasi Anda</p>
                <p id="current-coords" class="text-xs text-blue-700 dark:text-blue-300 mt-1"></p>
                <p id="distance-info" class="text-xs text-blue-700 dark:text-blue-300 mt-1"></p>
            </div>
        </div>
        <div class="flex items-start gap-3 ">
            <x-filament::icon icon="heroicon-s-clock"
                class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-2 flex-shrink-0" />
            <div>
                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Jam Sekolah</p>
                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                    {{ \Carbon\Carbon::parse($setting->start_time)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($setting->end_time)->format('H:i') }}</p>

            </div>
        </div>
    </div>
</div>
