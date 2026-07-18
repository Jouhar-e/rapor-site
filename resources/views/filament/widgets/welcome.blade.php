<x-filament-widgets::widget>
    <x-filament::section>

        {{-- ==============================
             BAGIAN ATAS: LOGO & SAPAAN
             ============================== --}}
        <div style="display: flex; align-items: center; gap: 1rem;">

            {{-- Logo Sekolah --}}
            @php
            $logoPath = !empty($school?->logo) ? storage_path('app/private/' . $school->logo) : null;
            $logoBase64 = null;

            if ($logoPath && file_exists($logoPath)) {
            $mime = mime_content_type($logoPath);
            $data = base64_encode(file_get_contents($logoPath));
            $logoBase64 = "data:{$mime};base64,{$data}";
            }
            @endphp

            @if ($logoBase64)
            <img
                src="{{ $logoBase64 }}"
                alt="Logo {{ $school->name }}"
                style="width: 60px; height: 60px; border-radius: 0.5rem; object-fit: cover; border: 1px solid #e5e7eb;" />
            @else
            <div style="width: 60px; height: 60px; border-radius: 0.5rem; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                <x-heroicon-o-building-library style="width: 30px; height: 30px; color: #9ca3af;" />
            </div>
            @endif

            {{-- Teks Sapaan --}}
            <div style="flex: 1;">
                <h2 style="font-size: 1.5rem; font-weight: 600; margin: 0; line-height: 1.2;">
                    👋 {{ $greeting }}, {{ $user->name }}
                </h2>
                <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.875rem;">
                    Selamat datang di Dashboard Sistem Informasi Rapot.
                </p>
            </div>

        </div>

        {{-- ==============================
             BAGIAN BAWAH: INFORMASI AKADEMIK
             ============================== --}}
        <div style="margin-top: 1.25rem; display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; background-color: #f9fafb; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; color: #4b5563; border: 1px solid #e5e7eb;">

            {{-- Tahun Ajaran --}}
            @if ($academicYear)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-heroicon-o-academic-cap style="width: 20px; height: 20px; color: #3b82f6;" />
                <span>TA: <strong>{{ $academicYear }}</strong></span>
            </div>
            <span style="color: #d1d5db;">|</span>
            @endif

            {{-- Semester --}}
            @if ($semester)
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-heroicon-o-book-open style="width: 20px; height: 20px; color: #3b82f6;" />
                <span>Semester: <strong>{{ $semester }}</strong></span>
            </div>
            <span style="color: #d1d5db;">|</span>
            @endif

            {{-- Tanggal --}}
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-heroicon-o-calendar style="width: 20px; height: 20px; color: #3b82f6;" />
                <span>{{ $date }}</span>
            </div>

            <span style="color: #d1d5db;">|</span>

            {{-- Jam (Live dengan Alpine.js) --}}
            <div
                style="display: flex; align-items: center; gap: 0.5rem;"
                x-data="{ time: '{{ $time }}' }"
                x-init="
                    setInterval(() => {
                        const now = new Date();
                        const h = String(now.getHours()).padStart(2, '0');
                        const m = String(now.getMinutes()).padStart(2, '0');
                        time = h + '.' + m; // Menggunakan titik sesuai format PHP Anda
                    }, 1000)
                ">
                <x-heroicon-o-clock style="width: 20px; height: 20px; color: #3b82f6;" />
                <span><strong x-text="time"></strong> {{ $timezone }}</span>
            </div>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>