<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Program;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class ExploreClasses extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sitemap';

    protected string $view = 'filament.pages.explore-classes';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Jelajahi Kelas';

    public ?int $academic_year_id = null;

    public ?int $program_id = null;

    public ?int $class_id = null;

    public array $treeData = [];

    public int $totalClasses = 0;

    public int $totalLearners = 0;

    public function getBreadcrumbs(): array
    {
        return [
            'Master Data' => null,
            'Jelajahi Kelas' => null,
        ];
    }

    public function mount(): void
    {
        $this->buildTree();
    }

    public function updated($propertyName): void
    {
        $this->buildTree();
    }

    protected function getFilterOptions(): array
    {
        $years = AcademicYear::where('is_archived', false)->orderBy('name', 'desc')->get();

        $classIds = ClassLearner::query()
            ->when($this->academic_year_id, fn ($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->distinct('class_id')
            ->pluck('class_id');

        $classes = Classes::with('program')
            ->whereIn('id', $classIds)
            ->get();

        return [$years, $classes];
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->placeholder('Semua Tahun Ajaran')
                    ->live()
                    ->options(fn () => AcademicYear::where('is_archived', false)
                        ->orderBy('name', 'desc')
                        ->pluck('name', 'id'))
                    ->afterStateUpdated(fn () => null),
                Select::make('program_id')
                    ->label('Program')
                    ->placeholder('Semua Program')
                    ->live()
                    ->options(fn () => Program::where('is_active', true)->pluck('name', 'id'))
                    ->afterStateUpdated(fn () => null),
                Select::make('class_id')
                    ->label('Kelas')
                    ->placeholder('Semua Kelas')
                    ->live()
                    ->options(fn () => Classes::query()
                        ->when($this->program_id, fn ($q) => $q->where('program_id', $this->program_id))
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->afterStateUpdated(fn () => null),
            ]);
    }

    public function buildTree(): void
    {
        $years = AcademicYear::where('is_archived', false)
            ->orderBy('name', 'desc')
            ->when($this->academic_year_id, fn ($q) => $q->where('id', $this->academic_year_id))
            ->get();

        $classLearners = ClassLearner::with('learner')
            ->whereIn('academic_year_id', $years->pluck('id'))
            ->when($this->class_id, fn ($q) => $q->where('class_id', $this->class_id))
            ->get()
            ->groupBy(['academic_year_id', 'class_id']);

        $allClassIds = collect();
        foreach ($classLearners as $yearId => $classes) {
            $allClassIds = $allClassIds->merge($classes->keys());
        }
        $allClassIds = $allClassIds->unique();

        $classes = Classes::with('program')
            ->whereIn('id', $allClassIds)
            ->when($this->program_id, fn ($q) => $q->where('program_id', $this->program_id))
            ->get()
            ->keyBy('id');

        $tree = [];
        foreach ($years as $year) {
            $yearClassesData = $classLearners->get($year->id, collect());
            $yearClasses = [];

            foreach ($yearClassesData as $classId => $cls) {
                $class = $classes->get($classId);
                if (! $class) {
                    continue;
                }

                $learners = $cls->map(fn ($cl) => $cl->learner)->filter()->values();

                $yearClasses[] = [
                    'class' => $class,
                    'learners' => $learners,
                ];

                $this->totalLearners += $learners->count();
                $this->totalClasses++;
            }

            if (! empty($yearClasses)) {
                $totalLearnersInYear = collect($yearClasses)->sum(fn ($cg) => count($cg['learners']));

                $tree[] = [
                    'year' => $year,
                    'classes' => $yearClasses,
                    'totalLearners' => $totalLearnersInYear,
                ];
            }
        }

        $this->treeData = $tree;
    }
}
