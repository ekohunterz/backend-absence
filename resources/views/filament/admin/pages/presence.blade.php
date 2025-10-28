<x-filament-panels::page>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pilih Kelas</h1>

            <div class="flex flex-wrap items-center gap-4">
                <div class="grow">{{ $this->form }}</div>
                {{-- <x-filament::input.wrapper>
                    <input type="date" wire:model.live.debounce.250ms="date" class="" />
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model.live.debounce.250ms="search"
                        placeholder="Cari Kelas..." />
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="selectedMajor">
                        <option value="">Semua Jurusan</option>
                        @foreach ($majors as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper> --}}
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($grades as $grade)
                <a href="{{ route('filament.admin.pages.submit-presence', ['grade' => $grade->id]) }}" wire:navigate
                    class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200  dark:border-gray-800
                   flex flex-col justify-between transition hover:shadow-md">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $grade->name }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $grade->major->name }}
                        </p>
                    </div>

                    <div class="mt-3">
                        @if ($grade->has_attendance_today)
                            <span
                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                ✅ Sudah Absen
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                ⏳ Belum Absen
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

    </div>



</x-filament-panels::page>
