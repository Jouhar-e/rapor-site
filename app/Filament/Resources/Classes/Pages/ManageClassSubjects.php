<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Resources\Classes\ClassesResource;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\SubjectGroup;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageClassSubjects extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ClassesResource::class;

    protected static ?string $title = 'Mata Pelajaran';

    protected string $view = 'filament.resources.classes.pages.manage-class-subjects';

    public Classes $class;

    public function mount($record): void
    {
        $this->class = Classes::findOrFail($record);
    }

    public function getBreadcrumbs(): array
    {
        return [
            ClassesResource::getUrl('index') => 'Kelas',
            'Ubah Mata Pelajaran',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Subject::where('class_id', $this->class->id)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('subjectGroup.name')
                    ->label('Kelompok')
                    ->searchable(),
                TextColumn::make('is_active')
                    ->label('Aktif')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif'),
            ])
            ->recordActions([
                Action::make('editSubject')
                    ->label('Ubah')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Select::make('subject_group_id')
                            ->label('Kelompok Mata Pelajaran')
                            ->options(fn () => SubjectGroup::where('is_active', true)->pluck('name', 'id'))
                            ->placeholder('Pilih Kelompok'),
                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name')
                            ->label('Nama Mata Pelajaran')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Keterangan')
                            ->default(null)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->fillForm(fn (Subject $record): array => $record->toArray())
                    ->action(function (Subject $record, array $data): void {
                        $record->update($data);

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Mata pelajaran berhasil diperbarui.')
                            ->success()
                            ->send();
                    })
                    ->modalWidth('lg'),
            ])
            ->emptyStateHeading('Belum ada mata pelajaran')
            ->emptyStateDescription('Kelas ini belum memiliki mata pelajaran. Tambahkan melalui menu Manajemen Mata Pelajaran.')
            ->emptyStateIcon('heroicon-o-book-open');
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
