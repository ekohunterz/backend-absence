<div class="flex flex-col gap-4 space-y-4">
    @if ($semesters && $semesters->count() > 0)
        @foreach ($semesters as $semester)
            <div
                class="p-4 rounded-lg border {{ $semester->is_active ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3
                            class="text-lg font-semibold {{ $semester->is_active ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">
                            {{ $semester->name }}
                        </h3>
                        <p
                            class="text-sm {{ $semester->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }} mt-1">
                            {{ \Carbon\Carbon::parse($semester->start_date)->format('d F Y') }} -
                            {{ \Carbon\Carbon::parse($semester->end_date)->format('d F Y') }}
                        </p>
                    </div>

                    @if ($semester->is_active)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Aktif
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tipe Semester</p>
                        <p
                            class="text-sm font-medium {{ $semester->is_active ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">
                            Semester {{ $semester->semester }} ({{ $semester->semester == 1 ? 'Ganjil' : 'Genap' }})
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Absensi</p>
                        <p
                            class="text-sm font-medium {{ $semester->is_active ? 'text-green-900 dark:text-green-100' : 'text-gray-900 dark:text-gray-100' }}">
                            {{ $semester->attendances()->count() }} hari
                        </p>
                    </div>
                </div>

                @if ($semester->description)
                    <div
                        class="mt-3 pt-3 border-t {{ $semester->is_active ? 'border-green-200 dark:border-green-800' : 'border-gray-200 dark:border-gray-700' }}">
                        <p
                            class="text-xs {{ $semester->is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $semester->description }}
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm">Belum ada semester</p>
        </div>
    @endif
</div>
