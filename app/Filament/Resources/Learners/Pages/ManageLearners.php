<?php

namespace App\Filament\Resources\Learners\Pages;

use App\Filament\Resources\Learners\LearnerResource;
use App\Models\Classes;
use App\Models\ClassLearner;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageLearners extends ManageRecords
{
    protected static string $resource = LearnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Peserta Didik')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-learner'))
                ->color('gray'),
            CreateAction::make()
                ->using(function (array $data, string $model): Model {
                    $classId = $data['class_id'];
                    $academicYearId = $data['academic_year_id'];
                    unset($data['class_id'], $data['academic_year_id']);

                    $class = Classes::find($classId);
                    $data['program_id'] = $class?->program_id;

                    $learner = $model::create($data);

                    ClassLearner::create([
                        'learner_id' => $learner->id,
                        'class_id' => $classId,
                        'academic_year_id' => $academicYearId,
                    ]);

                    return $learner;
                }),
        ];
    }
}
