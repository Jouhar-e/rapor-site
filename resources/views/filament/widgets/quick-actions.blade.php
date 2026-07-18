<x-filament-widgets::widget>
    {{-- Tambahan style="height: 100%;" agar tinggi merata --}}
    <x-filament::section style="height: 100%;">

        <div style="margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">
                Pintasan Cepat
            </h3>
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                Akses cepat ke menu utama sistem
            </p>
        </div>

        @php
        $actions = auth()->user()?->hasRole('admin') ? $adminActions : $tutorActions;

        $themeColors = [
        'primary' => ['bg' => '#eff6ff', 'text' => '#2563eb', 'border' => '#bfdbfe'],
        'success' => ['bg' => '#ecfdf5', 'text' => '#059669', 'border' => '#a7f3d0'],
        'warning' => ['bg' => '#fffbeb', 'text' => '#d97706', 'border' => '#fde68a'],
        'info' => ['bg' => '#f0f9ff', 'text' => '#0284c7', 'border' => '#bae6fd'],
        'danger' => ['bg' => '#fef2f2', 'text' => '#dc2626', 'border' => '#fecaca'],
        'gray' => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'border' => '#e5e7eb'],
        ];
        @endphp

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">

            @foreach ($actions as $action)
            @php
            $color = $themeColors[$action['color']] ?? $themeColors['primary'];
            @endphp

            <a href="{{ $action['url'] }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">

                <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; flex-shrink: 0; border-radius: 0.5rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border: 1px solid {{ $color['border'] }};">
                    <x-filament::icon :icon="$action['icon']" style="width: 24px; height: 24px;" />
                </div>

                <div>
                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #111827; margin: 0;">
                        {{ $action['label'] }}
                    </h4>
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                        {{ $action['description'] }}
                    </p>
                </div>

            </a>
            @endforeach

        </div>

    </x-filament::section>
</x-filament-widgets::widget>