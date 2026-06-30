@php
    $summary = $this->summary;
@endphp

<div class="space-y-6">
    @if (empty($summary))
        <div class="text-center text-gray-500 py-8">
            Belum ada data. Silakan selesaikan semua langkah sebelumnya.
        </div>
    @else
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ringkasan Kenaikan Kelas</h3>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-success-50 dark:bg-success-950 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-success-600 dark:text-success-400">{{ $summary['total_learners'] ?? 0 }}</p>
                    <p class="text-sm text-success-700 dark:text-success-300 mt-1">Warga Belajar Naik</p>
                </div>
                <div class="bg-info-50 dark:bg-info-950 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-info-600 dark:text-info-400">{{ $summary['total_mappings'] ?? 0 }}</p>
                    <p class="text-sm text-info-700 dark:text-info-300 mt-1">Mapping Kelas</p>
                </div>
                <div class="bg-warning-50 dark:bg-warning-950 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-warning-700 dark:text-warning-300">Tahun Ajaran Sumber</p>
                    <p class="text-lg font-bold text-warning-600 dark:text-warning-400 mt-1">{{ $summary['source_year'] ?? '-' }}</p>
                </div>
                <div class="bg-primary-50 dark:bg-primary-950 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-primary-700 dark:text-primary-300">Tahun Ajaran Tujuan</p>
                    <p class="text-lg font-bold text-primary-600 dark:text-primary-400 mt-1">{{ $summary['dest_year'] ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm border border-amber-200 dark:border-amber-700 dark:bg-amber-900/20">
            <div class="px-6 py-4 flex items-start gap-3">
                <x-filament::icon
                    alias="heroicon-o-exclamation-triangle"
                    class="h-6 w-6 text-amber-500 shrink-0 mt-0.5"
                />
                <div>
                    <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-200">Konfirmasi Tindakan</h4>
                    <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                        Dengan menekan tombol "Konfirmasi & Eksekusi", maka:
                    </p>
                    <ul class="list-disc list-inside text-sm text-amber-700 dark:text-amber-300 mt-2 space-y-1">
                        <li>Mapping kenaikan kelas akan disimpan.</li>
                        <li>Warga belajar akan dipindahkan ke kelas tujuan di tahun ajaran baru.</li>
                        <li>Tahun ajaran sumber akan <strong>diarsipkan</strong>.</li>
                        <li>Tahun ajaran tujuan akan <strong>diaktifkan</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
