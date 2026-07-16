<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter
        </x-slot>
        {{ $this->form }}
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Peserta Didik
        </x-slot>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
