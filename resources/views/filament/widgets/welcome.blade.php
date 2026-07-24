<x-filament-widgets::widget>
    <x-filament::section class="h-full">

        {{-- ==============================
             BAGIAN ATAS: LOGO & SAPAAN
             ============================== --}}
        <div class="flex items-center gap-4">

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
                <img src="{{ $logoBase64 }}" alt="Logo {{ $school->name }}"
                    style="width: 60px; height: 60px; min-width: 60px; min-height: 60px; max-width: 60px; max-height: 60px; object-fit: cover;"
                    class="rounded-lg ring-1 ring-gray-200 dark:ring-gray-700" />
            @else
                <div style="width: 60px; height: 60px; min-width: 60px; min-height: 60px;"
                    class="flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
                    <x-filament::icon icon="heroicon-o-building-library"
                        style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                        class="text-gray-400 dark:text-gray-500" />
                </div>
            @endif

            {{-- Teks Sapaan --}}
            <div class="flex-1 min-w-0">
                <h2 class="m-0 text-2xl font-semibold leading-tight text-gray-900 dark:text-white">
                    {{ $greeting }}, {{ $user->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Selamat datang di Dashboard Sistem Informasi Rapot.
                </p>
            </div>

        </div>

        {{-- ==============================
             BAGIAN BAWAH: INFORMASI AKADEMIK
             ============================== --}}
        <div
            class="flex flex-wrap items-center gap-4 px-4 py-3 mt-5 text-sm font-medium bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg">

            {{-- Tahun Ajaran --}}
            @if ($academicYear)
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-academic-cap"
                        style="width: 20px; height: 20px; min-width: 20px; min-height: 20px;"
                        class="text-blue-500 dark:text-blue-400" />
                    <span class="text-gray-600 dark:text-gray-300">TA: <strong
                            class="text-gray-900 dark:text-white">{{ $academicYear }}</strong></span>
                </div>
                <span class="text-gray-300 dark:text-gray-600">|</span>
            @endif

            {{-- Semester --}}
            @if ($semester)
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-book-open"
                        style="width: 20px; height: 20px; min-width: 20px; min-height: 20px;"
                        class="text-blue-500 dark:text-blue-400" />
                    <span class="text-gray-600 dark:text-gray-300">Semester: <strong
                            class="text-gray-900 dark:text-white">{{ $semester }}</strong></span>
                </div>
                <span class="text-gray-300 dark:text-gray-600">|</span>
            @endif

            {{-- Tanggal --}}
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-calendar"
                    style="width: 20px; height: 20px; min-width: 20px; min-height: 20px;"
                    class="text-blue-500 dark:text-blue-400" />
                <span class="text-gray-600 dark:text-gray-300">{{ $date }}</span>
            </div>

            <span class="text-gray-300 dark:text-gray-600">|</span>

            {{-- Jam (Live dengan Alpine.js) --}}
            <div class="flex items-center gap-2" x-data="{ time: '{{ $time }}' }" x-init="setInterval(() => {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                time = h + '.' + m;
            }, 1000)">
                <x-filament::icon icon="heroicon-o-clock"
                    style="width: 20px; height: 20px; min-width: 20px; min-height: 20px;"
                    class="text-blue-500 dark:text-blue-400" />
                <span class="text-gray-600 dark:text-gray-300"><strong class="text-gray-900 dark:text-white"
                        x-text="time"></strong> {{ $timezone }}</span>
            </div>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>
