<x-filament-widgets::widget>
    <x-filament::section>
        <div class="relative overflow-hidden">
            {{-- Gradient Background --}}
            <div class="absolute inset-0 bg-gradient-to-br {{ $this->getCardGradient() }} opacity-5"></div>

            {{-- Decorative Circles --}}
            <div
                class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br {{ $this->getCardGradient() }} rounded-full opacity-10 blur-2xl">
            </div>
            <div
                class="absolute -bottom-10 -left-10 w-40 h-40 bg-gradient-to-br {{ $this->getCardGradient() }} rounded-full opacity-10 blur-2xl">
            </div>

            <div class="relative p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-gradient-to-br {{ $this->getCardGradient() }} shadow-lg">
                            <x-filament::icon :icon="$statusIcon" class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                Status Absensi Hari Ini
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold {{ $this->getStatusBadgeColor() }}">
                        <x-filament::icon :icon="$statusIcon" class="w-4 h-4" />
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Status Message --}}
                <div class="mb-6">
                    <p class="text-base text-gray-700 dark:text-gray-300">
                        {{ $statusMessage }}
                    </p>
                </div>

                {{-- Time Details --}}
                @if ($status === 'hadir')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        {{-- Check-In Time --}}
                        @if ($checkInTime)
                            <div
                                class="flex items-center gap-3 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                <div class="p-2 rounded-lg bg-green-500 shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-xs font-medium text-green-700 dark:text-green-300 uppercase tracking-wide">
                                        Absen Masuk
                                    </p>
                                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                        {{ $checkInTime }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Check-Out Time --}}
                        @if ($checkOutTime)
                            <div
                                class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                <div class="p-2 rounded-lg bg-blue-500 shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wide">
                                        Absen Keluar
                                    </p>
                                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                        {{ $checkOutTime }}
                                    </p>
                                </div>
                            </div>
                        @elseif($checkInTime)
                            <div
                                class="flex items-center gap-3 p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800">
                                <div class="p-2 rounded-lg bg-orange-500 shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p
                                        class="text-xs font-medium text-orange-700 dark:text-orange-300 uppercase tracking-wide">
                                        Absen Keluar
                                    </p>
                                    <p class="text-sm font-semibold text-orange-900 dark:text-orange-100">
                                        Belum Absen Keluar
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Notes --}}
                @if ($notes)
                    <div
                        class="mb-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start gap-3">
                            <x-filament::icon icon="heroicon-o-chat-bubble-left-right"
                                class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                    Catatan
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Action Button --}}
                @php
                    $actionButton = $this->getActionButton();
                @endphp

                @if ($actionButton)
                    <div
                        class="flex items-center justify-between p-4 rounded-lg bg-gradient-to-r {{ $this->getCardGradient() }}">
                        <div class="flex items-center gap-3 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium">
                                @if ($status === 'belum_absen')
                                    Segera lakukan check-in agar tidak tercatat alpa
                                @else
                                    Jangan lupa untuk check-out sebelum pulang
                                @endif
                            </span>
                        </div>
                        <a href="{{ $actionButton['url'] }}" wire:navigate
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg text-sm font-semibold transition-all duration-200 hover:scale-105 hover:shadow-lg"
                            style="color: {{ $statusColor === 'success' ? '#16a34a' : ($statusColor === 'warning' ? '#ca8a04' : ($statusColor === 'info' ? '#2563eb' : '#dc2626')) }}">
                            {{ $actionButton['label'] }}
                            <x-filament::icon :icon="$actionButton['icon']" class="w-4 h-4" />
                        </a>
                    </div>
                @endif

                {{-- Info untuk status izin/sakit/alpa --}}
                @if (in_array($status, ['izin', 'sakit', 'alpa']))
                    <div
                        class="mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Status ini telah diatur oleh admin/wali kelas Anda</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
