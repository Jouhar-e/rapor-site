<?php

namespace App\Filament\Resources\Classes\Pages;

use App\Filament\Resources\Classes\ClassesResource;
use App\Filament\Resources\Subjects\SubjectResource;
use App\Models\Classes;
use App\Models\Subject;
use Filament\Actions\Action;
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
            // Format yang benar:
            ClassesResource::getUrl('index') => 'Kelas',
            'Mata Pelajaran', // Judul halaman ini
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
                    ->searchable()
                    ->url(fn (Subject $record): string => SubjectResource::getUrl('index'))
                    ->openUrlInNewTab(),
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
                Action::make('openSubject')
                    ->label('Buka di Manajemen Mata Pelajaran')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Subject $record): string => SubjectResource::getUrl('index'))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Belum ada mata pelajaran')
            ->emptyStateDescription('Kelas ini belum memiliki mata pelajaran. Tambahkan mata pelajaran melalui form edit kelas atau menu Manajemen Mata Pelajaran.')
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
