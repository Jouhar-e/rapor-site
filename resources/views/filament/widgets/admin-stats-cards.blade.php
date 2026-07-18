<x-filament-widgets::widget>
    {{-- Container Grid: Otomatis membagi menjadi 4 kolom di layar besar, dan susun ke bawah di HP --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">

        @foreach ($cards as $card)
        @php
        $t = $theme[$card['color']];
        @endphp

        {{-- Card Box --}}
        <div style="display: flex; align-items: center; justify-content: space-between; background-color: #ffffff; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb;">

            {{-- Bagian Kiri: Informasi Teks --}}
            <div style="display: flex; flex-direction: column; gap: 0.3rem;">

                {{-- Label (Misal: Total Warga Belajar) --}}
                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">
                    {{ $card['label'] }}
                </span>

                {{-- Value Angka Besar --}}
                <span style="font-size: 1.875rem; font-weight: 700; color: #111827; line-height: 1.2;">
                    {{ $card['value'] }}
                </span>

                {{-- Trend (Misal: +2 bulan ini) --}}
                <span style="font-size: 0.875rem; font-weight: 500; color: {{ $t['text'] ?? $t['icon'] }};">
                    {{ $card['trend'] }}
                </span>

            </div>

            {{-- Bagian Kanan: Ikon dalam Kotak --}}
            <div style="display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; flex-shrink: 0; border-radius: 1rem; background-color: {{ $t['bg'] }}; color: {{ $t['icon'] }};">
                <x-filament::icon :icon="$card['icon']" style="width: 28px; height: 28px;" />
            </div>

        </div>

        @endforeach

    </div>
</x-filament-widgets::widget>