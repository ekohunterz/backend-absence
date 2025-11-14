<div class="mt-6">
    <div
        class="relative overflow-hidden rounded-xl border-2 
        {{ $permissionStatus === 'izin' ? 'border-yellow-200 dark:border-yellow-800 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-900/10' : '' }}
        {{ $permissionStatus === 'sakit' ? 'border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10' : '' }}
        {{ $permissionStatus === 'alpa' ? 'border-red-200 dark:border-red-800 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/10' : '' }}">

        {{-- Decorative circles --}}
        <div
            class="absolute top-0 right-0 w-32 h-32 
            {{ $permissionStatus === 'izin' ? 'bg-yellow-200/30 dark:bg-yellow-700/20' : '' }}
            {{ $permissionStatus === 'sakit' ? 'bg-blue-200/30 dark:bg-blue-700/20' : '' }}
            {{ $permissionStatus === 'alpa' ? 'bg-red-200/30 dark:bg-red-700/20' : '' }}
            rounded-full -mr-16 -mt-16">
        </div>
        <div
            class="absolute bottom-0 left-0 w-24 h-24 
            {{ $permissionStatus === 'izin' ? 'bg-yellow-200/20 dark:bg-yellow-700/10' : '' }}
            {{ $permissionStatus === 'sakit' ? 'bg-blue-200/20 dark:bg-blue-700/10' : '' }}
            {{ $permissionStatus === 'alpa' ? 'bg-red-200/20 dark:bg-red-700/10' : '' }}
            rounded-full -ml-12 -mb-12">
        </div>

        <div class="relative p-8 text-center">
            <div class="flex justify-center mb-4">
                <div
                    class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg
                    {{ $permissionStatus === 'izin' ? 'bg-yellow-500' : '' }}
                    {{ $permissionStatus === 'sakit' ? 'bg-blue-500' : '' }}
                    {{ $permissionStatus === 'alpa' ? 'bg-red-500' : '' }}">
                    @if ($permissionStatus === 'izin')
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif($permissionStatus === 'sakit')
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>
            </div>

            <h3
                class="text-2xl font-bold mb-2
                {{ $permissionStatus === 'izin' ? 'text-yellow-900 dark:text-yellow-100' : '' }}
                {{ $permissionStatus === 'sakit' ? 'text-blue-900 dark:text-blue-100' : '' }}
                {{ $permissionStatus === 'alpa' ? 'text-red-900 dark:text-red-100' : '' }}">
                Anda {{ ucfirst($permissionStatus) }} Hari Ini
            </h3>

            <p
                class="text-sm mb-4
                {{ $permissionStatus === 'izin' ? 'text-yellow-700 dark:text-yellow-300' : '' }}
                {{ $permissionStatus === 'sakit' ? 'text-blue-700 dark:text-blue-300' : '' }}
                {{ $permissionStatus === 'alpa' ? 'text-red-700 dark:text-red-300' : '' }}">
                @if ($permissionStatus === 'izin')
                    Anda telah mengajukan izin untuk tidak hadir hari ini
                @elseif($permissionStatus === 'sakit')
                    Anda telah melaporkan sakit untuk hari ini
                @else
                    Anda tidak melakukan presensi dan belum ada keterangan
                @endif
            </p>

            @if ($permissionReason)
                <div
                    class="mt-4 p-4 rounded-lg
                    {{ $permissionStatus === 'izin' ? 'bg-yellow-100/50 dark:bg-yellow-900/30' : '' }}
                    {{ $permissionStatus === 'sakit' ? 'bg-blue-100/50 dark:bg-blue-900/30' : '' }}
                    {{ $permissionStatus === 'alpa' ? 'bg-red-100/50 dark:bg-red-900/30' : '' }}">
                    <p
                        class="text-xs font-medium mb-1
                        {{ $permissionStatus === 'izin' ? 'text-yellow-800 dark:text-yellow-200' : '' }}
                        {{ $permissionStatus === 'sakit' ? 'text-blue-800 dark:text-blue-200' : '' }}
                        {{ $permissionStatus === 'alpa' ? 'text-red-800 dark:text-red-200' : '' }}">
                        Catatan:
                    </p>
                    <p
                        class="text-sm
                        {{ $permissionStatus === 'izin' ? 'text-yellow-700 dark:text-yellow-300' : '' }}
                        {{ $permissionStatus === 'sakit' ? 'text-blue-700 dark:text-blue-300' : '' }}
                        {{ $permissionStatus === 'alpa' ? 'text-red-700 dark:text-red-300' : '' }}">
                        {{ $permissionReason }}
                    </p>
                </div>
            @endif

            <div
                class="mt-6 flex items-center justify-center gap-2 text-xs
                {{ $permissionStatus === 'izin' ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                {{ $permissionStatus === 'sakit' ? 'text-blue-600 dark:text-blue-400' : '' }}
                {{ $permissionStatus === 'alpa' ? 'text-red-600 dark:text-red-400' : '' }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                <span>Dicatat pada {{ $permissionRecordedAt }}</span>
            </div>
        </div>
    </div>


</div>
