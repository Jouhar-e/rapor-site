<?php

namespace App\Filament\Resources\Grades\Pages;

use App\Filament\Resources\Grades\GradeResource;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\HomeroomTeacher;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageGrades extends ManageRecords
{
    protected static string $resource = GradeResource::class;

    public ?int $class_id = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Tabel Nilai')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => GradeResource::getUrl('index'))
                ->color('gray'),
            Action::make('import')
                ->label('Import Nilai')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-grade'))
                ->color('gray'),
            CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        if ($homeroomClassIds->isNotEmpty()) {
            // Teachers with exactly 1 homeroom class → auto-select, no filter
            if ($homeroomClassIds->count() === 1) {
                $this->class_id = $homeroomClassIds->first();
            } else {
                // Teachers with multiple homeroom classes → show filter
                $this->class_id = null;
            }
        } elseif ($user->hasRole('admin')) {
            // Admins → show filter (can select any class)
            $this->class_id = null;
        }
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $user = Filament::auth()->user();

        $classIds = [];
        if ($this->class_id) {
            $classIds = [$this->class_id];
        } elseif ($user->hasRole('admin')) {
            $classIds = Classes::pluck('id')->toArray();
        } else {
            $classIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id')->toArray();
        }

        if (empty($classIds) && ! $user->hasRole('admin')) {
            $query->whereRaw('0 = 1');
        } else {
            $classLearnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
            $query->whereIn('learner_id', $classLearnerIds);
        }

        // Pastikan filter dari table (seperti semester_id) tetap diproses
        // Dalam Filament, filter tabel biasanya otomatis diterapkan pada $query.
        // Jika tidak, Anda mungkin perlu menangani filter state secara eksplisit
        // dari $this->tableFilters

        return $query;
    }

    public function getTableDescription(): ?string
    {
        return 'Kelola nilai peserta didik per kelas';
    }
}
