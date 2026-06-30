<x-filament-widgets::widget class="fi-welcome-widget">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="flex items-center gap-4">
            @if ($school?->logo)
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url($school->logo) }}"
                    alt="{{ $school->name }}"
                    class="h-12 w-12 rounded-full object-cover"
                    loading="lazy"
                />
            @else
                <x-filament-panels::avatar.user
                    :user="$user"
                    size="lg"
                    loading="lazy"
                    class="shrink-0"
                />
            @endif

            <div class="min-w-0 flex-1">
                <h1 class="text-sm font-semibold tracking-tight text-gray-900 dark:text-white">
                    👋 {{ $greeting }}, {{ $user->name }}
                </h1>

                <div class="mt-1 flex flex-wrap items-center gap-x-2 text-xs text-gray-500 dark:text-gray-400">
                    @if ($academicYear)
                        <span>Tahun Ajaran {{ $academicYear }}</span>
                    @endif
                    @if ($semester)
                        <span class="text-gray-300 dark:text-gray-600" aria-hidden="true">&bull;</span>
                        <span>Semester {{ $semester }}</span>
                    @endif
                    <span class="text-gray-300 dark:text-gray-600" aria-hidden="true">&bull;</span>
                    <span>{{ $date }}</span>
                    <span class="text-gray-300 dark:text-gray-600" aria-hidden="true">&bull;</span>
                    <span>
                        <span id="live-clock">{{ $time }}</span> {{ $timezone }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('live-clock').textContent = h + ':' + m;
    }
    setInterval(updateClock, 1000);
});
</script>
@endpush
