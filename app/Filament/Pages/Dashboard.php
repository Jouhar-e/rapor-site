<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminCharts;
use App\Filament\Widgets\AdminStatsCardsWidget;
use App\Filament\Widgets\GradeDistributionChart;
use App\Filament\Widgets\ProgressAbsensiWidget;
use App\Filament\Widgets\ProgressNilaiWidget;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\TutorStatsOverview;
use App\Filament\Widgets\WelcomeWidget;
use App\Models\AcademicYear;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(fn () => AcademicYear::pluck('name', 'id'))
                            ->placeholder('Semua')
                            ->native(false)
                            ->selectablePlaceholder(false),
                        Select::make('semester_id')
                            ->label('Semester')
                            ->options(fn () => Semester::pluck('name', 'id'))
                            ->placeholder('Semua')
                            ->native(false)
                            ->selectablePlaceholder(false),
                    ])
                    ->extraAttributes([
                        'class' => 'rounded-2xl shadow-sm max-w-xs',
                    ]),
            ]);
    }

    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 12,
        ];
    }

    public function getWidgets(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        return match (true) {
            $user->hasRole('admin') => $this->getAdminWidgets(),
            $user->hasRole('tutor') => $this->getTutorWidgets(),
            default => [],
        };
    }

    private function getAdminWidgets(): array
    {
        return [
            WelcomeWidget::class,
            AdminStatsCardsWidget::class,
            ProgressNilaiWidget::class,
            ProgressAbsensiWidget::class,
            AdminCharts::class,
            GradeDistributionChart::class,
            RecentActivityWidget::class,
            QuickActionsWidget::class,
        ];
    }

    private function getTutorWidgets(): array
    {
        return [
            WelcomeWidget::class,
            TutorStatsOverview::class,
            QuickActionsWidget::class,
        ];
    }
}
