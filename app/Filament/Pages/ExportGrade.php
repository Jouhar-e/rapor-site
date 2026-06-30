<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Grades\GradeResource;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Subject;
use App\Services\ExcelService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportGrade extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Ekspor Nilai';

    protected ?string $heading = 'Ekspor Nilai';

    protected string $view = 'filament.pages.export-grade';

    public static function canAccess(): bool
    {
        return auth()->user()->can('export.grade');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('semester_id', null)),
                        Select::make('semester_id')
                            ->label('Semester')
                            ->options(fn (callable $get) => $get('academic_year_id')
                                ? Semester::where('academic_year_id', $get('academic_year_id'))->whereHas('academicYear', fn ($q) => $q->where('is_archived', false))->pluck('name', 'id')
                                : [])
                            ->required()
                            ->reactive(),
                        Select::make('class_id')
                            ->label('Kelas')
                            ->options(Classes::pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('subject_id', null)),
                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->options(fn (callable $get) => $get('class_id')
                                ? Subject::where('class_id', $get('class_id'))->pluck('name', 'id')
                                : [])
                            ->required()
                            ->reactive(),
                    ]),
            ])
            ->statePath('data');
    }

    public function export(): StreamedResponse
    {
        $state = $this->form->getState();

        $grades = Grade::with(['learner', 'subject'])
            ->where('academic_year_id', $state['academic_year_id'])
            ->where('semester_id', $state['semester_id'])
            ->where('subject_id', $state['subject_id'])
            ->whereHas('learner', function ($q) use ($state) {
                $q->whereHas('classLearners', function ($q) use ($state) {
                    $q->where('class_id', $state['class_id'])
                        ->where('academic_year_id', $state['academic_year_id']);
                });
            })
            ->join('learners', 'grades.learner_id', '=', 'learners.id')
            ->orderBy('learners.name')
            ->select('grades.*')
            ->get();

        $subject = Subject::find($state['subject_id']);
        $academicYear = AcademicYear::find($state['academic_year_id']);
        $semester = Semester::find($state['semester_id']);
        $className = Classes::find($state['class_id'])?->name ?? '';

        $excel = app(ExcelService::class);

        return $excel->exportGrades(
            $grades,
            $subject?->name ?? '',
            $className,
            $academicYear?->name ?? '',
            $semester?->name ?? ''
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Input Nilai')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => GradeResource::getUrl('manage'))
                ->color('gray'),
            Action::make('export')
                ->label('Unduh Excel')
                ->action('export'),
        ];
    }
}
