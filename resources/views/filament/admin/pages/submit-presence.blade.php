<x-filament::page>
    <div class="space-y-6">

        <div class="flex justify-between items-center">
            <div>
                {{ $this->form }}
            </div>


            @if ($verified)
                <div class="text-right">
                    <p class="text-green-600 font-semibold">Diverifikasi oleh: {{ $verified['name'] }}</p>
                    <p>{{ $verified['at'] }}</p>
                </div>
            @else
                <p class="text-red-600 font-semibold">Belum diverifikasi</p>
            @endif
        </div>



        <div
            class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border-b border-gray-200 dark:border-gray-800">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">#
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Nama
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">NIS
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">JK
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @if (!$students)
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data siswa.
                            </td>
                        </tr>
                    @endif
                    @foreach ($students as $index => $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-gray-100">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-900 dark:text-gray-100">
                                {{ $student['name'] }}
                            </td>

                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                {{ $student['nis'] }}
                            </td>

                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                {{ $student['gender'] }}
                            </td>

                            <td class="px-6 py-3 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <label class="inline-flex items-center space-x-1">
                                        <input type="radio" wire:model="students.{{ $index }}.status"
                                            value="hadir" class="text-green-600 focus:ring-green-500 h-4 w-4" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Hadir</span>
                                    </label>

                                    <label class="inline-flex items-center space-x-1">
                                        <input type="radio" wire:model="students.{{ $index }}.status"
                                            value="izin" class="text-yellow-500 focus:ring-yellow-500 h-4 w-4" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Izin</span>
                                    </label>

                                    <label class="inline-flex items-center space-x-1">
                                        <input type="radio" wire:model="students.{{ $index }}.status"
                                            value="sakit" class="text-blue-500 focus:ring-blue-500 h-4 w-4" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Sakit</span>
                                    </label>

                                    <label class="inline-flex items-center space-x-1">
                                        <input type="radio" wire:model="students.{{ $index }}.status"
                                            value="alpa" class="text-red-500 focus:ring-red-500 h-4 w-4" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Alpa</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($students)
                <div class="p-4  flex justify-end">
                    <x-filament::button color="success" wire:click="save">
                        Simpan Absensi
                    </x-filament::button>
                </div>
            @endif
        </div>

    </div>
</x-filament::page>
