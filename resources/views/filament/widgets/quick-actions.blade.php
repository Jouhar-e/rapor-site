@php
    $user = filament()->auth()->user();
    $actions = $user->hasRole('admin') ? $adminActions : $tutorActions;
@endphp

<x-filament-widgets::widget class="fi-actions-widget">
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Aksi Cepat</h3>
        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Tindakan yang sering digunakan</p>

        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
            @foreach ($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    class="group flex flex-col items-center gap-3 rounded-xl border border-gray-200 bg-white p-5 text-center shadow-sm transition-all hover:border-primary-200 hover:shadow-md dark:border-gray-600 dark:bg-gray-800 dark:hover:border-primary-700"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50 text-gray-600 shadow-sm transition-transform group-hover:scale-110 group-hover:bg-primary-50 group-hover:text-primary-600 dark:bg-gray-700 dark:text-gray-400 dark:group-hover:bg-primary-900 dark:group-hover:text-primary-400">
                        <x-filament::icon
                            :icon="$action['icon']"
                            class="h-6 w-6"
                        />
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">
                            {{ $action['label'] }}
                        </span>
                        <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">
                            {{ $action['description'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
