<?php

namespace App\Filament\Pages\Auth;

use App\Models\Tutor;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class EditProfile extends BaseEditProfile
{
    // Memaksa lebar halaman menjadi penuh (Full)
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::FourExtraLarge;
    }

    public function form(Schema $schema): Schema
    {
        $tutor = Tutor::where('user_id', Auth::id())->first();

        $components = [
            Section::make('Informasi Akun')
                ->columns(2)
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]),
        ];

        if ($tutor) {
            $components[] = Section::make('Data Tutor')
                ->columns(2)
                ->schema([
                    TextInput::make('tutor_nip')
                        ->label('NIP')
                        ->default($tutor->nip)
                        ->maxLength(255),
                    TextInput::make('tutor_name')
                        ->label('Nama Lengkap')
                        ->default($tutor->name)
                        ->maxLength(255),
                    Select::make('tutor_gender')
                        ->label('Jenis Kelamin')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->default($tutor->gender),
                    TextInput::make('tutor_birth_place')
                        ->label('Tempat Lahir')
                        ->default($tutor->birth_place)
                        ->maxLength(255),
                    DatePicker::make('tutor_birth_date')
                        ->label('Tanggal Lahir')
                        ->default($tutor->birth_date),
                    Textarea::make('tutor_address')
                        ->label('Alamat')
                        ->rows(3)
                        ->default($tutor->address),
                    TextInput::make('tutor_phone')
                        ->label('Telepon')
                        ->tel()
                        ->default($tutor->phone)
                        ->maxLength(20),
                    TextInput::make('tutor_email')
                        ->label('Email')
                        ->email()
                        ->default($tutor->email)
                        ->maxLength(255),
                ]);
        }

        return $schema->components($components);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $tutorData = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'tutor_')) {
                $tutorData[substr($key, 6)] = $value;
                unset($data[$key]);
            }
        }

        if (! empty($tutorData)) {
            $tutor = Tutor::where('user_id', Auth::id())->first();
            if ($tutor) {
                $tutor->update($tutorData);
            }
        }

        return $data;
    }
}
