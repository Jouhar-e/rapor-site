<x-filament-panels::page>
    <div class="mb-6">
        {{ $this->filterForm }}
    </div>

    @if (blank($this->treeData))
        <x-filament::empty-state
            heading="Tidak ada data"
            description="Belum ada kelas dengan peserta didik untuk pengaturan filter saat ini."
        />
    @else
        <div x-data="{ expandedAll: false }" class="space-y-2">
            <div class="flex gap-2 mb-4">
                <x-filament::button
                    size="xs"
                    color="gray"
                    x-on:click="expandedAll = true"
                >
                    Buka Semua
                </x-filament::button>
                <x-filament::button
                    size="xs"
                    color="gray"
                    x-on:click="expandedAll = false"
                >
                    Tutup Semua
                </x-filament::button>
            </div>

            @foreach ($this->treeData as $yearGroup)
                <div
                    x-data="{ yearOpen: false }"
                    x-init="
                        yearOpen = expandedAll;
                        $watch('expandedAll', val => yearOpen = val)
                    "
                    class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden"
                >
                    <button
                        x-on:click="yearOpen = !yearOpen"
                        class="w-full flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left"
                    >
                        <span x-show="!yearOpen">
                            <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                        </span>
                        <span x-show="yearOpen" style="display: none">
                            <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400" />
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $yearGroup['year']->name }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ({{ count($yearGroup['classes']) }} kelas, {{ $yearGroup['totalLearners'] }} peserta)
                        </span>
                        @if ($yearGroup['year']->is_active)
                            <x-filament::badge size="sm" color="success">Aktif</x-filament::badge>
                        @endif
                    </button>

                    <div x-show="yearOpen" style="display: none" class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($yearGroup['classes'] as $classGroup)
                            <div
                                x-data="{ classOpen: false }"
                                x-init="
                                    classOpen = expandedAll;
                                    $watch('expandedAll', val => classOpen = val)
                                "
                                class="ml-6"
                            >
                                <button
                                    x-on:click="classOpen = !classOpen"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-left"
                                >
                                    <span x-show="!classOpen">
                                        <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400" />
                                    </span>
                                    <span x-show="classOpen" style="display: none">
                                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400" />
                                    </span>
                                    <x-heroicon-o-building-library class="w-5 h-5 text-blue-500" />
                                    <span class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $classGroup['class']->name }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        ({{ $classGroup['class']->program?->name ?? '-' }})
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ count($classGroup['learners']) }} peserta
                                    </span>
                                </button>

                                <div x-show="classOpen" style="display: none" class="ml-10 border-l-2 border-gray-200 dark:border-gray-600 pl-4 py-1">
                                    @forelse ($classGroup['learners'] as $learner)
                                        <div class="flex items-center gap-3 py-1.5 text-sm">
                                            <x-heroicon-o-user class="w-4 h-4 text-gray-400 shrink-0" />
                                            <span class="text-gray-700 dark:text-gray-300">{{ $learner->name }}</span>
                                            @if ($learner->nis)
                                                <span class="text-gray-400 text-xs">NIS: {{ $learner->nis }}</span>
                                            @endif
                                            @if ($learner->nisn)
                                                <span class="text-gray-400 text-xs">NISN: {{ $learner->nisn }}</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-400 italic py-2">
                                            Belum ada peserta didik
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="text-sm text-gray-400 mt-4">
                Total: {{ count($this->treeData) }} tahun ajaran, {{ $this->totalClasses }} kelas, {{ $this->totalLearners }} peserta didik
            </div>
        </div>
    @endif
</x-filament-panels::page>
