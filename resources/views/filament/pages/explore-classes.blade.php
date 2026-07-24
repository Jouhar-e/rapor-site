<x-filament-panels::page>
    <x-filament::section icon="heroicon-o-funnel" heading="Filter Pencarian"
        description="Saring data kelas berdasarkan kriteria di bawah ini." class="mb-6">
        {{ $this->filterForm }}
    </x-filament::section>

    @if (blank($this->treeData))
        <x-filament::empty-state heading="Tidak ada data kelas"
            description="Belum ada kelas atau peserta didik yang cocok dengan kriteria filter Anda saat ini."
            icon="heroicon-o-x-circle" />
    @else
        <div x-data="{ expandAll: @js($this->expandAll) }" style="display: flex; flex-direction: column; gap: 1rem;">

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-bottom: 0.5rem;">
                <x-filament::button size="sm" color="gray" icon="heroicon-o-arrows-pointing-in"
                    x-on:click="expandAll = false; $wire.set('expandAll', false)">
                    Tutup Semua
                </x-filament::button>
                <x-filament::button size="sm" color="primary" icon="heroicon-o-arrows-pointing-out"
                    x-on:click="expandAll = true; $wire.set('expandAll', true)" outlined>
                    Buka Semua
                </x-filament::button>
            </div>

            @foreach ($this->treeData as $yearGroup)
                <div x-data="{ open: expandAll }" x-effect="open = expandAll"
                    class="rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900 shadow-sm">

                    <div x-on:click="open = !open"
                        class="flex items-center gap-3 px-4 py-3 cursor-pointer select-none">
                        <x-filament::icon :icon="$yearGroup['year']->is_active ? 'heroicon-o-check-circle' : 'heroicon-o-clock'"
                            :class="$yearGroup['year']->is_active ? 'text-success-500' : 'text-gray-400'"
                            class="h-5 w-5 shrink-0" />
                        <div class="flex flex-col flex-1 min-w-0">
                            <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $yearGroup['year']->name }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($yearGroup['classes']) }} kelas berjalan, total {{ $yearGroup['totalLearners'] }} peserta didik</span>
                        </div>
                        <x-filament::icon icon="heroicon-o-chevron-down"
                            class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200"
                            x-bind:class="open ? 'rotate-180' : ''" />
                    </div>

                    <div x-show="open" x-collapse>
                        <div class="px-4 pb-4 pt-0 flex flex-col gap-1">
                            @foreach ($yearGroup['classes'] as $classGroup)
                                <div x-data="{ openClass: expandAll }" x-effect="openClass = expandAll"
                                    class="rounded-lg border border-gray-200 bg-gray-50/50 dark:border-white/5 dark:bg-white/5">

                                    <div x-on:click="openClass = !openClass"
                                        class="flex items-center gap-3 px-4 py-2.5 cursor-pointer select-none">
                                        <x-filament::icon icon="heroicon-o-building-library"
                                            class="h-4 w-4 shrink-0 text-primary-500" />
                                        <div class="flex flex-col flex-1 min-w-0">
                                            <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $classGroup['class']->name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $classGroup['class']->program?->name ?? 'Program Tidak Ditentukan' }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ count($classGroup['learners']) }} siswa</span>
                                        <x-filament::icon icon="heroicon-o-chevron-down"
                                            class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200"
                                            x-bind:class="openClass ? 'rotate-180' : ''" />
                                    </div>

                                    <div x-show="openClass" x-collapse>
                                        <div class="px-4 pb-3 pt-0 flex flex-col gap-0.5">
                                            @forelse ($classGroup['learners'] as $learner)
                                                <div
                                                    style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.75rem; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem;">

                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <div
                                                            style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9999px; background-color: #e0e7ff; color: #4f46e5; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                                                            {{ substr($learner->name, 0, 2) }}
                                                        </div>
                                                        <span style="font-weight: 500; font-size: 0.875rem; color: #111827;">
                                                            {{ $learner->name }}
                                                        </span>
                                                    </div>

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
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

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
