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
        <div x-data="{ expandAll: @js($this->expandAll) }" style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- Tombol Aksi Buka/Tutup --}}
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <x-filament::button size="sm" color="gray" icon="heroicon-o-arrows-pointing-in"
                    x-on:click="expandAll = false; $wire.set('expandAll', false)">
                    Tutup Semua
                </x-filament::button>
                <x-filament::button size="sm" color="primary" icon="heroicon-o-arrows-pointing-out"
                    x-on:click="expandAll = true; $wire.set('expandAll', true)" outlined>
                    Buka Semua
                </x-filament::button>
            </div>

            {{-- Looping Level 1: Tahun Ajaran --}}
            @foreach ($this->treeData as $yearGroup)
                <div x-data="{ open: expandAll }" x-effect="open = expandAll"
                    style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">

                    {{-- Header Tahun Ajaran (DIPERBAIKI) --}}
                    <div x-on:click="open = !open"
                        x-bind:style="open ? 'background-color: #f9fafb; border-bottom: 1px solid #f3f4f6;' :
                            'background-color: transparent; border-bottom: 1px solid transparent;'"
                        style="cursor: pointer; user-select: none;">

                        {{-- Kerangka Utama (Aman dari timpaan Alpine) --}}
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem;">
                            <x-filament::icon :icon="$yearGroup['year']->is_active ? 'heroicon-o-check-circle' : 'heroicon-o-clock'"
                                style="width: 1.5rem; height: 1.5rem; flex-shrink: 0; color: {{ $yearGroup['year']->is_active ? '#10b981' : '#9ca3af' }};" />

                            <div style="display: flex; flex-direction: column; flex: 1; min-width: 0;">
                                <span
                                    style="font-size: 1rem; font-weight: 600; color: #111827;">{{ $yearGroup['year']->name }}</span>
                                <span style="font-size: 0.8125rem; color: #6b7280; margin-top: 0.125rem;">
                                    <strong>{{ count($yearGroup['classes']) }}</strong> kelas berjalan, total
                                    <strong>{{ $yearGroup['totalLearners'] }}</strong> peserta didik
                                </span>
                            </div>

                            <div style="transition: transform 0.2s;"
                                x-bind:style="open ? 'transform: rotate(180deg);' : ''">
                                <x-filament::icon icon="heroicon-o-chevron-down"
                                    style="width: 1.25rem; height: 1.25rem; flex-shrink: 0; color: #9ca3af;" />
                            </div>
                        </div>

                    </div>

                    {{-- Konten Tahun Ajaran --}}
                    <div x-show="open" x-collapse>
                        <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">

                            {{-- Looping Level 2: Kelas --}}
                            @foreach ($yearGroup['classes'] as $classGroup)
                                <div x-data="{ openClass: expandAll }" x-effect="openClass = expandAll"
                                    style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; overflow: hidden;">

                                    {{-- Header Kelas (DIPERBAIKI) --}}
                                    <div x-on:click="openClass = !openClass"
                                        x-bind:style="openClass ? 'background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0;' :
                                            'background-color: transparent; border-bottom: 1px solid transparent;'"
                                        style="cursor: pointer; user-select: none;">

                                        {{-- Kerangka Utama (Aman dari timpaan Alpine) --}}
                                        <div
                                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem;">
                                            <x-filament::icon icon="heroicon-o-building-library"
                                                style="width: 1.25rem; height: 1.25rem; flex-shrink: 0; color: #3b82f6;" />

                                            <div style="display: flex; flex-direction: column; flex: 1; min-width: 0;">
                                                <span
                                                    style="font-size: 0.875rem; font-weight: 600; color: #0f172a;">{{ $classGroup['class']->name }}</span>
                                                <span
                                                    style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">{{ $classGroup['class']->program?->name ?? 'Program Tidak Ditentukan' }}</span>
                                            </div>

                                            <span
                                                style="font-size: 0.75rem; font-weight: 500; color: #64748b; background-color: #e2e8f0; padding: 0.125rem 0.5rem; border-radius: 9999px;">
                                                {{ count($classGroup['learners']) }} siswa
                                            </span>

                                            <div style="margin-left: 0.5rem; transition: transform 0.2s;"
                                                x-bind:style="openClass ? 'transform: rotate(180deg);' : ''">
                                                <x-filament::icon icon="heroicon-o-chevron-down"
                                                    style="width: 1rem; height: 1rem; flex-shrink: 0; color: #94a3b8;" />
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Konten Kelas (Daftar Siswa) --}}
                                    <div x-show="openClass" x-collapse>
                                        <div style="padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">

                                            {{-- Looping Level 3: Peserta Didik --}}
                                            @forelse ($classGroup['learners'] as $learner)
                                                <div
                                                    style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.75rem 1rem; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);">

                                                    {{-- Info Kiri Siswa --}}
                                                    <div style="display: flex; align-items: center; gap: 0.875rem;">
                                                        <div
                                                            style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 9999px; background-color: #eff6ff; color: #2563eb; font-size: 0.875rem; font-weight: 700; text-transform: uppercase; border: 1px solid #bfdbfe;">
                                                            {{ substr($learner->name, 0, 2) }}
                                                        </div>
                                                        <span
                                                            style="font-weight: 600; font-size: 0.875rem; color: #1e293b;">
                                                            {{ $learner->name }}
                                                        </span>
                                                    </div>

                                                    {{-- Info Kanan Siswa (Badge) --}}
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
                                                {{-- State Kosong Siswa --}}
                                                <div
                                                    style="font-size: 0.875rem; color: #64748b; font-style: italic; padding: 1rem; background-color: #ffffff; border: 1px dashed #cbd5e1; border-radius: 0.5rem; text-align: center;">
                                                    Belum ada peserta didik yang terdaftar di kelas ini.
                                                </div>
                                            @endforelse

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Ringkasan Total --}}
            <div
                style="display: flex; align-items: center; justify-content: center; padding: 1.25rem; margin-top: 0.5rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <p style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin: 0; text-align: center;">
                    Menampilkan <span style="color: #4f46e5; font-weight: 700;">{{ count($this->treeData) }}</span>
                    Tahun Ajaran,
                    <span style="color: #4f46e5; font-weight: 700;">{{ $this->totalClasses }}</span> Kelas, dan
                    <span style="color: #4f46e5; font-weight: 700;">{{ $this->totalLearners }}</span> Peserta Didik
                </p>
            </div>

        </div>
    @endif
</x-filament-panels::page>
