<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-funnel" class="w-5 h-5" />
                    <span>Filter Tahun Ajaran</span>
                </div>
            </x-slot>

            <div class="space-y-3">
                <form wire:submit="updateData">
                    {{ $this->form }}
                </form>

                @if ($academicYear && $semester)
                    <div
                        class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-2">
                            <x-filament::icon icon="heroicon-o-information-circle"
                                class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-blue-900 dark:text-blue-100">
                                <p class="font-medium">{{ $this->getAcademicYearLabel() }} -
                                    {{ $this->getSemesterLabel() }}</p>
                                <p class="text-xs text-blue-800 dark:text-blue-200 mt-1">
                                    Periode: {{ $this->getSemesterPeriod() }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-blue-800 dark:text-blue-200">Status:</span>
                                    @if ($semester->is_active)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Semester Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            {{-- Hadir --}}
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-green-500 to-green-600 p-4 text-white shadow-lg">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['hadir'] ?? 0 }}</div>
                    <p class="text-xs opacity-90 uppercase tracking-wide">Hadir</p>
                </div>
            </div>

            {{-- Sakit --}}
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-yellow-500 to-yellow-600 p-4 text-white shadow-lg">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['sakit'] ?? 0 }}</div>
                    <p class="text-xs opacity-90 uppercase tracking-wide">Sakit</p>
                </div>
            </div>

            {{-- Izin --}}
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white shadow-lg">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['izin'] ?? 0 }}</div>
                    <p class="text-xs opacity-90 uppercase tracking-wide">Izin</p>
                </div>
            </div>

            {{-- Alpa --}}
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-red-500 to-red-600 p-4 text-white shadow-lg">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['alpa'] ?? 0 }}</div>
                    <p class="text-xs opacity-90 uppercase tracking-wide">Alpa</p>
                </div>
            </div>

            {{-- Hari Kerja --}}
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 p-4 text-white shadow-lg">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['work_days'] ?? 0 }}</div>
                    <p class="text-xs opacity-90 uppercase tracking-wide">Hari Kerja</p>
                </div>
            </div>

            {{-- Persentase --}}
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 p-4 text-white shadow-lg">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold mb-1">{{ $statistics['attendance_rate'] ?? 0 }}%</div>
                    <p class="text-xs opacity-90 uppercase tracking-wide">Kehadiran</p>
                </div>
            </div>
        </div>

        {{-- Academic Year Calendar Table --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-5 h-5" />
                        <span>
                            Kalender Absensi - {{ $this->getAcademicYearLabel() }}
                        </span>
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded bg-green-500 text-white flex items-center justify-center font-semibold text-xs">
                                H</div>
                            <span class="text-gray-600 dark:text-gray-400">Hadir</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded bg-yellow-500 text-white flex items-center justify-center font-semibold text-xs">
                                S</div>
                            <span class="text-gray-600 dark:text-gray-400">Sakit</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded bg-blue-500 text-white flex items-center justify-center font-semibold text-xs">
                                I</div>
                            <span class="text-gray-600 dark:text-gray-400">Izin</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded bg-red-500 text-white flex items-center justify-center font-semibold text-xs">
                                A</div>
                            <span class="text-gray-600 dark:text-gray-400">Alpa</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div
                                class="w-5 h-5 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xs">
                                -</div>
                            <span class="text-gray-600 dark:text-gray-400">Libur/Kosong</span>
                        </div>
                    </div>
                </div>
            </x-slot>

            @if (count($semesterData) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse min-w-max">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase border border-gray-300 dark:border-gray-700 sticky left-0 bg-gray-100 dark:bg-gray-800 z-20"
                                    style="min-width: 120px;">
                                    Bulan
                                </th>
                                <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase border border-gray-300 dark:border-gray-700"
                                    style="min-width: 80px;">
                                    Statistik
                                </th>
                                @for ($day = 1; $day <= $maxDays; $day++)
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-700"
                                        style="min-width: 32px;">
                                        {{ $day }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($semesterData as $monthData)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    {{-- Bulan --}}
                                    <td
                                        class="px-3 py-3 text-sm font-bold text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 sticky left-0 bg-white dark:bg-gray-900 z-10">
                                        <div class="flex flex-col">
                                            <span>{{ $monthData['month_name'] }}</span>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 font-normal">{{ $monthData['year'] }}</span>
                                        </div>
                                    </td>

                                    {{-- Statistik Bulanan --}}
                                    <td class="px-2 py-2 border border-gray-300 dark:border-gray-700">
                                        <div class="flex flex-col gap-1 text-xs">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="text-green-600 dark:text-green-400 font-semibold">H:</span>
                                                <span
                                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $monthData['stats']['hadir'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between gap-1">
                                                <span
                                                    class="text-yellow-600 dark:text-yellow-400 font-semibold">S:</span>
                                                <span
                                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $monthData['stats']['sakit'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="text-blue-600 dark:text-blue-400 font-semibold">I:</span>
                                                <span
                                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $monthData['stats']['izin'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="text-red-600 dark:text-red-400 font-semibold">A:</span>
                                                <span
                                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $monthData['stats']['alpa'] }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Days (1-31) --}}
                                    @for ($day = 1; $day <= $maxDays; $day++)
                                        <td class="p-0 border border-gray-300 dark:border-gray-700 text-center">
                                            @if (isset($monthData['days'][$day]) && $monthData['days'][$day])
                                                @php
                                                    $dayData = $monthData['days'][$day];
                                                    $status = $dayData['status'];
                                                    $isWeekend = $dayData['is_weekend'];
                                                @endphp

                                                @if ($isWeekend && !$status)
                                                    {{-- Weekend tanpa absen --}}
                                                    <div
                                                        class="w-full h-10 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                                        <span class="text-xs text-gray-400">•</span>
                                                    </div>
                                                @elseif ($status)
                                                    {{-- Ada status absen --}}
                                                    <div class="w-full h-10 {{ $this->getStatusColor($status) }} text-white flex items-center justify-center font-bold text-sm cursor-help"
                                                        title="{{ $this->getStatusTooltip($status, $dayData) }}">
                                                        {{ $this->getStatusLabel($status) }}
                                                    </div>
                                                @else
                                                    {{-- Hari kerja belum absen --}}
                                                    <div
                                                        class="w-full h-10 bg-white dark:bg-gray-900 flex items-center justify-center">
                                                        <span class="text-xs text-gray-400 dark:text-gray-600">-</span>
                                                    </div>
                                                @endif
                                            @else
                                                {{-- Hari tidak ada di bulan ini --}}
                                                <div class="w-full h-10 bg-gray-50 dark:bg-gray-800/50"></div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Info Note --}}
                <div
                    class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-2">
                        <x-filament::icon icon="heroicon-o-information-circle"
                            class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                        <div class="text-xs text-blue-900 dark:text-blue-100">
                            <p class="font-medium mb-1">Keterangan:</p>
                            <ul class="space-y-0.5 text-blue-800 dark:text-blue-200">
                                <li>• Hover pada kotak berwarna untuk melihat detail jam masuk/keluar</li>
                                <li>• Kolom "Statistik" menampilkan ringkasan per bulan</li>
                                <li>• Titik (•) menandakan hari libur/weekend</li>
                                <li>• Kotak kosong abu-abu adalah tanggal yang tidak ada di bulan tersebut</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <x-filament::icon icon="heroicon-o-calendar-days" class="w-16 h-16 mx-auto mb-3 opacity-50" />
                    <p class="text-lg font-medium">Belum ada data absensi</p>
                    <p class="text-sm mt-1">Silakan pilih tahun ajaran yang berbeda atau mulai melakukan absensi</p>
                </div>
            @endif
        </x-filament::section>

        {{-- Summary by Month --}}
        @if (count($semesterData) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($semesterData as $monthData)
                    <div
                        class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $monthData['month_name'] }} {{ $monthData['year'] }}
                            </h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $monthData['days_in_month'] }} hari
                            </span>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <div class="text-center p-2 bg-green-50 dark:bg-green-900/20 rounded">
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                                    {{ $monthData['stats']['hadir'] }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Hadir</div>
                            </div>
                            <div class="text-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                                <div class="text-lg font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $monthData['stats']['sakit'] }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Sakit</div>
                            </div>
                            <div class="text-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    {{ $monthData['stats']['izin'] }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Izin</div>
                            </div>
                            <div class="text-center p-2 bg-red-50 dark:bg-red-900/20 rounded">
                                <div class="text-lg font-bold text-red-600 dark:text-red-400">
                                    {{ $monthData['stats']['alpa'] }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Alpa</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
