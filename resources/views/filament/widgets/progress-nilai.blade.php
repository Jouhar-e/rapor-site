<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ==============================
             BAGIAN HEADER & PERSENTASE
             ============================== --}}
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">

            <div style="display: flex; align-items: center; gap: 1rem;">
                {{-- Kotak Ikon (Warna Hijau - Success) --}}
                <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; flex-shrink: 0; border-radius: 0.75rem; background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0;">
                    <x-heroicon-o-academic-cap style="width: 24px; height: 24px;" />
                </div>

                {{-- Judul --}}
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">
                        Progress Penilaian
                    </h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                        Kelengkapan entri nilai akademik
                    </p>
                </div>
            </div>

            {{-- Angka Persentase Besar --}}
            <div>
                <span style="font-size: 1.875rem; font-weight: 700; color: #111827;">
                    {{ $percentage }}%
                </span>
            </div>

        </div>

        {{-- ==============================
             BAGIAN PROGRESS BAR
             ============================== --}}
        <div style="margin-top: 1.5rem;">
            <div style="height: 10px; width: 100%; overflow: hidden; border-radius: 9999px; background-color: #f3f4f6; border: 1px solid #e5e7eb;">
                <div
                    style="height: 100%; border-radius: 9999px; background-color: #10b981; width: {{ $percentage }}%; transition: width 1s ease-out;"></div>
            </div>
        </div>

        {{-- ==============================
             BAGIAN RINCIAN STATUS
             ============================== --}}
        <div style="margin-top: 1.5rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">

            {{-- Selesai --}}
            <div style="display: flex; flex-direction: column; background-color: #f9fafb; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">Selesai</span>
                <span style="margin-top: 0.25rem; font-size: 1.125rem; font-weight: 700; color: #059669;">
                    {{ $completed }}
                </span>
            </div>

            {{-- Tertunda --}}
            <div style="display: flex; flex-direction: column; background-color: #f9fafb; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">Tertunda</span>
                <span style="margin-top: 0.25rem; font-size: 1.125rem; font-weight: 700; color: #d97706;">
                    {{ $pending }}
                </span>
            </div>

            {{-- Belum Mulai --}}
            <div style="display: flex; flex-direction: column; background-color: #f9fafb; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
                <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">Belum Mulai</span>
                <span style="margin-top: 0.25rem; font-size: 1.125rem; font-weight: 700; color: #111827;">
                    {{ $not_started }}
                </span>
            </div>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>