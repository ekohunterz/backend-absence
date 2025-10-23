<x-filament::page>
    <div class="space-y-6">

        {{-- Filter --}}
        <div class="p-4 bg-gray-50 rounded-full border border-gray-200 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="grade_id">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="academic_year_id">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach ($academic_years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model="month">
                        <option value="">-- Tampilkan Semua --</option>
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}">
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>


                <div class="flex items-end">
                    <x-filament::button wire:click="loadReport" class="w-full">
                        Tampilkan
                    </x-filament::button>
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
</x-filament::page>
