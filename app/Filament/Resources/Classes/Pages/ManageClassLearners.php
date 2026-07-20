<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Resources\Classes\ClassesResource;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\Learner;
use App\Models\Program;
use App\Models\Semester;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class ManageClassLearners extends Page
{
    protected static string $resource = ClassesResource::class;

    protected static ?string $title = 'Peserta Didik';

    protected string $view = 'filament.resources.classes.pages.manage-class-learners';

    public Classes $class;

    public ?int $academic_year_id = null;

    public ?int $semester_id = null;

    public Collection $registeredLearners;

    public Collection $unregisteredLearners;

    public array $lockedLearnerIds = [];

    public array $previousClassMap = [];

    public ?string $searchRegistered = '';

    public ?string $searchAvailable = '';

    public ?int $filterProgram = null;

    public ?string $filterGender = null;

    public array $selectedRegistered = [];

    public array $selectedAvailable = [];

    public int $perPage = 100;

    public bool $hasMoreAvailable = false;

    public int $classCapacity = 40;

    public function mount($record): void
    {
        $this->class = Classes::findOrFail($record);
        $this->classCapacity = max($this->classCapacity, 1);

        $activeYear = AcademicYear::where('is_active', true)->where('is_archived', false)->first();
        $this->academic_year_id = $activeYear?->id;
        $this->semester_id = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()?->id
            : null;

        $this->loadLearners();
    }

    public function loadLearners(): void
    {
        $allRegisteredThisYear = collect();
        if ($this->academic_year_id) {
            $allRegisteredThisYear = ClassLearner::where('academic_year_id', $this->academic_year_id)
                ->pluck('learner_id');
        }

        $this->registeredLearners = ClassLearner::where('class_id', $this->class->id)
            ->when($this->academic_year_id, fn ($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->when($this->searchRegistered, fn ($q) => $q->whereHas('learner', fn ($q) => $q
                ->where('name', 'like', "%{$this->searchRegistered}%")
                ->orWhere('nis', 'like', "%{$this->searchRegistered}%")))
            ->with(['learner', 'academicYear', 'semester'])
            ->get();

        $regLearnerIds = $this->registeredLearners->pluck('learner_id')->filter()->values();
        $this->lockedLearnerIds = [];
        if ($regLearnerIds->isNotEmpty() && $this->academic_year_id) {
            $this->lockedLearnerIds = Grade::whereIn('learner_id', $regLearnerIds)
                ->where('academic_year_id', $this->academic_year_id)
                ->distinct('learner_id')
                ->pluck('learner_id')
                ->toArray();
        }

        $availableQuery = Learner::where('status', 'aktif')
            ->when($allRegisteredThisYear->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $allRegisteredThisYear))
            ->when($this->searchAvailable, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchAvailable}%")
                    ->orWhere('nis', 'like', "%{$this->searchAvailable}%");
            }))
            ->when($this->filterProgram, fn ($q) => $q->where('program_id', $this->filterProgram))
            ->when($this->filterGender, fn ($q) => $q->where('gender', $this->filterGender))
            ->orderBy('name');

        $totalAvailable = $availableQuery->count();
        $this->unregisteredLearners = $availableQuery->with('program')->take($this->perPage)->get();
        $this->hasMoreAvailable = $this->perPage < $totalAvailable;

        if ($this->unregisteredLearners->isNotEmpty()) {
            $prev = ClassLearner::whereIn('learner_id', $this->unregisteredLearners->pluck('id'))
                ->where('academic_year_id', '<>', $this->academic_year_id)
                ->with('class:id,name')
                ->get()
                ->groupBy('learner_id');

            $this->previousClassMap = $prev->map(function ($items) {
                $last = $items->sortByDesc('academic_year_id')->first();

                return $last?->class?->name ?? '-';
            })->toArray();
        } else {
            $this->previousClassMap = [];
        }
    }

    public function addToClass(int $learnerId): void
    {
        if (! $this->academic_year_id || ! $this->semester_id) {
            Notification::make()
                ->warning()
                ->title('Pilih tahun ajaran dan semester terlebih dahulu')
                ->send();

            return;
        }

        $alreadyInAnyClass = ClassLearner::where('learner_id', $learnerId)
            ->where('academic_year_id', $this->academic_year_id)
            ->exists();

        if ($alreadyInAnyClass) {
            Notification::make()
                ->warning()
                ->title('Peserta didik sudah terdaftar')
                ->body('Peserta didik ini sudah terdaftar di kelas lain pada tahun ajaran yang sama.')
                ->send();

            return;
        }

        ClassLearner::create([
            'learner_id' => $learnerId,
            'class_id' => $this->class->id,
            'academic_year_id' => $this->academic_year_id,
            'semester_id' => $this->semester_id,
        ]);

        $this->selectedAvailable = [];
        $this->loadLearners();
    }

    public function removeFromClass(int $classLearnerId): void
    {
        $cl = ClassLearner::find($classLearnerId);

        if (! $cl) {
            return;
        }

        if (in_array($cl->learner_id, $this->lockedLearnerIds)) {
            Notification::make()
                ->warning()
                ->title('Tidak dapat mengeluarkan')
                ->body('Peserta didik ini masih memiliki data nilai.')
                ->persistent()
                ->send();

            return;
        }

        $cl->delete();
        $this->selectedRegistered = [];
        $this->loadLearners();
    }

    public function addMultipleToClass(): void
    {
        if (empty($this->selectedAvailable)) {
            Notification::make()
                ->warning()
                ->title('Pilih peserta didik terlebih dahulu')
                ->send();

            return;
        }

        if (! $this->academic_year_id || ! $this->semester_id) {
            Notification::make()
                ->warning()
                ->title('Pilih tahun ajaran dan semester terlebih dahulu')
                ->send();

            return;
        }

        $count = 0;
        foreach ($this->selectedAvailable as $learnerId) {
            $exists = ClassLearner::where('learner_id', $learnerId)
                ->where('academic_year_id', $this->academic_year_id)
                ->exists();

            if (! $exists) {
                ClassLearner::create([
                    'learner_id' => $learnerId,
                    'class_id' => $this->class->id,
                    'academic_year_id' => $this->academic_year_id,
                    'semester_id' => $this->semester_id,
                ]);
                $count++;
            }
        }

        Notification::make()
            ->success()
            ->title("{$count} peserta didik berhasil ditambahkan")
            ->send();

        $this->selectedAvailable = [];
        $this->loadLearners();
    }

    public function removeMultipleFromClass(): void
    {
        if (empty($this->selectedRegistered)) {
            Notification::make()
                ->warning()
                ->title('Pilih peserta didik terlebih dahulu')
                ->send();

            return;
        }

        $removed = 0;
        $blocked = 0;
        foreach ($this->selectedRegistered as $clId) {
            $cl = ClassLearner::find($clId);
            if (! $cl) {
                continue;
            }
            if (in_array($cl->learner_id, $this->lockedLearnerIds)) {
                $blocked++;

                continue;
            }
            $cl->delete();
            $removed++;
        }

        if ($blocked > 0) {
            Notification::make()
                ->warning()
                ->title("{$removed} dikeluarkan, {$blocked} tidak bisa (memiliki nilai)")
                ->persistent()
                ->send();
        } elseif ($removed > 0) {
            Notification::make()
                ->success()
                ->title("{$removed} peserta didik berhasil dikeluarkan")
                ->send();
        }

        $this->selectedRegistered = [];
        $this->loadLearners();
    }

    public function moveAllFiltered(): void
    {
        if (! $this->academic_year_id || ! $this->semester_id) {
            Notification::make()
                ->warning()
                ->title('Pilih tahun ajaran dan semester terlebih dahulu')
                ->send();

            return;
        }

        $allRegisteredThisYear = ClassLearner::where('academic_year_id', $this->academic_year_id)
            ->pluck('learner_id');

        $query = Learner::where('status', 'aktif')
            ->when($allRegisteredThisYear->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $allRegisteredThisYear))
            ->when($this->searchAvailable, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchAvailable}%")
                    ->orWhere('nis', 'like', "%{$this->searchAvailable}%");
            }))
            ->when($this->filterProgram, fn ($q) => $q->where('program_id', $this->filterProgram))
            ->when($this->filterGender, fn ($q) => $q->where('gender', $this->filterGender));

        $toMove = $query->pluck('id');

        if ($toMove->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('Tidak ada peserta yang cocok dengan filter')
                ->send();

            return;
        }

        $count = 0;
        foreach ($toMove as $learnerId) {
            $exists = ClassLearner::where('learner_id', $learnerId)
                ->where('academic_year_id', $this->academic_year_id)
                ->exists();

            if (! $exists) {
                ClassLearner::create([
                    'learner_id' => $learnerId,
                    'class_id' => $this->class->id,
                    'academic_year_id' => $this->academic_year_id,
                    'semester_id' => $this->semester_id,
                ]);
                $count++;
            }
        }

        Notification::make()
            ->success()
            ->title("{$count} peserta didik berhasil ditambahkan")
            ->send();

        $this->selectedAvailable = [];
        $this->loadLearners();
    }

    public function clearClass(): void
    {
        $clIds = ClassLearner::where('class_id', $this->class->id)
            ->when($this->academic_year_id, fn ($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->pluck('id');

        $removed = 0;
        $blocked = 0;
        foreach ($clIds as $clId) {
            $cl = ClassLearner::find($clId);
            if (! $cl) {
                continue;
            }
            if (in_array($cl->learner_id, $this->lockedLearnerIds)) {
                $blocked++;

                continue;
            }
            $cl->delete();
            $removed++;
        }

        if ($blocked > 0) {
            Notification::make()
                ->warning()
                ->title("{$removed} dikeluarkan, {$blocked} tidak bisa (memiliki nilai)")
                ->persistent()
                ->send();
        } elseif ($removed > 0) {
            Notification::make()
                ->success()
                ->title("Kelas dikosongkan ({$removed} peserta didik)")
                ->send();
        }

        $this->selectedRegistered = [];
        $this->loadLearners();
    }

    public function selectAllRegistered(): void
    {
        $ids = $this->registeredLearners->pluck('id')->toArray();
        $this->selectedRegistered = count($this->selectedRegistered) === count($ids)
            ? []
            : $ids;
    }

    public function selectAllAvailable(): void
    {
        $ids = $this->unregisteredLearners->pluck('id')->toArray();
        $this->selectedAvailable = count($this->selectedAvailable) === count($ids)
            ? []
            : $ids;
    }

    public function loadMore(): void
    {
        $this->perPage += 100;
        $this->loadLearners();
    }

    public function updatedSearchRegistered(): void
    {
        $this->loadLearners();
    }

    public function updatedSearchAvailable(): void
    {
        $this->perPage = 100;
        $this->loadLearners();
    }

    public function updatedFilterProgram(): void
    {
        $this->perPage = 100;
        $this->loadLearners();
    }

    public function updatedFilterGender(): void
    {
        $this->perPage = 100;
        $this->loadLearners();
    }

    public function updatedAcademicYearId(): void
    {
        $this->semester_id = null;
        $this->perPage = 100;
        $this->loadLearners();
    }

    public function updatedSemesterId(): void
    {
        $this->loadLearners();
    }

    public function getSemesterOptions(): array
    {
        if (! $this->academic_year_id) {
            return [];
        }

        return Semester::where('academic_year_id', $this->academic_year_id)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getProgramOptions(): array
    {
        return Program::where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getClassCount(): int
    {
        return $this->registeredLearners->count();
    }

    public function getCapacityPercent(): float
    {
        if ($this->classCapacity <= 0) {
            return 0;
        }

        return round(($this->getClassCount() / $this->classCapacity) * 100, 1);
    }

    public function getCapacityColor(): string
    {
        $pct = $this->getCapacityPercent();
        if ($pct >= 100) {
            return 'danger';
        }
        if ($pct >= 80) {
            return 'warning';
        }

        return 'success';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => ClassesResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
