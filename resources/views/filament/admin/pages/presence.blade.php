<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    Pilih Kelas
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Pilih kelas untuk melakukan absensi hari ini
                </p>
            </div>

            <div class="w-full md:w-auto">
                {{ $this->form }}
            </div>
        </div>

        <!-- Statistics Cards -->
        @php
            $totalGrades = count($grades);
            $attendedGrades = collect($grades)->where('has_attendance_today', true)->count();
            $notAttendedGrades = $totalGrades - $attendedGrades;
            $completionPercentage = $totalGrades > 0 ? round(($attendedGrades / $totalGrades) * 100) : 0;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Total Kelas</p>
                        <p class="text-3xl font-bold mt-1">{{ $totalGrades }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Sudah Absen</p>
                        <p class="text-3xl font-bold mt-1">{{ $attendedGrades }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Belum Absen</p>
                        <p class="text-3xl font-bold mt-1">{{ $notAttendedGrades }}</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Progress</p>
                        <p class="text-3xl font-bold mt-1">{{ $completionPercentage }}%</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        @if ($totalGrades > 0)
            <div class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Progress Absensi Hari Ini
                    </span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $attendedGrades }} / {{ $totalGrades }} Kelas
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-500 {{ $completionPercentage == 100 ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-blue-500 to-blue-600' }}"
                        style="width: {{ $completionPercentage }}%"></div>
                </div>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="flex gap-2 flex-wrap">
            <button wire:click="$set('filterStatus', 'all')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ !isset($filterStatus) || $filterStatus === 'all'
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Semua ({{ $totalGrades }})
            </button>
            <button wire:click="$set('filterStatus', 'attended')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ isset($filterStatus) && $filterStatus === 'attended'
                        ? 'bg-green-600 text-white'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Sudah Absen ({{ $attendedGrades }})
            </button>
            <button wire:click="$set('filterStatus', 'not_attended')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ isset($filterStatus) && $filterStatus === 'not_attended'
                        ? 'bg-red-600 text-white'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Belum Absen ({{ $notAttendedGrades }})
            </button>
        </div>

        <!-- Grades Grid -->
        @php
            $filteredGrades = collect($grades);
            if (isset($filterStatus) && $filterStatus !== 'all') {
                if ($filterStatus === 'attended') {
                    $filteredGrades = $filteredGrades->where('has_attendance_today', true);
                } elseif ($filterStatus === 'not_attended') {
                    $filteredGrades = $filteredGrades->where('has_attendance_today', false);
                }
            }
        @endphp

        @if ($filteredGrades->isEmpty())
            <div
                class="bg-white dark:bg-gray-900 rounded-xl p-12 text-center border border-gray-200 dark:border-gray-800">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg">Tidak ada kelas yang ditemukan</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($filteredGrades as $grade)
                    <a href="{{ route('filament.admin.pages.submit-presence', ['grade' => $grade['id']]) }}"
                        wire:navigate
                        class="group relative bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800 
                              overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1
                              {{ $grade['has_attendance_today'] ? 'hover:border-green-500' : 'hover:border-blue-500' }}">
                        <!-- Color Accent Bar -->
                        <div
                            class="absolute top-0 left-0 right-0 h-1 {{ $grade['has_attendance_today'] ? 'bg-green-500' : 'bg-red-500' }}">
                        </div>

                        <div class="p-5">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                        {{ $grade['name'] }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                                        {{ $grade['major']['name'] }}
                                    </p>
                                </div>

                                <!-- Icon -->
                                <div
                                    class="flex-shrink-0 p-2 rounded-lg {{ $grade['has_attendance_today'] ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                    @if ($grade['has_attendance_today'])
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Student Count -->

                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>{{ $grade['students_count'] }} Siswa</span>
                            </div>


                            <!-- Status Badge -->
                            <div class="flex items-center justify-between">
                                @if ($grade['has_attendance_today'])
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Sudah Diverifikasi
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Belum Diverifikasi
                                    </span>
                                @endif

                                <!-- Arrow Icon -->
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Hover Effect Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Quick Actions (Optional) -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                        💡 Tips Absensi
                    </h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Pilih kelas untuk melakukan absensi. Kelas dengan tanda hijau sudah melakukan absensi hari ini.
                        Klik pada kartu kelas untuk melihat detail dan mengelola absensi siswa.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
