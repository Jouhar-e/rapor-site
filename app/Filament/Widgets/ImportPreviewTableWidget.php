<?php

namespace App\Filament\Widgets;

use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ImportPreviewTableWidget extends TableWidget
{
    public array $data = [];

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function table(Table $table): Table
    {
        $columns = [];

        $firstRow = ! empty($this->data) ? $this->data[0] : [];

        foreach (array_keys($firstRow) as $key) {
            $label = match ($key) {
                'nis' => 'NIS',
                'nisn' => 'NISN',
                'nip' => 'NIP',
                'name' => 'Nama',
                'gender' => 'Jenis Kelamin',
                'birth_place' => 'Tempat Lahir',
                'birth_date' => 'Tgl Lahir',
                'address' => 'Alamat',
                'email' => 'Email',
                'phone' => 'Telepon',
                'is_active' => 'Status',
                'password' => 'Kata Sandi',
                'class_name' => 'Kelas',
                'task_score' => 'Tugas',
                'pts_score' => 'PTS',
                'pas_score' => 'PAS',
                'practice_score' => 'Praktik',
                default => ucfirst(str_replace('_', ' ', $key)),
            };

            $columns[] = TextColumn::make($key)->label($label);
        }

        return $table
            ->records(fn (): array => $this->data)
            ->columns($columns)
            ->paginated(false);
    }
}
