<x-filament-widgets::widget class="fi-progress-widget">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-filament::section>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-success-50 text-success-600 dark:bg-success-900 dark:text-success-400">
                        <x-filament::icon
                            icon="heroicon-o-academic-cap"
                            class="h-6 w-6"
                        />
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Progress Penilaian
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Kelengkapan nilai akademik
                        </p>
                    </div>
                </div>
                <span class="text-2xl font-semibold tracking-tight text-success-600 dark:text-success-400">
                    {{ $grade['percentage'] }}%
                </span>
            </div>

            <div class="mt-4">
                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div
                        class="h-full rounded-full bg-success-500 transition-all duration-700 ease-out"
                        style="width: {{ $grade['percentage'] }}%"
                    ></div>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>
                    <span class="font-semibold text-success-600 dark:text-success-400">{{ $grade['completed'] }}</span>
                    selesai
                </span>
                <span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $grade['pending'] }}</span>
                    tersisa
                </span>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-info-50 text-info-600 dark:bg-info-900 dark:text-info-400">
                        <x-filament::icon
                            icon="heroicon-o-clipboard-document-check"
                            class="h-6 w-6"
                        />
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Progress Absensi
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Kelengkapan data absensi
                        </p>
                    </div>
                </div>
                <span class="text-2xl font-semibold tracking-tight text-info-600 dark:text-info-400">
                    {{ $attendance['percentage'] }}%
                </span>
            </div>

            <div class="mt-4">
                <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div
                        class="h-full rounded-full bg-info-500 transition-all duration-700 ease-out"
                        style="width: {{ $attendance['percentage'] }}%"
                    ></div>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>
                    <span class="font-semibold text-info-600 dark:text-info-400">{{ $attendance['completed'] }}</span>
                    selesai
                </span>
                <span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $attendance['pending'] }}</span>
                    tersisa
                </span>
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
