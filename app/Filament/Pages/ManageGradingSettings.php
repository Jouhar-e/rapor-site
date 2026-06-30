<?php

namespace App\Filament\Pages;

use App\Models\GradingSetting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageGradingSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Pengaturan Penilaian';

    protected ?string $heading = 'Pengaturan Penilaian';

    protected string $view = 'filament.pages.manage-grading-settings';

    public static function canAccess(): bool
    {
        return auth()->user()->can('setting.view');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $setting = GradingSetting::first()?->toArray() ?? [
            'task_percentage' => 30,
            'pts_percentage' => 30,
            'pas_percentage' => 40,
            'practice_percentage' => 0,
            'min_score' => 0,
            'max_score' => 100,
            'rounding_digits' => 0,
        ];

        $this->form->fill($setting);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bobot Nilai')
                    ->description('Total bobot harus 100%')
                    ->schema([
                        TextInput::make('task_percentage')
                            ->label('Tugas (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                        TextInput::make('pts_percentage')
                            ->label('PTS (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                        TextInput::make('pas_percentage')
                            ->label('PAS (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                        TextInput::make('practice_percentage')
                            ->label('Praktik (%)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])->columns(4),
                Section::make('Rentang Nilai')
                    ->schema([
                        TextInput::make('min_score')
                            ->label('Nilai Minimal')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        TextInput::make('max_score')
                            ->label('Nilai Maksimal')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        TextInput::make('rounding_digits')
                            ->label('Pembulatan (digit)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $total = $data['task_percentage'] + $data['pts_percentage']
            + $data['pas_percentage'] + $data['practice_percentage'];

        if (abs((float) $total - 100.0) > 0.001) {
            $this->addError('data.task_percentage', "Total bobot harus 100%, saat ini {$total}%.");

            Notification::make()
                ->warning()
                ->title("Total bobot harus 100%, saat ini {$total}%.")
                ->persistent()
                ->send();

            return;
        }

        $setting = GradingSetting::first();

        if ($setting) {
            $setting->update($data);
        } else {
            GradingSetting::create($data);
        }

        Notification::make()
            ->success()
            ->title('Pengaturan penilaian berhasil disimpan.')
            ->send();
    }
}
