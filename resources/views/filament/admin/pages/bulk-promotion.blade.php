<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Form Section --}}
        <div>
            {{ $this->form }}
        </div>

        {{-- Students List --}}
        @if (!empty($students))
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">
                                Daftar Siswa - {{ $sourceGrade?->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $this->getSelectedCount() }} dari {{ $this->getTotalCount() }} siswa terpilih
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <x-filament::button wire:click="selectAll" size="sm" color="success" outlined>
                                Pilih Semua
                            </x-filament::button>
                            <x-filament::button wire:click="deselectAll" size="sm" color="gray" outlined>
                                Batalkan Semua
                            </x-filament::button>
                        </div>
                    </div>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" wire:click="selectAll"
                                        checked="{{ $this->getSelectedCount() === $this->getTotalCount() }}"
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" />
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    No
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    NIS
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Nama Siswa
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    JK
                                </th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($students as $index => $student)
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition
                                    {{ in_array($student['id'], $selectedStudentIds) ? 'bg-primary-50 dark:bg-primary-900/10' : '' }}">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" wire:click="toggleStudent({{ $student['id'] }})"
                                            checked="{{ in_array($student['id'], $selectedStudentIds) }}"
                                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" />
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $student['nis'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $student['name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $student['gender'] === 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200' }}">
                                            {{ $student['gender'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if (in_array($student['id'], $selectedStudentIds))
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Terpilih
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Summary & Action --}}
                <div class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>
                            <span
                                class="font-semibold text-gray-900 dark:text-gray-100">{{ $this->getSelectedCount() }}</span>
                            siswa akan dinaikan ke kelas
                            @if (isset($data['target_grade_id']))
                                <span class="font-semibold text-primary-600">
                                    {{ \App\Models\Grade::find($data['target_grade_id'])?->name ?? '-' }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </p>
                    </div>

                    <x-filament::button wire:click="promote" color="success" icon="heroicon-o-arrow-up-circle"
                        size="lg" :disabled="empty($selectedStudentIds) || !isset($data['target_grade_id'])">
                        Naikan Kelas ({{ $this->getSelectedCount() }} Siswa)
                    </x-filament::button>
                </div>
            </x-filament::section>
        @else
            {{-- Empty State --}}
            <x-filament::section>
                <div class="py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        Pilih Kelas Asal
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Pilih kelas yang ingin dinaikan untuk melihat daftar siswa
                    </p>
                </div>
            </x-filament::section>
        @endif

        {{-- Info Box --}}
        <x-filament::section>
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-medium text-gray-900 dark:text-gray-100 mb-2">Cara Penggunaan:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Pilih kelas asal yang akan dinaikan</li>
                        <li>Pilih kelas tujuan</li>
                        <li>Centang siswa yang akan dinaikan (default: semua terpilih)</li>
                        <li>Isi alasan/keterangan (opsional)</li>
                        <li>Klik tombol "Naikan Kelas"</li>
                    </ol>
                    <p class="mt-3 text-xs text-gray-500">
                        💡 Tips: Gunakan tombol "Auto Promote" untuk otomatis naikan ke kelas berikutnya
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
