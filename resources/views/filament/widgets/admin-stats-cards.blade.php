<x-filament-widgets::widget class="fi-stats-cards-widget">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            @php
                $t = $theme[$card['color']];
            @endphp
            <div
                class="flex h-[140px] flex-col rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-lg"
                        style="background-color: {{ $t['bg'] }}; color: {{ $t['icon'] }};"
                    >
                        <x-filament::icon :icon="$card['icon']" class="h-4 w-4" />
                    </div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $card['label'] }}
                    </span>
                </div>

                <div class="flex flex-1 items-center justify-center">
                    <span class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $card['value'] }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-medium"
                        style="color: {{ $t['text'] }}"
                    >
                        {{ $card['trend'] }}
                    </span>
                    <svg
                        viewBox="0 0 80 28"
                        class="h-7 w-20"
                        style="color: {{ $t['line'] }}"
                        aria-hidden="true"
                    >
                        <defs>
                            <linearGradient
                                id="sg-{{ $loop->index }}"
                                x1="0"
                                y1="0"
                                x2="0"
                                y2="1"
                            >
                                <stop offset="0%" stop-color="currentColor" stop-opacity="0.2" />
                                <stop offset="100%" stop-color="currentColor" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path
                            d="{{ $card['sparklineFill'] }}"
                            fill="url(#sg-{{ $loop->index }})"
                        />
                        <path
                            d="{{ $card['sparkline'] }}"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
