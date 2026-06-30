<x-filament-widgets::widget class="fi-progress-widget">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-900 dark:text-success-400">
                    <x-filament::icon icon="heroicon-o-academic-cap" class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Progress Penilaian</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelengkapan nilai akademik</p>
                </div>
            </div>
            <span class="text-2xl font-semibold tracking-tight text-success-600 dark:text-success-400">
                {{ $percentage }}%
            </span>
        </div>

        <div class="mt-4">
            <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                <div class="h-full rounded-full bg-success-500 transition-all duration-700 ease-out" style="width: {{ $percentage }}%"></div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div class="rounded-xl bg-success-50 px-3 py-2 dark:bg-success-900">
                <span class="block text-sm font-semibold text-success-600 dark:text-success-400">{{ $completed }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Selesai</span>
            </div>
            <div class="rounded-xl bg-warning-50 px-3 py-2 dark:bg-warning-900">
                <span class="block text-sm font-semibold text-warning-600 dark:text-warning-400">{{ $pending }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Tertunda</span>
            </div>
            <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-gray-700">
                <span class="block text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $not_started }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Belum Mulai</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
