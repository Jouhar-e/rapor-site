<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter
        </x-slot>
        {{ $this->form }}
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>
