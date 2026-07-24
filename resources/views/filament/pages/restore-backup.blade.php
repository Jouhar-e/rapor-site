<x-filament-panels::page>

    {{-- STEP 1: UPLOAD FILE --}}
    @if ($step === 1)
        <x-filament::section icon="heroicon-o-cloud-arrow-up">
            <x-slot name="heading">
                Upload File Backup
            </x-slot>
            <x-slot name="description">
                Pilih file .sql hasil backup untuk direstore ke database.
            </x-slot>

            <form wire:submit="preview">
                <div>{{ $this->form }}</div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <x-filament::button type="submit" icon="heroicon-o-eye">
                        Preview File
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    @endif

    {{-- STEP 2: KONFIRMASI RESTORE --}}
    @if ($step === 2 && $fileInfo)
        <x-filament::section icon="heroicon-o-document-magnifying-glass">
            <x-slot name="heading">
                Konfirmasi Restore
            </x-slot>
            <x-slot name="description">
                Periksa kembali detail file backup sebelum melakukan proses pemulihan data.
            </x-slot>

            {{-- Detail File (Menggunakan Inline CSS agar rapi dan tidak menumpuk) --}}
            <div
                style="border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <div style="display: flex; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;">
                    <div
                        style="width: 35%; padding: 0.875rem 1.25rem; font-weight: 600; color: #4b5563; font-size: 0.875rem;">
                        Nama File
                    </div>
                    <div
                        style="width: 65%; padding: 0.875rem 1.25rem; color: #111827; font-size: 0.875rem; word-break: break-all;">
                        {{ $fileInfo['name'] }}
                    </div>
                </div>
                <div style="display: flex; border-bottom: 1px solid #e5e7eb; background-color: #ffffff;">
                    <div
                        style="width: 35%; padding: 0.875rem 1.25rem; font-weight: 600; color: #4b5563; font-size: 0.875rem;">
                        Ukuran
                    </div>
                    <div style="width: 65%; padding: 0.875rem 1.25rem; color: #111827; font-size: 0.875rem;">
                        {{ $fileInfo['size_formatted'] }}
                    </div>
                </div>
                <div style="display: flex; background-color: #f9fafb;">
                    <div
                        style="width: 35%; padding: 0.875rem 1.25rem; font-weight: 600; color: #4b5563; font-size: 0.875rem;">
                        Diupload Tanggal
                    </div>
                    <div style="width: 65%; padding: 0.875rem 1.25rem; color: #111827; font-size: 0.875rem;">
                        {{ $fileInfo['uploaded_at'] }}
                    </div>
                </div>
            </div>

            <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle">
                <x-slot name="heading">
                    Peringatan Kritis!
                </x-slot>
                <x-slot name="description">
                    Semua data saat ini akan diganti dan ditimpa dengan data dari file backup ini. Tindakan ini
                    <strong>tidak dapat dibatalkan</strong>. Pastikan Anda telah memilih file yang benar.
                </x-slot>
            </x-filament::callout>

            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <x-filament::button wire:click="executeRestore" color="warning" icon="heroicon-o-arrow-path">
                    Ya, Restore Data Sekarang
                </x-filament::button>
                <x-filament::button wire:click="resetRestore" color="gray" outlined>
                    Batal
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- STEP 3: HASIL RESTORE --}}
    @if ($step === 3 && $restoreResult)
        <x-filament::section>
            <x-filament::callout :color="$restoreResult['success'] ? 'success' : 'danger'" :icon="$restoreResult['success'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'">
                <x-slot name="heading">
                    {{ $restoreResult['success'] ? 'Restore Berhasil Diselesaikan' : 'Restore Gagal' }}
                </x-slot>
                <x-slot name="description">
                    @if ($restoreResult['success'])
                        Database Anda telah berhasil dipulihkan dari file
                        <strong>{{ $restoreResult['filename'] }}</strong>.
                    @else
                        Terjadi kesalahan: {{ $restoreResult['error'] }}
                    @endif
                </x-slot>
            </x-filament::callout>

            <div style="margin-top: 1.5rem;">
                <x-filament::button wire:click="resetRestore" color="primary" icon="heroicon-o-arrow-left">
                    Kembali ke Menu Awal
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- TABEL RIWAYAT BACKUP BAWAAN --}}
    <x-filament::section icon="heroicon-o-clock">
        <x-slot name="heading">
            Riwayat Backup Database
        </x-slot>

        {{ $this->table }}
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
