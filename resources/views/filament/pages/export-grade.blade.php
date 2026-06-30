<x-filament-panels::page>
    <form wire:submit="export">
        {{ $this->form }}

        <div class="flex gap-3 mt-6">
            <x-filament::button type="submit">
                Download CSV
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
