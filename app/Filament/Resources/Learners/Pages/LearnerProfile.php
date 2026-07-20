<?php

namespace App\Filament\Resources\Learners\Pages;

use App\Filament\Resources\Learners\LearnerResource;
use App\Models\Grade;
use App\Models\HomeroomNote;
use App\Models\Learner;
use App\Models\LearnerExtracurricular;
use App\Models\Semester;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class LearnerProfile extends Page
{
    protected static string $resource = LearnerResource::class;

    protected string $view = 'filament.resources.learners.pages.learner-profile';

    public Learner $learner;

    public ?int $semester_id = null;

    public function getBreadcrumbs(): array
    {
        return [
            LearnerResource::getUrl('index') => 'Peserta Didik',
            $this->learner->name,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => LearnerResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    public function mount($record): void
    {
        $this->learner = Learner::with([
            'program',
            'classLearners.classes',
            'classLearners.semester.academicYear',
        ])->findOrFail($record);
    }

    protected function getViewData(): array
    {
        $activeSemester = Semester::whereHas(
            'academicYear',
            fn ($q) => $q->where('is_active', true)
        )->where('is_active', true)->first();

        $semesterId = $this->semester_id ?? $activeSemester?->id;

        $grades = Grade::with('subject')
            ->where('learner_id', $this->learner->id)
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->get();

        $attendance = $this->learner->attendances()
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->get();

        $attendanceSummary = [
            'sick' => $attendance->sum('sick'),
            'permission' => $attendance->sum('permission'),
            'absent' => $attendance->sum('absent'),
            'total' => $attendance->sum('sick') + $attendance->sum('permission') + $attendance->sum('absent'),
        ];

        $extracurriculars = LearnerExtracurricular::with('extracurricular')
            ->where('learner_id', $this->learner->id)
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->get();

        $homeroomNotes = HomeroomNote::where('learner_id', $this->learner->id)
            ->when($semesterId, fn ($q) => $q->where('semester_id', $semesterId))
            ->latest()
            ->get();

        $semesters = Semester::whereHas(
            'academicYear',
            fn ($q) => $q->where('is_archived', false)
        )->with('academicYear')->orderByDesc('academic_year_id')->get();

        $currentClassLearner = $this->learner->classLearners()
            ->with('classes', 'semester')
            ->when($activeSemester, fn ($q) => $q->where('semester_id', $activeSemester->id))
            ->latest()
            ->first();

        return [
            'grades' => $grades,
            'attendanceSummary' => $attendanceSummary,
            'extracurriculars' => $extracurriculars,
            'homeroomNotes' => $homeroomNotes,
            'semesters' => $semesters,
            'currentClassLearner' => $currentClassLearner,
            'activeSemester' => $activeSemester,
        ];
    }

    public function updatedSemesterId(): void
    {
        //
    }
}
