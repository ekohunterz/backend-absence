<x-filament::page x-data="{ activeTab: 'tab1' }">

    <x-filament::tabs class="w-full">
        <x-filament::tabs.item alpine-active="activeTab === 'tab1'" x-on:click="activeTab = 'tab1'" class="w-full">
            Rekap Siswa
        </x-filament::tabs.item>
        <x-filament::tabs.item alpine-active="activeTab === 'tab2'" x-on:click="activeTab = 'tab2'" class="w-full">
            Rekap Kelas
        </x-filament::tabs.item>
    </x-filament::tabs>

    {{-- Rekap Siswa --}}
    <div class="space-y-6" x-show="activeTab === 'tab1'">
        {{-- Filter --}}
        <div class="p-4 bg-gray-50 rounded-full border border-gray-200 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-center gap-4">
                <div class="grow">{{ $this->form }}</div>

                <div class="flex items-end space-x-4 justify-end">
                    <x-filament::button wire:click="loadReport" class="w-full">
                        Tampilkan
                    </x-filament::button>
                    @if (!empty($reports))
                        <x-filament::button wire:click="exportToExcel" class="w-full" color="warning">
                            Export
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabel Laporan --}}
        @if (empty($grade_id) || empty($academic_year_id))
            <div
                class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                Silahkan pilih kelas dan tahun ajaran untuk menampilkan laporan.
            </div>
        @elseif ($reports->isNotEmpty())
            <div
                class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border-b border-gray-200 dark:border-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">NIS</th>
                            <th class="px-4 py-2 text-center">Hadir</th>
                            <th class="px-4 py-2 text-center">Izin</th>
                            <th class="px-4 py-2 text-center">Sakit</th>
                            <th class="px-4 py-2 text-center">Alpa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($reports as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-2">{{ $r['name'] }}</td>
                                <td class="px-4 py-2">{{ $r['nis'] }}</td>
                                <td class="px-4 py-2 text-center text-green-600">{{ $r['hadir'] }}</td>
                                <td class="px-4 py-2 text-center text-yellow-500">{{ $r['izin'] }}</td>
                                <td class="px-4 py-2 text-center text-blue-500">{{ $r['sakit'] }}</td>
                                <td class="px-4 py-2 text-center text-red-500">{{ $r['alpa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Total</th>
                            <th class="px-4 py-2 text-left"></th>
                            <th class="px-4 py-2 text-center">{{ $reports->sum('hadir') }}</th>
                            <th class="px-4 py-2 text-center">{{ $reports->sum('izin') }}</th>
                            <th class="px-4 py-2 text-center">{{ $reports->sum('sakit') }}</th>
                            <th class="px-4 py-2 text-center">{{ $reports->sum('alpa') }}</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div
                class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                Tidak ada data laporan untuk ditampilkan.
            </div>
        @endif

    </div>


    {{-- Rekap Kelas --}}
    <div class="space-y-6" x-show="activeTab === 'tab2'">
        {{-- Filter --}}
        <div class="p-4 bg-gray-50 rounded-full border border-gray-200 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-center gap-4">
                <div class="grow">{{ $this->form_major }}</div>


                <div class="flex items-end space-x-4 justify-end">
                    <x-filament::button wire:click="loadReportGrades" class="w-full">
                        Tampilkan
                    </x-filament::button>
                    @if (!empty($report_grades))
                        <x-filament::button wire:click="exportGradeToExcel" class="w-full" color="warning">
                            Export
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabel Laporan --}}
        @if (empty($academic_year_id))
            <div
                class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                Silahkan pilih tahun ajaran untuk menampilkan laporan.
            </div>
        @elseif ($report_grades)
            <div
                class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border-b border-gray-200 dark:border-gray-800">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-center">Hadir</th>
                            <th class="px-4 py-2 text-center">Izin</th>
                            <th class="px-4 py-2 text-center">Sakit</th>
                            <th class="px-4 py-2 text-center">Alpa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($report_grades as $r)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-2">{{ $r['name'] }}</td>
                                <td class="px-4 py-2 text-center text-green-600">{{ $r['hadir'] }}</td>
                                <td class="px-4 py-2 text-center text-yellow-500">{{ $r['izin'] }}</td>
                                <td class="px-4 py-2 text-center text-blue-500">{{ $r['sakit'] }}</td>
                                <td class="px-4 py-2 text-center text-red-500">{{ $r['alpa'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Total</th>
                            <th class="px-4 py-2 text-center">{{ $report_grades->sum('hadir') }}</th>
                            <th class="px-4 py-2 text-center">{{ $report_grades->sum('izin') }}</th>
                            <th class="px-4 py-2 text-center">{{ $report_grades->sum('sakit') }}</th>
                            <th class="px-4 py-2 text-center">{{ $report_grades->sum('alpa') }}</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div
                class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                Tidak ada data laporan untuk ditampilkan.
            </div>
        @endif

    </div>
</x-filament::page>
