<?php

namespace App\Filament\Resources\Learners;

use App\Filament\Resources\Learners\Pages\LearnerProfile;
use App\Filament\Resources\Learners\Pages\ManageLearners;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Learner;
use App\Models\Semester;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LearnerResource extends Resource
{
    protected static ?string $model = Learner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Peserta Didik';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Peserta Didik';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(fn () => Classes::pluck('name', 'id'))
                    ->required(),
                Select::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->options(fn () => AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                    ->required(),
                Select::make('semester_id')
                    ->label('Semester')
                    ->options(function (callable $get): array {
                        $academicYearId = $get('academic_year_id');
                        if (! $academicYearId) {
                            return [];
                        }

                        return Semester::where('academic_year_id', $academicYearId)
                            ->whereHas('academicYear', fn ($q) => $q->where('is_archived', false))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->placeholder('Pilih Semester')
                    ->live(),
                TextInput::make('nis')
                    ->label('NIS')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nisn')
                    ->label('NISN')
                    ->nullable(),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->maxLength(255),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir'),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'keluar' => 'Keluar',
                        'pindah' => 'Pindah',
                    ])
                    ->required()
                    ->default('aktif'),
                Section::make('Data Tambahan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('religion')
                            ->label('Agama')
                            ->options([
                                'Islam' => 'Islam',
                                'Kristen' => 'Kristen',
                                'Katolik' => 'Katolik',
                                'Hindu' => 'Hindu',
                                'Buddha' => 'Buddha',
                                'Konghucu' => 'Konghucu',
                            ]),
                        TextInput::make('child_order')
                            ->label('Anak Ke')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255),
                        DatePicker::make('admission_date')
                            ->label('Tanggal Diterima'),
                        TextInput::make('admission_class')
                            ->label('Kelas Awal')
                            ->maxLength(255),
                        Select::make('admission_status')
                            ->label('Status Masuk')
                            ->options([
                                'baru' => 'Baru',
                                'pindahan' => 'Pindahan',
                            ]),
                        TextInput::make('father_name')
                            ->label('Nama Ayah')
                            ->maxLength(255),
                        TextInput::make('father_job')
                            ->label('Pekerjaan Ayah')
                            ->maxLength(255),
                        TextInput::make('mother_name')
                            ->label('Nama Ibu')
                            ->maxLength(255),
                        TextInput::make('mother_job')
                            ->label('Pekerjaan Ibu')
                            ->maxLength(255),
                        TextInput::make('guardian_name')
                            ->label('Nama Wali')
                            ->maxLength(255),
                        TextInput::make('guardian_job')
                            ->label('Pekerjaan Wali')
                            ->maxLength(255),
                        TextInput::make('report_number')
                            ->label('Nomor Rapor')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->getStateUsing(function (Model $record): string {
                        return $record->classLearners->first()?->classes?->name ?? '-';
                    }),
                TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->getStateUsing(function (Model $record): string {
                        return $record->classLearners->first()?->academicYear?->name ?? '-';
                    }),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('L/P')
                    ->searchable(),
                TextColumn::make('birth_place')
                    ->label('Tempat Lahir')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Tgl Lahir')
                    ->date()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'lulus' => 'warning',
                        'keluar' => 'danger',
                        'pindah' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'keluar' => 'Keluar',
                        'pindah' => 'Pindah',
                        default => $state,
                    })
                    ->searchable(),
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
                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->options(fn () => Classes::pluck('name', 'id'))
                    ->query(fn ($query, array $state) => $query->when(
                        filled($state['value'] ?? null),
                        fn ($q) => $q->whereHas('classLearners', fn ($q) => $q->where('class_id', $state['value']))
                    )),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Profil')
                    ->url(fn (Model $record): string => route('filament.admin.resources.learners.profile', $record)),
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        $classLearner = ClassLearner::where('learner_id', $record->id)->first();
                        if ($classLearner) {
                            $data['class_id'] = $classLearner->class_id;
                            $data['academic_year_id'] = $classLearner->academic_year_id;
                            $data['semester_id'] = $classLearner->semester_id;
                        }

                        return $data;
                    })
                    ->using(function (Model $record, array $data): Model {
                        $classId = $data['class_id'];
                        $academicYearId = $data['academic_year_id'];
                        $semesterId = $data['semester_id'] ?? null;
                        unset($data['class_id'], $data['academic_year_id'], $data['semester_id']);
                        $class = Classes::find($classId);
                        $data['program_id'] = $class?->program_id;
                        $record->update($data);
                        ClassLearner::updateOrCreate(
                            ['learner_id' => $record->id, 'academic_year_id' => $academicYearId],
                            ['class_id' => $classId, 'semester_id' => $semesterId],
                        );

                        return $record;
                    }),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Learner $record) {
                        if ($record->grades()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Peserta didik ini masih memiliki nilai terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->attendances()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Peserta didik ini masih memiliki presensi terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->classLearners()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Peserta didik ini masih memiliki penempatan kelas terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->homeroomNotes()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Peserta didik ini masih memiliki catatan wali kelas terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->learnerExtracurriculars()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Peserta didik ini masih memiliki ekstrakurikuler terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Belum ada warga belajar')
            ->emptyStateDescription('Belum ada warga belajar yang terdaftar.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records) {
                            foreach ($records as $record) {
                                if ($record->classLearners()->count() > 0 || $record->grades()->count() > 0 || $record->attendances()->count() > 0 || $record->learnerExtracurriculars()->count() > 0) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Beberapa peserta didik masih memiliki data terkait.')
                                        ->persistent()
                                        ->send();
                                    $action->halt();

                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLearners::route('/'),
            'profile' => LearnerProfile::route('/{record}/profile'),
        ];
    }
}
