<x-filament::page>
    <div class="mb-6">
        {{ $this->filterForm }}
    </div>

    @if (filled($class_id ?? $this->getAccessibleClassIds()))
        <x-filament::section class="mb-6">
            <div class="grid grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $totalStudents }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Siswa</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $totalGrades }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Nilai</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $publishedGrades }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Diterbitkan</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $lockedGrades }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Terkunci</div>
                </div>
            </div>
        </x-filament::section>
    @endif

    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex gap-4 -mb-px">
            <button
                wire:click="setActiveTab('input')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'input' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Input Nilai
            </button>
            <button
                wire:click="setActiveTab('pivot')"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'pivot' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Rekap Nilai
            </button>
        </nav>
    </div>

    {{ $this->table }}
</x-filament::page>
