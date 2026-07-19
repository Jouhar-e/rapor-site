<x-filament-widgets::widget>
    <x-filament::section style="height: 100%;">

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 0.75rem; background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;">
                    <x-heroicon-o-clock style="width: 20px; height: 20px;" />
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">
                        Timeline Akademik
                    </h3>
                    @if ($activeSemester)
                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">
                        Semester aktif: <strong>{{ $activeSemester }}</strong>
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @forelse ($timeline as $year)
            <div style="display: flex; gap: 1rem;">

                <div style="display: flex; flex-direction: column; align-items: center; width: 20px; flex-shrink: 0;">
                    <div style="width: 14px; height: 14px; border-radius: 9999px; {{ $year['is_active'] ? 'background-color: #3b82f6; box-shadow: 0 0 0 3px #bfdbfe;' : 'background-color: #d1d5db;' }} border: 2px solid white; flex-shrink: 0;"></div>
                    @if (! $loop->last)
                    <div style="width: 2px; flex: 1; background-color: #e5e7eb; min-height: 20px;"></div>
                    @endif
                </div>

                <div style="flex: 1; min-width: 0; padding-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: #111827;">
                            {{ $year['name'] }}
                        </span>
                        @if ($year['is_active'])
                        <span style="font-size: 0.625rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background-color: #dbeafe; color: #2563eb;">
                            Aktif
                        </span>
                        @endif
                        @if ($year['start_date'])
                        <span style="font-size: 0.75rem; color: #9ca3af;">
                            {{ $year['start_date'] }} - {{ $year['end_date'] ?? '...' }}
                        </span>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @forelse ($year['semesters'] as $sem)
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; {{ $sem['is_active'] ? 'background-color: #eff6ff; border: 1px solid #bfdbfe;' : 'background-color: #f9fafb; border: 1px solid #e5e7eb;' }}">
                            <div style="width: 8px; height: 8px; border-radius: 9999px; flex-shrink: 0; {{ $sem['is_active'] ? 'background-color: #3b82f6;' : 'background-color: #d1d5db;' }}"></div>

                            <span style="font-size: 0.8125rem; font-weight: 500; color: #374151; flex: 1;">
                                {{ $sem['name'] }}
                                @if ($sem['is_active'])
                                <span style="font-size: 0.625rem; font-weight: 600; color: #2563eb;">
                                    &bull; Sekarang
                                </span>
                                @endif
                            </span>

                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 48px; height: 6px; border-radius: 9999px; background-color: #e5e7eb; overflow: hidden;">
                                    <div style="height: 100%; border-radius: 9999px; {{ $sem['grade_percentage'] >= 100 ? 'background-color: #10b981;' : ($sem['grade_percentage'] > 0 ? 'background-color: #f59e0b;' : 'background-color: #ef4444;') }} width: {{ $sem['grade_percentage'] }}%;"></div>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 600; {{ $sem['grade_percentage'] >= 100 ? 'color: #059669;' : ($sem['grade_percentage'] > 0 ? 'color: #d97706;' : 'color: #dc2626;') }} min-width: 2rem; text-align: right;">
                                    {{ $sem['grade_percentage'] }}%
                                </span>
                            </div>
                        </div>
                        @empty
                        <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">
                            Belum ada semester
                        </p>
                        @endforelse
                    </div>
                </div>

            </div>
            @empty
            <div style="text-align: center; padding: 2rem 0; color: #9ca3af; font-size: 0.875rem;">
                Belum ada tahun ajaran.
            </div>
            @endforelse
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
