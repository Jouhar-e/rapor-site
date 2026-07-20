<?php

namespace App\Filament\Resources\Classes;

use App\Filament\Resources\Classes\Pages\ManageClasses;
use App\Filament\Resources\Classes\Pages\ManageClassLearners;
use App\Filament\Resources\Classes\Pages\ManageClassSubjects;
use App\Models\Classes;
use App\Models\Phase;
use App\Models\Subject;
use App\Models\SubjectGroup;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ClassesResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Kelas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kelas';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Program & Fase')
                        ->icon('heroicon-o-academic-cap')
                        ->columns(2)
                        ->schema([
                            Select::make('program_id')
                                ->label('Program')
                                ->relationship('program', 'name')
                                ->required(),
                            Select::make('phase_id')
                                ->label('Fase')
                                ->options(fn () => Phase::where('is_active', true)->pluck('name', 'id'))
                                ->placeholder('Pilih Fase'),
                        ]),
                    Step::make('Identitas Kelas')
                        ->icon('heroicon-o-building-library')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Kelas')
                                ->required()
                                ->maxLength(255),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'aktif' => 'Aktif',
                                    'nonaktif' => 'Nonaktif',
                                    'lulus' => 'Lulus',
                                ])
                                ->required()
                                ->default('aktif'),
                            Textarea::make('description')
                                ->label('Keterangan')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                    Step::make('Mata Pelajaran')
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            Repeater::make('subjects')
                                ->relationship('subjects')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nama Mata Pelajaran')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('code')
                                        ->label('Kode')
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('subject_group_id')
                                        ->label('Kelompok')
                                        ->options(fn () => SubjectGroup::where('is_active', true)->pluck('name', 'id')),
                                    Textarea::make('description')
                                        ->label('Keterangan')
                                        ->default(null),
                                    Toggle::make('is_active')
                                        ->label('Aktif')
                                        ->default(true),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel('Tambah Mata Pelajaran'),
                        ]),
                ])
                    ->nextAction(fn (Action $action): Action => $action->label('Lanjut'))
                    ->previousAction(fn (Action $action): Action => $action->label('Kembali')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                TextColumn::make('phase.name')
                    ->label('Fase')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Kelas')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        'lulus' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'lulus' => 'Lulus',
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
                //
            ])
            ->recordActions([
                Action::make('manageLearners')
                    ->label('Peserta Didik')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->url(fn (Model $record): string => route('filament.admin.resources.classes.learners', $record)),
                Action::make('manageSubjects')
                    ->label('Mata Pelajaran')
                    ->icon('heroicon-o-book-open')
                    ->color('warning')
                    ->url(fn (Model $record): string => route('filament.admin.resources.classes.subjects', $record)),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $classNum = '';
                        preg_match('/(\d+)/', $data['name'] ?? '', $matches);
                        $classNum = $matches[1] ?? '';

                        $existingCodes = Subject::where('class_id', $data['id'] ?? 0)->pluck('code')->toArray();
                        $newCodes = [];

                        foreach ($data['subjects'] ?? [] as $key => $subject) {
                            if (empty($subject['code']) && ! empty($subject['name'])) {
                                $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $subject['name']), 0, 3));
                                $baseCode = $prefix.($classNum ? '-'.$classNum : '');

                                $code = $baseCode;
                                $counter = 1;
                                while (in_array($code, $existingCodes) || in_array($code, $newCodes)) {
                                    $code = $baseCode.'_'.$counter;
                                    $counter++;
                                }
                                $newCodes[] = $code;

                                $data['subjects'][$key]['code'] = $code;
                            }
                        }

                        return $data;
                    }),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Classes $record) {
                        if ($record->classLearners()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki peserta didik terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->subjects()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki mata pelajaran terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->homeroomTeachers()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki wali kelas terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->sourcePromotionMappings()->count() > 0 || $record->destinationPromotionMappings()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki pemetaan kenaikan kelas terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Belum ada kelas')
            ->emptyStateDescription('Belum ada kelas yang terdaftar. Silakan buat kelas baru.')
            ->emptyStateIcon('heroicon-o-building-library')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records) {
                            foreach ($records as $record) {
                                if ($record->classLearners()->count() > 0 || $record->subjects()->count() > 0 || $record->homeroomTeachers()->count() > 0 || $record->sourcePromotionMappings()->count() > 0 || $record->destinationPromotionMappings()->count() > 0) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Beberapa kelas masih memiliki data terkait.')
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
            'index' => ManageClasses::route('/'),
            'learners' => ManageClassLearners::route('/{record}/learners'),
            'subjects' => ManageClassSubjects::route('/{record}/subjects'),
        ];
    }
}
