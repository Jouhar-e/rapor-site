<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'md' => 12,
            'xl' => 8,
        ];
    }

    protected function getViewData(): array
    {
        return [
            'adminActions' => [
                [
                    'label' => 'Input Nilai',
                    'description' => 'Kelola nilai akademik',
                    'icon' => 'heroicon-o-pencil-square',
                    'url' => route('filament.admin.resources.grades.index'),
                    'color' => 'primary',
                ],
                [
                    'label' => 'Input Absensi',
                    'description' => 'Catat kehadiran warga belajar',
                    'icon' => 'heroicon-o-calendar-days',
                    'url' => route('filament.admin.resources.attendances.index'),
                    'color' => 'success',
                ],
                [
                    'label' => 'Cetak Rapor',
                    'description' => 'Generate laporan rapor',
                    'icon' => 'heroicon-o-printer',
                    'url' => route('filament.admin.pages.cetak-rapot'),
                    'color' => 'warning',
                ],
                [
                    'label' => 'Tambah Warga Belajar',
                    'description' => 'Registrasi peserta baru',
                    'icon' => 'heroicon-o-user-plus',
                    'url' => route('filament.admin.resources.learners.index'),
                    'color' => 'info',
                ],
                [
                    'label' => 'Kelola Tutor',
                    'description' => 'Atur data pengajar',
                    'icon' => 'heroicon-o-users',
                    'url' => route('filament.admin.resources.tutors.index'),
                    'color' => 'gray',
                ],
                [
                    'label' => 'Backup Sistem',
                    'description' => 'Cadangkan data sistem',
                    'icon' => 'heroicon-o-circle-stack',
                    'url' => route('filament.admin.resources.backup-histories.index'),
                    'color' => 'danger',
                ],
            ],
            'tutorActions' => [
                [
                    'label' => 'Input Nilai',
                    'description' => 'Input nilai warga belajar',
                    'icon' => 'heroicon-o-pencil-square',
                    'url' => route('filament.admin.resources.grades.index'),
                    'color' => 'primary',
                ],
                [
                    'label' => 'Input Absensi',
                    'description' => 'Catat kehadiran kelas',
                    'icon' => 'heroicon-o-calendar-days',
                    'url' => route('filament.admin.resources.attendances.manage'),
                    'color' => 'success',
                ],
                [
                    'label' => 'Catatan Wali Kelas',
                    'description' => 'Kelola catatan wali kelas',
                    'icon' => 'heroicon-o-document-text',
                    'url' => route('filament.admin.resources.homeroom-notes.index'),
                    'color' => 'info',
                ],
            ],
        ];
    }
}
