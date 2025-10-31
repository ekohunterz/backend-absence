<x-filament-panels::page>
    {{ $this->form }}

    <div class="mt-6 flex items-center gap-4 justify-end">
        <x-filament::button wire:click="save" color="primary" wire:loading.attr="disabled" wire:target="save">
            Simpan
        </x-filament::button>
    </div>
</x-filament-panels::page>
