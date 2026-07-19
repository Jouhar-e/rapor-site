<x-filament-panels::page>
    {{-- Bagian Filter --}}
    <x-filament::section icon="heroicon-o-funnel" heading="Filter Pencarian"
        description="Saring data kelas berdasarkan kriteria di bawah ini." class="mb-6">
        {{ $this->filterForm }}
    </x-filament::section>

    @if (blank($this->treeData))
        <x-filament::empty-state heading="Tidak ada data kelas"
            description="Belum ada kelas atau peserta didik yang cocok dengan kriteria filter Anda saat ini."
            icon="heroicon-o-x-circle" />
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">

            {{-- Tombol Buka/Tutup Semua (Menggunakan Livewire wire:click) --}}
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-bottom: 0.5rem;">
                <x-filament::button size="sm" color="gray" icon="heroicon-o-arrows-pointing-in"
                    wire:click="$set('expandAll', false)">
                    Tutup Semua
                </x-filament::button>
                <x-filament::button size="sm" color="primary" icon="heroicon-o-arrows-pointing-out"
                    wire:click="$set('expandAll', true)" outlined>
                    Buka Semua
                </x-filament::button>
            </div>

            {{-- Looping Data Tahun Ajaran --}}
            @foreach ($this->treeData as $yearGroup)
                <x-filament::section :collapsible="true" :collapsed="!$this->expandAll" :heading="$yearGroup['year']->name" :description="count($yearGroup['classes']) .
                    ' kelas berjalan, total ' .
                    $yearGroup['totalLearners'] .
                    ' peserta didik'"
                    :icon="$yearGroup['year']->is_active ? 'heroicon-o-check-circle' : 'heroicon-o-clock'" :iconColor="$yearGroup['year']->is_active ? 'success' : 'gray'">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">

                        {{-- Looping Data Kelas --}}
                        @foreach ($yearGroup['classes'] as $classGroup)
                            <x-filament::section :collapsible="true" :collapsed="!$this->expandAll" :heading="$classGroup['class']->name" :description="$classGroup['class']->program?->name ?? 'Program Tidak Ditentukan'"
                                icon="heroicon-o-building-library" iconColor="primary" compact>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                    @forelse ($classGroup['learners'] as $learner)
                                        {{-- Kotak Peserta Didik --}}
                                        <div
                                            style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.75rem; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem;">

                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                {{-- Avatar Bulat Inisial --}}
                                                <div
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9999px; background-color: #e0e7ff; color: #4f46e5; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                                                    {{ substr($learner->name, 0, 2) }}
                                                </div>

                                                {{-- Nama Siswa --}}
                                                <span style="font-weight: 500; font-size: 0.875rem; color: #111827;">
                                                    {{ $learner->name }}
                                                </span>
                                            </div>

                                            {{-- Badges NIS & NISN --}}
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                @if ($learner->nis)
                                                    <x-filament::badge color="info" size="sm"
                                                        icon="heroicon-m-identification">
                                                        NIS: {{ $learner->nis }}
                                                    </x-filament::badge>
                                                @endif

                                                @if ($learner->nisn)
                                                    <x-filament::badge color="success" size="sm"
                                                        icon="heroicon-m-identification">
                                                        NISN: {{ $learner->nisn }}
                                                    </x-filament::badge>
                                                @endif
                                            </div>

                                        </div>

                                    @empty
                                        <div
                                            style="font-size: 0.875rem; color: #6b7280; font-style: italic; padding: 0.75rem 1rem; background-color: #f9fafb; border: 1px dashed #d1d5db; border-radius: 0.5rem; text-align: center;">
                                            Belum ada peserta didik yang terdaftar di kelas ini.
                                        </div>
                                    @endforelse
                                </div>
                            </x-filament::section>
                        @endforeach

                    </div>
                </x-filament::section>
            @endforeach

            {{-- Ringkasan Total di Bawah --}}
            <div
                style="display: flex; align-items: center; justify-content: center; padding: 1rem; margin-top: 1.5rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <p style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin: 0;">
                    Menampilkan <span style="color: #4f46e5; font-weight: 700;">{{ count($this->treeData) }}</span>
                    Tahun Ajaran,
                    <span style="color: #4f46e5; font-weight: 700;">{{ $this->totalClasses }}</span> Kelas, dan
                    <span style="color: #4f46e5; font-weight: 700;">{{ $this->totalLearners }}</span> Peserta Didik
                </p>
            </div>

        </div>
    @endif
</x-filament-panels::page>
