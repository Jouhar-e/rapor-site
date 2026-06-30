<x-filament-panels::page>
    @if ($step === 1)
        <x-filament::section>
            <x-slot name="heading">
                Upload File CSV
            </x-slot>
            <x-slot name="description">
                Kolom: nip, name, gender, birth_place, birth_date, address, phone, email, is_active, password
            </x-slot>

            <form wire:submit="preview">
                <div>{{ $this->form }}</div>

                <div class="flex gap-3" style="margin-top: 2rem !important;">
                    <x-filament::button type="submit">
                        Preview
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    @endif

    @if ($step === 2)
        <x-filament::section>
            <x-slot name="heading">
                Preview Data (10 baris pertama)
            </x-slot>

            @livewire(\App\Filament\Widgets\ImportPreviewTableWidget::class, ['data' => $previewData], key('preview-'.$previewKey))

            <div class="flex gap-3" style="margin-top: 2rem !important;">
                <x-filament::button wire:click="executeImport" color="primary">
                    Import Data
                </x-filament::button>
                <x-filament::button wire:click="resetImport" color="gray">
                    Batal
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    @if ($step === 3 && $importResult)
        @php
            $hasErrors = ! empty($importResult['errors']);
        @endphp

        <x-filament::callout
            :color="$hasErrors ? 'danger' : 'success'"
            :icon="$hasErrors ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'"
        >
            <x-slot name="heading">
                Import Selesai
            </x-slot>
            <x-slot name="description">
                {{ $importResult['imported'] }} baris baru, {{ $importResult['updated'] }} diperbarui, {{ $importResult['skipped'] }} dilewati{{ $hasErrors ? ', ' . count($importResult['errors']) . ' error' : '' }}.
            </x-slot>
        </x-filament::callout>

        @if ($hasErrors)
            <x-filament::section secondary compact collapsible :collapsed="false" class="mt-6">
                <x-slot name="heading">
                    Detail Error ({{ count($importResult['errors']) }})
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Baris</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Error</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($importResult['errors'] as $error)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $error['row'] }}</td>
                                    <td class="px-4 py-3 text-red-600 dark:text-red-400">
                                        @foreach ($error['errors'] as $fieldErrors)
                                            @foreach ($fieldErrors as $msg)
                                                <div>{{ $msg }}</div>
                                            @endforeach
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <div style="margin-top: 2rem !important;">
            <x-filament::button wire:click="resetImport" color="primary">
                Kembali ke Upload
            </x-filament::button>
        </div>
    @endif

    <x-filament::section>
        <x-slot name="heading">
            Riwayat Import
        </x-slot>

        {{ $this->table }}
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
