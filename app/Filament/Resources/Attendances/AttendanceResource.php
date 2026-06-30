<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\ManageAttendancePivot;
use App\Filament\Resources\Attendances\Pages\ManageAttendances;
use App\Models\Attendance;
use App\Models\ClassLearner;
use App\Models\HomeroomTeacher;
use App\Models\Learner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'Presensi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Presensi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('learner_id')
                    ->label('Peserta Didik')
                    ->options(function (): array {
                        $user = Filament::auth()->user();
                        $classIds = HomeroomTeacher::where('user_id', $user->id)
                            ->pluck('class_id');

                        if ($classIds->isNotEmpty()) {
                            $learnerIds = ClassLearner::whereIn('class_id', $classIds)
                                ->pluck('learner_id');

                            return Learner::whereIn('id', $learnerIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }

                        if ($user->hasRole('admin')) {
                            return Learner::pluck('name', 'id')->toArray();
                        }

                        return [];
                    })
                    ->searchable()
                    ->required(),
                Select::make('academic_year_id')
                    ->relationship('academicYear', 'name', fn ($query) => $query->where('is_archived', false))
                    ->required(),
                Select::make('semester_id')
                    ->relationship('semester', 'name')
                    ->required(),
                TextInput::make('sick')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('permission')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('absent')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Filament::auth()->user();
                $classIds = HomeroomTeacher::where('user_id', $user->id)
                    ->pluck('class_id');

                if ($classIds->isNotEmpty()) {
                    $learnerIds = ClassLearner::whereIn('class_id', $classIds)
                        ->pluck('learner_id');

                    $query->whereIn('learner_id', $learnerIds);

                    return;
                }

                if (! $user->hasRole('admin')) {
                    $query->whereRaw('0 = 1');
                }
            })
            ->columns([
                TextColumn::make('learner.name')
                    ->label('Peserta Didik')
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->searchable(),
                TextColumn::make('semester.name')
                    ->label('Semester')
                    ->searchable(),
                TextColumn::make('sick')
                    ->label('Sakit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('permission')
                    ->label('Izin')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('absent')
                    ->label('Tanpa Keterangan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendancePivot::route('/'),
            'manage' => ManageAttendances::route('/manage'),
        ];
    }
}
