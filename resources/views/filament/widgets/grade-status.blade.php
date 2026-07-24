<x-filament-widgets::widget>
    <x-filament::section style="height: 100%;">

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 0.75rem; background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0;">
                    <x-filament::icon icon="heroicon-o-check-badge" style="width: 20px; height: 20px;" />
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">
                        Status Nilai Per Kelas
                    </h3>
                    @if ($semesterName)
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">
                        Semester: <strong>{{ $semesterName }}</strong>
                    </p>
                    @endif
                </div>
            </div>
        </div>

        @if ($classes->isEmpty())
        <div style="text-align: center; padding: 2rem 0; color: #9ca3af; font-size: 0.875rem;">
            Tidak ada kelas aktif di semester ini.
        </div>
        @else
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">

            @foreach ($classes as $class)
            <div style="padding: 0.75rem 1rem; border-radius: 0.75rem; background-color: #f9fafb; border: 1px solid #e5e7eb;">

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                        <div style="width: 10px; height: 10px; border-radius: 9999px; flex-shrink: 0;
                            {{ $class['status'] === 'complete' ? 'background-color: #10b981;' : ($class['status'] === 'partial' ? 'background-color: #f59e0b;' : 'background-color: #ef4444;') }}">
                        </div>
                        <span style="font-size: 0.8125rem; font-weight: 600; color: #111827;">
                            {{ $class['name'] }}
                        </span>
                        @if ($class['program'])
                        <span style="font-size: 0.6875rem; color: #9ca3af;">
                            {{ $class['program'] }}{{ $class['phase'] ? ' - '.$class['phase'] : '' }}
                        </span>
                        @endif
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 600; color: #6b7280;">
                        {{ $class['percentage'] }}%
                    </span>
                </div>

                <div style="height: 6px; width: 100%; border-radius: 9999px; background-color: #e5e7eb; overflow: hidden; margin-bottom: 0.5rem;">
                    <div style="height: 100%; border-radius: 9999px;
                        {{ $class['status'] === 'complete' ? 'background-color: #10b981;' : ($class['status'] === 'partial' ? 'background-color: #f59e0b;' : 'background-color: #ef4444;') }}
                        width: {{ $class['percentage'] }}%;"></div>
                </div>

                <div style="display: flex; gap: 1rem; font-size: 0.75rem;">
                    <span style="color: #059669;">
                        &#10003; {{ $class['completed'] }} Selesai
                    </span>
                    <span style="color: #d97706;">
                        &#9679; {{ $class['draft'] }} Draft
                    </span>
                    <span style="color: #9ca3af;">
                        &#8212; {{ $class['not_started'] }} Belum
                    </span>
                    <span style="color: #6b7280; margin-left: auto;">
                        {{ $class['total_learners'] }} WB
                    </span>
                </div>

            </div>
            @endforeach

        </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
