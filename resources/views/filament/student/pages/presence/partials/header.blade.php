<div class="flex items-center justify-between">
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Presensi Hari Ini
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    @if ($hasPermission)
        {{-- Has Permission Badge --}}
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium 
            {{ $permissionStatus === 'izin' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
            {{ $permissionStatus === 'sakit' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
            {{ $permissionStatus === 'alpa' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
            {{ ucfirst($permissionStatus) }}
        </span>
    @elseif ($presenceToday)
        {{-- Already Present Badge --}}
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            Sudah Absen
        </span>
    @else
        {{-- Not Present Yet Badge --}}
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
            <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            Belum Absen
        </span>
    @endif
</div>
