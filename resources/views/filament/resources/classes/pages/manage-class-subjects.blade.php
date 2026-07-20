<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Daftar Mata Pelajaran - {{ $class->name }}
        </x-slot>
        <x-slot name="description">
            Klik nama mata pelajaran untuk membuka Manajemen Mata Pelajaran.
        </x-slot>
    </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>
