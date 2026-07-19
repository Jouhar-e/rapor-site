<x-filament-panels::page>
    @php
        $cl = $currentClassLearner;
    @endphp

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">

        {{-- LEFT: Learner Info Card --}}
        <x-filament::section style="height: 100%;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem;">
                <div style="display: flex; align-items: center; justify-content: center; width: 72px; height: 72px; border-radius: 9999px; background-color: #e0e7ff; color: #4f46e5; font-size: 1.5rem; font-weight: bold;">
                    {{ substr($learner->name, 0, 2) }}
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">{{ $learner->name }}</h3>
                    <p style="font-size: 0.8125rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                        {{ $learner->nis }} / {{ $learner->nisn ?? '-' }}
                    </p>
                </div>
                <div style="width: 100%; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8125rem; color: #374151;">
                    <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280;">Program</span>
                        <span style="font-weight: 500;">{{ $learner->program?->name ?? '-' }}</span>
                    </div>
                    @if ($cl)
                    <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280;">Kelas</span>
                        <span style="font-weight: 500;">{{ $cl->classes?->name ?? '-' }}</span>
                    </div>
                    @endif
                    @if ($cl?->semester)
                    <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280;">Semester</span>
                        <span style="font-weight: 500;">{{ $cl->semester->name }}</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280;">Jenis Kelamin</span>
                        <span style="font-weight: 500;">{{ $learner->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.375rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280;">Status</span>
                        <span style="font-weight: 500;">{{ ucfirst($learner->status ?? 'aktif') }}</span>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- RIGHT: Tabs / Content --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- Semester Filter --}}
            <x-filament::section>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 0.875rem; font-weight: 500; color: #374151;">Filter Semester:</span>
                    <select wire:model.live="semester_id" style="padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; background-color: white;">
                        <option value="">Semua Semester</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->academicYear->name }} - {{ $sem->name }}</option>
                        @endforeach
                    </select>
                </div>
            </x-filament::section>

            {{-- Grades Table --}}
            <x-filament::section heading="Nilai Akademik" icon="heroicon-o-document-text" collapsible>
                @if ($grades->isEmpty())
                    <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Belum ada nilai.</p>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                            <thead>
                                <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-weight: 600; color: #374151;">Mapel</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">Tugas</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">PTS</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">PAS</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">Praktik</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">Akhir</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">Predikat</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600; color: #374151;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grades as $grade)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 0.5rem 0.75rem; font-weight: 500;">{{ $grade->subject?->name ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center;">{{ $grade->task_score ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center;">{{ $grade->pts_score ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center;">{{ $grade->pas_score ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center;">{{ $grade->practice_score ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center; font-weight: 600;">{{ $grade->final_score ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center;">{{ $grade->predicate ?? '-' }}</td>
                                    <td style="padding: 0.5rem 0.75rem; text-align: center;">
                                        <span style="padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600;
                                            {{ $grade->status === 'published' ? 'background-color: #d1fae5; color: #059669;' : ($grade->status === 'locked' ? 'background-color: #dbeafe; color: #2563eb;' : 'background-color: #fef3c7; color: #d97706;') }}">
                                            {{ ucfirst($grade->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>

            {{-- Attendance Summary --}}
            <x-filament::section heading="Ringkasan Absensi" icon="heroicon-o-clipboard-document-check" collapsible>
                @if ($attendanceSummary['total'] === 0 && !$attendance->isNotEmpty())
                    <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Belum ada data absensi.</p>
                @else
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                        <div style="text-align: center; padding: 0.75rem; background-color: #f0fdf4; border-radius: 0.5rem; border: 1px solid #dcfce7;">
                            <span style="font-size: 1.25rem; font-weight: 700; color: #16a34a;">{{ $attendanceSummary['sick'] }}</span>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Sakit</p>
                        </div>
                        <div style="text-align: center; padding: 0.75rem; background-color: #fefce8; border-radius: 0.5rem; border: 1px solid #fef08a;">
                            <span style="font-size: 1.25rem; font-weight: 700; color: #ca8a04;">{{ $attendanceSummary['permission'] }}</span>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Izin</p>
                        </div>
                        <div style="text-align: center; padding: 0.75rem; background-color: #fef2f2; border-radius: 0.5rem; border: 1px solid #fecaca;">
                            <span style="font-size: 1.25rem; font-weight: 700; color: #dc2626;">{{ $attendanceSummary['absent'] }}</span>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Alpha</p>
                        </div>
                        <div style="text-align: center; padding: 0.75rem; background-color: #f3f4f6; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                            <span style="font-size: 1.25rem; font-weight: 700; color: #4b5563;">{{ $attendanceSummary['total'] }}</span>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Total</p>
                        </div>
                    </div>
                @endif
            </x-filament::section>

            {{-- Extracurriculars --}}
            <x-filament::section heading="Ekstrakurikuler" icon="heroicon-o-star" collapsible>
                @if ($extracurriculars->isEmpty())
                    <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Belum ada kegiatan ekstrakurikuler.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach ($extracurriculars as $extra)
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                                <span style="font-weight: 500; font-size: 0.875rem;">{{ $extra->extracurricular?->name }}</span>
                                @if ($extra->score)
                                    <span style="font-size: 0.75rem; color: #6b7280;">Nilai: {{ $extra->score }}</span>
                                @endif
                                @if ($extra->description)
                                    <span style="font-size: 0.75rem; color: #9ca3af;">{{ $extra->description }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>

            {{-- Homeroom Notes --}}
            <x-filament::section heading="Catatan Wali Kelas" icon="heroicon-o-document-text" collapsible>
                @if ($homeroomNotes->isEmpty())
                    <p style="font-size: 0.875rem; color: #9ca3af; text-align: center; padding: 1rem;">Belum ada catatan wali kelas.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach ($homeroomNotes as $note)
                            <div style="padding: 0.75rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: #374151;">{{ $note->semester?->name ?? '-' }}</span>
                                    <span style="font-size: 0.75rem; color: #9ca3af;">{{ $note->created_at?->format('d M Y') }}</span>
                                </div>
                                <p style="font-size: 0.875rem; color: #4b5563; margin: 0; line-height: 1.5;">
                                    {{ $note->notes ?? $note->description ?? '-' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>

        </div>
    </div>
</x-filament-panels::page>
