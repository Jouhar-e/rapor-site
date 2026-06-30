<x-filament-widgets::widget class="fi-recent-activity-widget">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Aktivitas Terbaru</h3>

        <div class="mt-4 space-y-3">
            @forelse ($activities as $log)
                <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white"
                        style="background-color: {{ match($log->action) { 'create' => '#22c55e', 'update' => '#3b82f6', 'delete' => '#ef4444', default => '#6b7280' } }};"
                    >
                        <x-filament::icon
                            :icon="match($log->action) {
                                'create' => 'heroicon-m-plus',
                                'update' => 'heroicon-m-pencil',
                                'delete' => 'heroicon-m-trash',
                                default => 'heroicon-m-circle',
                            }"
                            class="h-4 w-4"
                        />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">
                            <span class="font-medium">{{ $log->user?->name ?? 'Sistem' }}</span>
                            {{ match($log->action) {
                                'create' => 'menambahkan',
                                'update' => 'mengubah',
                                'delete' => 'menghapus',
                                default => $log->action,
                            } }}
                            <span class="font-medium text-gray-600 dark:text-gray-400">{{ $log->model_basename }}</span>
                        </p>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ $log->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center gap-2 py-8 text-center">
                    <x-filament::icon
                        icon="heroicon-o-clock"
                        class="h-8 w-8 text-gray-400 dark:text-gray-500"
                    />
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
