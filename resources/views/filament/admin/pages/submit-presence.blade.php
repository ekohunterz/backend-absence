<x-filament::page>


    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="w-full md:w-auto">
                {{ $this->form }}
            </div>

            @if (!empty($verified['name']))
                <div class="text-left md:text-right">
                    <div class="flex items-center gap-2 text-green-600 font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Diverifikasi oleh: {{ $verified['name'] }}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $verified['at'] }}</p>
                </div>
            @else
                <div class="flex items-center gap-2 text-red-600 font-semibold">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Belum diverifikasi</span>
                </div>
            @endif
        </div>

        <!-- Statistics Cards -->
        @if ($students)
            <!-- Bulk Actions -->

            <div class="flex items-center justify-end gap-2 flex-wrap">
                <x-filament::button size="sm" color="success" wire:click="setAllStatus('hadir')">
                    Set Semua Hadir
                </x-filament::button>
            </div>
        @endif

        <!-- Attendance Table -->
        <div
            class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 w-16">
                                #</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Nama
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 w-32">
                                NIS</th>
                            <th
                                class="px-6 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-200 w-16">
                                JK</th>
                            <th
                                class="px-6 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-200 w-16">
                                Masuk</th>

                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @if (!$students || count($students) === 0)
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-sm">Tidak ada data siswa</p>
                                        <p class="text-xs mt-1">Pilih kelas terlebih dahulu</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($students as $index => $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $student['name'] }}
                                            </span>
                                            @if ($this->hasAttachments($student))
                                                <button wire:click="showStudentDetail({{ $index }})"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                    title="Lihat detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                        {{ $student['nis'] }}
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-600 dark:text-gray-400">
                                        {{ $student['gender'] }}
                                    </td>
                                    <td class="px-6 py-3 text-center text-gray-600 dark:text-gray-400">
                                        {{ $student['check_in_time'] ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="inline-flex text-center items-center gap-1.5 p-1  rounded-full">
                                            @php
                                                $statuses = [
                                                    'hadir' => [
                                                        'icon' => 'H',
                                                        'color' => 'green',
                                                    ],
                                                    'izin' => [
                                                        'icon' => 'I',
                                                        'color' => 'yellow',
                                                    ],
                                                    'sakit' => [
                                                        'icon' => 'S',
                                                        'color' => 'blue',
                                                    ],
                                                    'alpa' => [
                                                        'icon' => 'A',
                                                        'color' => 'red',
                                                    ],
                                                ];
                                            @endphp

                                            @foreach ($statuses as $value => $status)
                                                <label class="relative cursor-pointer">
                                                    <input type="radio"
                                                        wire:model="students.{{ $index }}.status"
                                                        value="{{ $value }}" class="peer sr-only" />

                                                    <!-- Pill Button -->
                                                    <div
                                                        class="relative w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700
                        text-sm font-medium
                        transition-all duration-200
                        {{ $status['color'] === 'green' ? 'text-green-700 dark:text-green-400 peer-checked:bg-green-500 peer-checked:text-white hover:bg-green-50 dark:hover:bg-green-900/30' : '' }}
                        {{ $status['color'] === 'yellow' ? 'text-yellow-700 dark:text-yellow-400 peer-checked:bg-yellow-500 peer-checked:text-white hover:bg-yellow-50 dark:hover:bg-yellow-900/30' : '' }}
                        {{ $status['color'] === 'blue' ? 'text-blue-700 dark:text-blue-400 peer-checked:bg-blue-500 peer-checked:text-white hover:bg-blue-50 dark:hover:bg-blue-900/30' : '' }}
                        {{ $status['color'] === 'red' ? 'text-red-700 dark:text-red-400 peer-checked:bg-red-500 peer-checked:text-white hover:bg-red-50 dark:hover:bg-red-900/30' : '' }}
                        peer-checked:shadow-md
                        peer-checked:scale-105
                        hover:scale-102">

                                                        <div class="flex items-center gap-1.5">
                                                            <!-- Icon -->
                                                            <span class="text-base leading-none">
                                                                {{ $status['icon'] }}
                                                            </span>


                                                        </div>

                                                        <!-- Selected Indicator Dot -->
                                                        <div
                                                            class="absolute -top-0.5 -right-0.5 
                            w-2 h-2 rounded-full
                            {{ $status['color'] === 'green' ? 'bg-green-600' : '' }}
                            {{ $status['color'] === 'yellow' ? 'bg-yellow-600' : '' }}
                            {{ $status['color'] === 'blue' ? 'bg-blue-600' : '' }}
                            {{ $status['color'] === 'red' ? 'bg-red-600' : '' }}
                            opacity-0 peer-checked:opacity-100
                            scale-0 peer-checked:scale-100
                            transition-all duration-200">
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($students && count($students) > 0)
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Total: <span class="font-semibold">{{ count($students) }}</span> siswa
                    </p>
                    <x-filament::button color="success" wire:click="save" icon="heroicon-o-check-circle">
                        Simpan Absensi
                    </x-filament::button>
                </div>
            @endif
        </div>

    </div>

    <!-- Detail Modal -->
    @if ($showDetailModal && $selectedStudent)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showDetailModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 -z-10 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
                    wire:click="closeDetailModal"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Detail Absensi
                            </h3>
                            <button wire:click="closeDetailModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4 space-y-4">
                        <!-- Student Info -->
                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama</label>
                                <p class="text-gray-900 dark:text-gray-100 font-semibold">
                                    {{ $selectedStudent['name'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">NIS</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $selectedStudent['nis'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                <p class="font-semibold">
                                    <span
                                        class=" text-sm
                                        @if ($selectedStudent['status'] === 'hadir') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($selectedStudent['status'] === 'izin') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($selectedStudent['status'] === 'sakit') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($selectedStudent['status'] === 'alpa') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif
                                    ">
                                        {{ ucfirst($selectedStudent['status']) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis
                                    Kelamin</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $selectedStudent['gender'] }}</p>
                            </div>
                        </div>

                        <!-- Time Info -->
                        @if ($selectedStudent['check_in_time'] || $selectedStudent['check_out_time'])
                            <div class="grid grid-cols-2 gap-4">
                                @if ($selectedStudent['check_in_time'])
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <label
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Waktu Masuk
                                        </label>
                                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($selectedStudent['check_in_time'])->format('H:i') }}
                                        </p>
                                    </div>
                                @endif

                                @if ($selectedStudent['check_out_time'])
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <label
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Waktu Keluar
                                        </label>
                                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($selectedStudent['check_out_time'])->format('H:i') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Selfie Photo -->
                        @if ($selectedStudent['photo_in'])
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Foto Selfie
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div
                                        class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                        <img src="{{ Storage::url($selectedStudent['photo_in']) }}" alt="Selfie"
                                            class="w-full h-auto object-cover" />
                                        <a href="{{ Storage::url($selectedStudent['photo_in']) }}" target="_blank"
                                            class="absolute top-2 right-2 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div
                                        class="relative rounded-lg overflow-hidden border  border-gray-200 dark:border-gray-700">
                                        <img src="{{ $selectedStudent['photo_out'] ? Storage::url($selectedStudent['photo_out']) : 'https://placehold.co/600x400?text=No+Image' }}"
                                            alt="Selfie" class="w-full h-auto object-cover" />
                                        @if ($selectedStudent['photo_out'])
                                            <a href="{{ Storage::url($selectedStudent['photo_out']) }}"
                                                target="_blank"
                                                class="absolute top-2 right-2 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endif

                        <!-- Permission Proof -->
                        @if ($selectedStudent['permission_proof'])
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bukti Izin/Sakit
                                </label>
                                <div
                                    class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                    <img src="{{ Storage::url($selectedStudent['permission_proof']) }}"
                                        alt="Bukti" class="w-full h-auto object-cover" />
                                    <a href="{{ Storage::url($selectedStudent['permission_proof']) }}"
                                        target="_blank"
                                        class="absolute top-2 right-2 p-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        @if ($selectedStudent['notes'])
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Catatan
                                </label>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <p class="text-gray-700 dark:text-gray-300 ">
                                        {{ $selectedStudent['notes'] }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Empty State -->
                        @if (!$selectedStudent['photo_in'] && !$selectedStudent['permission_proof'] && !$selectedStudent['notes'])
                            <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm">Tidak ada lampiran atau catatan</p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end">
                            <x-filament::button color="gray" wire:click="closeDetailModal">
                                Tutup
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament::page>
