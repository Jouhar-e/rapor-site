<x-filament-panels::page>

    {{-- 1. BAGIAN FILTER PENCARIAN --}}
    <x-filament::section icon="heroicon-o-funnel" heading="Filter Data Akademik"
        description="Pilih kelas, tahun ajaran, dan semester untuk memuat data nilai." class="mb-2">
        {{ $this->filterForm }}
    </x-filament::section>

    {{-- 2. BAGIAN WIDGET STATISTIK (Menggunakan Inline CSS agar pasti rapi) --}}
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 0.5rem;">

        {{-- Card: Total Siswa --}}
        <div
            style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <div
                style="display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; border-radius: 9999px; background-color: #eff6ff; color: #3b82f6;">
                <x-filament::icon icon="heroicon-o-users" style="width: 1.75rem; height: 1.75rem;" />
            </div>
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6b7280; font-weight: 500;">Total Siswa</p>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $totalStudents }}</h3>
            </div>
        </div>

        {{-- Card: Total Nilai --}}
        <div
            style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <div
                style="display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; border-radius: 9999px; background-color: #f3e8ff; color: #a855f7;">
                <x-filament::icon icon="heroicon-o-document-text" style="width: 1.75rem; height: 1.75rem;" />
            </div>
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6b7280; font-weight: 500;">Total Nilai</p>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $totalGrades }}</h3>
            </div>
        </div>

        {{-- Card: Diterbitkan --}}
        <div
            style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <div
                style="display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; border-radius: 9999px; background-color: #ecfdf5; color: #10b981;">
                <x-filament::icon icon="heroicon-o-check-circle" style="width: 1.75rem; height: 1.75rem;" />
            </div>
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6b7280; font-weight: 500;">Diterbitkan</p>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $publishedGrades }}</h3>
            </div>
        </div>

        {{-- Card: Terkunci --}}
        <div
            style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <div
                style="display: flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; border-radius: 9999px; background-color: #fef2f2; color: #ef4444;">
                <x-filament::icon icon="heroicon-o-lock-closed" style="width: 1.75rem; height: 1.75rem;" />
            </div>
            <div>
                <p style="margin: 0; font-size: 0.875rem; color: #6b7280; font-weight: 500;">Terkunci</p>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $lockedGrades }}</h3>
            </div>
        </div>

    </div>

    {{-- 3. BAGIAN TABEL DATA --}}
    <div>
        {{ $this->table }}
    </div>

</x-filament-panels::page>
