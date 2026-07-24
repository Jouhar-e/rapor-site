<x-filament-widgets::widget>
    {{-- Tambahan style="height: 100%;" agar kotak putih memanjang ke bawah --}}
    <x-filament::section style="height: 100%;">

        {{-- ==============================
             BAGIAN HEADER
             ============================== --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">
                Aktivitas Terbaru
            </h3>
        </div>

        {{-- ==============================
             BAGIAN LIST AKTIVITAS
             ============================== --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            @forelse ($activities as $activity)
            <div style="display: flex; gap: 1rem; align-items: flex-start;">

                {{-- Ikon Bulat Kecil --}}
                <div style="margin-top: 0.125rem; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9999px; background-color: #f3f4f6; color: #6b7280; flex-shrink: 0; border: 1px solid #e5e7eb;">
                    <x-filament::icon icon="heroicon-o-bolt" style="width: 16px; height: 16px;" />
                </div>

                {{-- Detail Aktivitas --}}
                <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 0.875rem; color: #374151; margin: 0; line-height: 1.4; word-wrap: break-word;">
                        {{ $activity->description ?? $activity->event ?? $activity->action ?? 'Melakukan aktivitas pada sistem' }}
                    </p>
                    <span style="font-size: 0.75rem; color: #9ca3af; display: block; margin-top: 0.25rem;">
                        {{ $activity->created_at?->diffForHumans() ?? '-' }}
                    </span>
                </div>

            </div>
            @empty

            {{-- Jika Data Kosong --}}
            <div style="text-align: center; padding: 2rem 0; color: #9ca3af; font-size: 0.875rem;">
                Belum ada aktivitas yang tercatat.
            </div>

            @endforelse

        </div>

    </x-filament::section>
</x-filament-widgets::widget>