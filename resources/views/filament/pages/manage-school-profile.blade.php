<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <br>
        <x-filament::button type="submit">
            Simpan
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
