<x-filament-panels::page>
    <div class="space-y-6 print:space-y-4">
        {{-- Filter Section --}}
        <x-filament::section class="print:hidden">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-funnel" class="w-5 h-5" />
                    <span>Filter Laporan</span>

                </div>
            </x-slot>

            <div class="space-y-3">
                <form wire:submit="updateData">
                    {{ $this->form }}
                </form>

                @if ($semester && $grade)
                    <div
                        class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-2">
                            <x-filament::icon icon="heroicon-o-information-circle"
                                class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-blue-900 dark:text-blue-100">
                                <p class="font-medium">{{ $this->getSemesterLabel() }} - Kelas
                                    {{ $this->getGradeLabel() }}</p>
                                <p class="text-xs text-blue-800 dark:text-blue-200 mt-1">
                                    Total Siswa: {{ $statistics['total_students'] ?? 0 }} siswa
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-2 print:hidden">
            <x-filament::button color="gray" icon="heroicon-o-printer" wire:click="print">
                Print
            </x-filament::button>
            <x-filament::button color="success" icon="heroicon-o-document-arrow-down" wire:click="exportExcel">
                Export Excel
            </x-filament::button>
            <x-filament::button color="danger" icon="heroicon-o-document-text" wire:click="exportPdf">
                Export PDF
            </x-filament::button>
        </div>

        {{-- Print Header --}}
        <div class="hidden print:block text-center mb-6">
            <h1 class="text-2xl font-bold">LAPORAN ABSENSI SISWA</h1>
            <p class="text-sm mt-2">{{ $this->getSemesterLabel() }}</p>
            <p class="text-sm">Kelas {{ $this->getGradeLabel() }}</p>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 print:grid-cols-5 print:gap-2">
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-gray-500 to-gray-600 p-3 text-white shadow-lg print:p-2">
                <div class="text-2xl font-bold print:text-lg">{{ $statistics['total_students'] ?? 0 }}</div>
                <p class="text-xs opacity-90 uppercase print:text-[10px]">Total Siswa</p>
            </div>
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-green-500 to-green-600 p-3 text-white shadow-lg print:p-2">
                <div class="text-2xl font-bold print:text-lg">{{ $statistics['hadir'] ?? 0 }}</div>
                <p class="text-xs opacity-90 uppercase print:text-[10px]">Hadir</p>
            </div>
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-yellow-500 to-yellow-600 p-3 text-white shadow-lg print:p-2">
                <div class="text-2xl font-bold print:text-lg">{{ $statistics['sakit'] ?? 0 }}</div>
                <p class="text-xs opacity-90 uppercase print:text-[10px]">Sakit</p>
            </div>
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 p-3 text-white shadow-lg print:p-2">
                <div class="text-2xl font-bold print:text-lg">{{ $statistics['izin'] ?? 0 }}</div>
                <p class="text-xs opacity-90 uppercase print:text-[10px]">Izin</p>
            </div>
            <div
                class="relative overflow-hidden rounded-lg bg-gradient-to-br from-red-500 to-red-600 p-3 text-white shadow-lg print:p-2">
                <div class="text-2xl font-bold print:text-lg">{{ $statistics['alpa'] ?? 0 }}</div>
                <p class="text-xs opacity-90 uppercase print:text-[10px]">Alpa</p>
            </div>
        </div>

        {{-- Attendance Table --}}
        <x-filament::section class="mb-12">
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-table-cells" class="w-5 h-5" />
                        <span>Laporan Absensi per Siswa</span>
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap items-center gap-3 text-xs print:gap-2 print:text-[10px]">
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 rounded bg-green-500 print:w-3 print:h-3"></div>
                            <span>H</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 rounded bg-yellow-500 print:w-3 print:h-3"></div>
                            <span>S</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 rounded bg-blue-500 print:w-3 print:h-3"></div>
                            <span>I</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 rounded bg-red-500 print:w-3 print:h-3"></div>
                            <span>A</span>
                        </div>
                    </div>
                </div>
            </x-slot>

            @if (count($reportData['students'] ?? []) > 0)
                <div class="overflow-x-auto print:text-xs">
                    <table class="w-full border-collapse text-sm print:text-[10px]">
                        <thead>
                            {{-- Month Headers --}}
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th rowspan="2"
                                    class="px-2 py-2 text-left text-xs font-bold border border-gray-300 dark:border-gray-700 sticky left-0 bg-gray-100 dark:bg-gray-800 z-20 print:px-1 print:py-1"
                                    style="min-width: 150px;">
                                    NAMA
                                </th>
                                <th rowspan="2"
                                    class="px-2 py-2 text-center text-xs font-bold border border-gray-300 dark:border-gray-700 print:px-1 print:py-1"
                                    style="min-width: 70px;">
                                    STATS
                                </th>
                                @foreach ($reportData['months'] as $month)
                                    <th colspan="{{ $maxDays }}"
                                        class="px-2 py-2 text-center text-xs font-bold border border-gray-300 dark:border-gray-700 uppercase print:px-1 print:py-1">
                                        {{ $month['month_name'] }}
                                    </th>
                                @endforeach
                            </tr>
                            {{-- Day Headers --}}
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                @foreach ($reportData['months'] as $month)
                                    @for ($day = 1; $day <= $maxDays; $day++)
                                        <th class="px-1 py-1 text-center text-xs border border-gray-300 dark:border-gray-700 print:px-0.5 print:py-0.5"
                                            style="min-width: 28px;">
                                            {{ $day }}
                                        </th>
                                    @endfor
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData['students'] as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    {{-- Student Name --}}
                                    <td
                                        class="px-2 py-2 border border-gray-300 dark:border-gray-700 sticky left-0 bg-white dark:bg-gray-900 z-10 print:px-1 print:py-1">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100 print:text-[10px]">
                                                {{ $student['name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 print:text-[8px]">
                                                {{ $student['nis'] }}</p>
                                        </div>
                                    </td>

                                    {{-- Stats --}}
                                    <td
                                        class="px-1 py-1 border border-gray-300 dark:border-gray-700 print:px-0.5 print:py-0.5">
                                        <div class="flex flex-col gap-0.5 text-xs print:text-[8px]">
                                            <div class="flex items-center justify-between">
                                                <span class="text-green-600">H:</span>
                                                <span class="font-bold">{{ $student['stats']['hadir'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-yellow-600">S:</span>
                                                <span class="font-bold">{{ $student['stats']['sakit'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-blue-600">I:</span>
                                                <span class="font-bold">{{ $student['stats']['izin'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-red-600">A:</span>
                                                <span class="font-bold">{{ $student['stats']['alpa'] }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Days --}}
                                    @foreach ($student['months'] as $monthData)
                                        @for ($day = 1; $day <= $maxDays; $day++)
                                            <td class="p-0 border border-gray-300 dark:border-gray-700 text-center">
                                                @if (isset($monthData['days'][$day]) && $monthData['days'][$day])
                                                    @php
                                                        $dayData = $monthData['days'][$day];
                                                        $status = $dayData['status'];
                                                        $isWeekend = $dayData['is_weekend'];
                                                    @endphp

                                                    @if ($isWeekend && !$status)
                                                        <div
                                                            class="w-full h-8 bg-gray-100 dark:bg-gray-800 flex items-center justify-center print:h-6">
                                                            <span class="text-xs text-gray-400">•</span>
                                                        </div>
                                                    @elseif($status)
                                                        <div
                                                            class="w-full h-8 {{ $this->getStatusColor($status) }} text-white flex items-center justify-center font-bold text-xs print:h-6 print:text-[10px]">
                                                            {{ $this->getStatusLabel($status) }}
                                                        </div>
                                                    @else
                                                        <div
                                                            class="w-full h-8 flex items-center justify-center print:h-6">
                                                            <span class="text-xs text-gray-400">-</span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="w-full h-8 bg-gray-50 dark:bg-gray-800/50 print:h-6">
                                                    </div>
                                                @endif
                                            </td>
                                        @endfor
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <x-filament::icon icon="heroicon-o-user-group" class="w-16 h-16 mx-auto mb-3 opacity-50" />
                    <p class="text-lg font-medium">Belum ada data</p>
                    <p class="text-sm mt-1">Pilih semester dan kelas untuk melihat laporan</p>
                </div>
            @endif
        </x-filament::section>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .print\:hidden {
                display: none !important;
            }

            .print\:block {
                display: block !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</x-filament-panels::page>
