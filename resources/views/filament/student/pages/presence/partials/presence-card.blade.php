<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    {{-- Check-In Card --}}
    <div
        class="relative overflow-hidden rounded-xl border-2 border-green-200 dark:border-green-800 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/10">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-200/30 dark:bg-green-700/20 rounded-full -mr-16 -mt-16">
        </div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-green-200/20 dark:bg-green-700/10 rounded-full -ml-12 -mb-12">
        </div>

        <div class="relative p-5">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-sm font-semibold text-green-900 dark:text-green-100 uppercase tracking-wide">
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
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Already Checked Out --}}
        <div
            class="relative overflow-hidden rounded-xl border-2 border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200/30 dark:bg-blue-700/20 rounded-full -mr-16 -mt-16">
            </div>
            <div
                class="absolute bottom-0 left-0 w-24 h-24 bg-blue-200/20 dark:bg-blue-700/10 rounded-full -ml-12 -mb-12">
            </div>

            <div class="relative p-5">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 uppercase tracking-wide">
                                Presensi Keluar
                            </p>
                            <span class="px-2 py-0.5 text-xs font-medium bg-blue-500 text-white rounded-full">
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
