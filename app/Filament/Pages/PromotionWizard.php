<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\PromotionMapping;
use App\Services\PromotionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use UnitEnum;

class PromotionWizard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-circle';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 8;

    protected static ?string $title = 'Kenaikan Kelas';

    protected ?string $heading = 'Kenaikan Kelas';

    protected string $view = 'filament.pages.promotion-wizard';

    public static function canAccess(): bool
    {
        return auth()->user()->can('promotion.view');
    }

    public ?array $data = [];

    public array $previewData = [];

    public array $summary = [];

    public function mount(): void
    {
        $this->form->fill([
            'mappings' => [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Tahun Ajaran')
                        ->icon('heroicon-o-calendar')
                        ->columns(2)
                        ->schema([
                            Select::make('source_academic_year_id')
                                ->label('Tahun Ajaran Sumber')
                                ->options(fn (): array => AcademicYear::query()
                                    ->orderByDesc('name')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state): void {
                                    $mappings = $state
                                        ? PromotionMapping::where('academic_year_id', $state)
                                            ->whereNull('promoted_at')
                                            ->get(['source_class_id', 'destination_class_id'])
                                            ->toArray()
                                        : [];
                                    $set('mappings', $mappings);
                                }),
                            Select::make('year_option')
                                ->label('Opsi Tahun Ajaran Tujuan')
                                ->options([
                                    'existing' => 'Pilih Tahun Ajaran yang Ada',
                                    'new' => 'Buat Tahun Ajaran Baru',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set): void {
                                    $set('dest_academic_year_id', null);
                                }),
                            Select::make('dest_academic_year_id')
                                ->label('Tahun Ajaran Tujuan')
                                ->options(fn (callable $get): array => $get('source_academic_year_id')
                                    ? AcademicYear::where('id', '!=', $get('source_academic_year_id'))
                                        ->orderByDesc('name')
                                        ->pluck('name', 'id')
                                        ->toArray()
                                    : [])
                                ->visible(fn (callable $get): bool => $get('year_option') === 'existing')
                                ->required(fn (callable $get): bool => $get('year_option') === 'existing')
                                ->reactive(),
                            Grid::make()
                                ->visible(fn (callable $get): bool => $get('year_option') === 'new')
                                ->schema([
                                    TextInput::make('new_year_name')
                                        ->label('Nama Tahun Ajaran Baru')
                                        ->placeholder('Contoh: 2026/2027')
                                        ->required(fn (callable $get): bool => $get('year_option') === 'new'),
                                    DatePicker::make('new_year_start_date')
                                        ->label('Tanggal Mulai')
                                        ->required(fn (callable $get): bool => $get('year_option') === 'new'),
                                    DatePicker::make('new_year_end_date')
                                        ->label('Tanggal Selesai')
                                        ->required(fn (callable $get): bool => $get('year_option') === 'new'),
                                ])->columns(2),
                        ]),
                    Step::make('Mapping Kelas')
                        ->icon('heroicon-o-arrows-right-left')
                        ->afterValidation(function (): void {
                            $this->generatePreview();
                        })
                        ->schema([
                            Repeater::make('mappings')
                                ->label('')
                                ->schema([
                                    Select::make('source_class_id')
                                        ->label('Kelas Sumber')
                                        ->options(fn (callable $get): array => $this->getSourceClassOptions(
                                            $get('../../source_academic_year_id')
                                        ))
                                        ->required()
                                        ->reactive()
                                        ->distinct()
                                        ->disableOptionWhen(fn (string $value, callable $get): bool => collect($get('../../mappings') ?? [])
                                            ->where('source_class_id', $value)
                                            ->count() > 1),
                                    Select::make('destination_class_id')
                                        ->label('Kelas Tujuan')
                                        ->options(fn (callable $get): array => $this->getDestinationClassOptions(
                                            $get('../../dest_academic_year_id')
                                        ))
                                        ->required(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel('Tambah Mapping Kelas'),
                        ]),
                    Step::make('Tinjauan')
                        ->icon('heroicon-o-eye')
                        ->schema([
                            View::make('filament.components.promotion-review'),
                        ]),
                    Step::make('Konfirmasi')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            View::make('filament.components.promotion-confirmation'),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                        <x-filament::button
                            type="submit"
                            size="lg"
                            color="success"
                            icon="heroicon-o-check-circle"
                        >
                            Konfirmasi & Eksekusi
                        </x-filament::button>
                    BLADE)))
                    ->nextAction(
                        fn (Action $action): Action => $action->label('Lanjut'),
                    )
                    ->previousAction(
                        fn (Action $action): Action => $action->label('Kembali'),
                    ),
            ])
            ->statePath('data');
    }

    public function getSourceClassOptions(?int $academicYearId): array
    {
        if (! $academicYearId) {
            return [];
        }

        $year = AcademicYear::find($academicYearId);

        if (! $year) {
            return [];
        }

        return app(PromotionService::class)
            ->getActiveClasses($year)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getDestinationClassOptions(?int $academicYearId): array
    {
        $query = Classes::query()->orderBy('name');

        if ($academicYearId) {
            $query->where(function ($q) use ($academicYearId): void {
                $q->whereHas('classLearners', fn ($sq) => $sq->where('academic_year_id', $academicYearId))
                    ->orWhere('status', 'aktif');
            });
        }

        return $query->pluck('name', 'id')->toArray();
    }

    public function generatePreview(): void
    {
        $data = $this->form->getState();
        $service = app(PromotionService::class);

        $preview = [];
        $totalLearners = 0;

        foreach (($data['mappings'] ?? []) as $mapping) {
            $sourceClass = Classes::find($mapping['source_class_id']);
            $destClass = Classes::find($mapping['destination_class_id']);

            if (! $sourceClass || ! $destClass) {
                continue;
            }

            $candidates = $service->getPromotionCandidates($sourceClass, AcademicYear::find($data['source_academic_year_id']));
            $count = $candidates->count();
            $totalLearners += $count;

            $preview[] = [
                'source_class' => $sourceClass,
                'destination_class' => $destClass,
                'learners' => $candidates,
                'count' => $count,
            ];
        }

        $this->previewData = $preview;
        $this->summary = [
            'total_learners' => $totalLearners,
            'total_mappings' => count($preview),
            'source_year' => AcademicYear::find($data['source_academic_year_id'])?->name,
            'dest_year' => $this->getDestYearName($data),
        ];
    }

    protected function getDestYearName(array $data): string
    {
        if ($data['year_option'] === 'existing' && $data['dest_academic_year_id']) {
            return AcademicYear::find($data['dest_academic_year_id'])?->name ?? '-';
        }

        return $data['new_year_name'] ?? '-';
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $sourceYear = AcademicYear::find($data['source_academic_year_id']);

        if ($data['year_option'] === 'new') {
            $destYear = AcademicYear::create([
                'name' => $data['new_year_name'],
                'start_date' => $data['new_year_start_date'],
                'end_date' => $data['new_year_end_date'],
                'is_active' => false,
                'is_archived' => false,
            ]);
        } else {
            $destYear = AcademicYear::find($data['dest_academic_year_id']);
        }

        if (! $sourceYear || ! $destYear) {
            Notification::make()
                ->danger()
                ->title('Gagal memproses kenaikan kelas.')
                ->send();

            return;
        }

        $service = app(PromotionService::class);

        DB::transaction(function () use ($data, $sourceYear): void {
            $submittedKeys = collect($data['mappings'] ?? [])->map(fn ($m) => $m['source_class_id'].'-'.$m['destination_class_id']);

            PromotionMapping::where('academic_year_id', $sourceYear->id)
                ->whereNull('promoted_at')
                ->get()
                ->each(function (PromotionMapping $mapping) use ($submittedKeys): void {
                    $key = $mapping->source_class_id.'-'.$mapping->destination_class_id;
                    if (! $submittedKeys->contains($key)) {
                        $mapping->delete();
                    }
                });

            foreach (($data['mappings'] ?? []) as $mapping) {
                PromotionMapping::firstOrCreate(
                    [
                        'source_class_id' => $mapping['source_class_id'],
                        'destination_class_id' => $mapping['destination_class_id'],
                        'academic_year_id' => $sourceYear->id,
                    ],
                );
            }
        });

        $service->executePromotions($sourceYear, $destYear);

        $service->archiveYear($sourceYear);

        $service->activateYear($destYear);

        Notification::make()
            ->success()
            ->title('Kenaikan kelas berhasil diproses.')
            ->send();

        $this->redirect(static::getUrl());
    }
}
