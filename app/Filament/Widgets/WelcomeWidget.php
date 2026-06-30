<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\SchoolProfile;
use App\Models\Semester;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getViewData(): array
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;

        $user = Auth::user();
        $schoolProfile = SchoolProfile::first();

        return [
            'user' => $user,
            'school' => $schoolProfile,
            'greeting' => $this->getGreeting(),
            'date' => now()->translatedFormat('l, d F Y'),
            'time' => now()->format('H.i'),
            'timezone' => 'WIB',
            'academicYear' => $activeYear?->name,
            'semester' => $activeSemester?->name,
        ];
    }

    private function getGreeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 10 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
    }
}
