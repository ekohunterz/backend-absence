<div class="space-y-4">
    {{-- Student Info --}}
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <div class="flex items-center gap-4">
            <img src="{{ $student->avatar_url ?? 'https://www.gravatar.com/avatar/64e1b8d34f425d19e1ee2ea7236d3028?d=mp&r=g&s=250' }}"
                alt="{{ $student->name }}" class="w-16 h-16 rounded-full object-cover" />
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $student->name }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    NIS: {{ $student->nis }} • Kelas Saat Ini: <span
                        class="font-semibold">{{ $student->grade->name }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    @if ($histories->isEmpty())
        <div class="py-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat perpindahan kelas</p>
        </div>
    @else
        <div class="relative">
            {{-- Vertical Line --}}
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

            {{-- History Items --}}
            <div class="space-y-6">
                @foreach ($histories as $history)
                    <div class="relative flex gap-4">
                        {{-- Icon --}}
                        <div
                            class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center z-10
                            {{ $history->new_grade_id ? 'bg-blue-500' : 'bg-green-500' }}">
                            @if ($history->new_grade_id)
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div
                            class="flex-1 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-gray-100">
                                        {{ $history->promotion_type }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $history->promotion_date->format('d F Y') }}
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $history->new_grade_id ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $history->academicYear?->name ?? 'N/A' }}
                                </span>
                            </div>

                            {{-- Grade Change --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="px-3 py-1 rounded-md text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $history->oldGrade?->name ?? 'N/A' }}
                                </span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                <span
                                    class="px-3 py-1 rounded-md text-sm font-medium
                                    {{ $history->new_grade_id ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $history->newGrade?->name ?? 'Alumni' }}
                                </span>
                            </div>

                            {{-- Reason --}}
                            @if ($history->reason)
                                <div class="mb-3">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        <span class="font-medium">Alasan:</span> {{ $history->reason }}
                                    </p>
                                </div>
                            @endif

                            {{-- Notes --}}
                            @if ($history->notes)
                                <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-md mb-3">
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $history->notes }}
                                    </p>
                                </div>
                            @endif

                            {{-- Footer --}}
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>

                                <span class="mx-1">•</span>
                                <span>{{ $history->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium mb-1">Ringkasan Riwayat</p>
                    <p>Total perpindahan kelas: <span class="font-semibold">{{ $histories->count() }}</span></p>
                </div>
            </div>
        </div>
    @endif
</div>
