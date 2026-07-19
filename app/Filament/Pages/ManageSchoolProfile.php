<?php

namespace App\Filament\Pages;

use App\Models\SchoolProfile;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSchoolProfile extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Profil Sekolah';

    protected ?string $heading = 'Profil Sekolah';

    protected string $view = 'filament.pages.manage-school-profile';

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.dashboard') => 'Beranda',
            'Sistem',
            'Profil Sekolah',
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('setting.view');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $profile = SchoolProfile::first();

        $this->form->fill(
            $profile?->toArray() ?? []
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Sekolah')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(20),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3),
                        TextInput::make('district')
                            ->label('Kecamatan')
                            ->maxLength(100),
                        TextInput::make('city')
                            ->label('Kota/Kabupaten')
                            ->maxLength(100),
                        TextInput::make('province')
                            ->label('Provinsi')
                            ->maxLength(100),
                        TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(10),
                    ])->columns(2),
                Section::make('Kontak')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),
                Section::make('Kepala Sekolah')
                    ->schema([
                        TextInput::make('headmaster_name')
                            ->label('Nama Kepala Sekolah')
                            ->maxLength(255),
                        TextInput::make('headmaster_nip')
                            ->label('NIP Kepala Sekolah')
                            ->maxLength(30),
                    ])->columns(2),
                Section::make('Dokumen & Logo')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo Sekolah')
                            ->image()
                            ->directory('school-profiles'),
                        FileUpload::make('headmaster_signature')
                            ->label('Tanda Tangan Kepala Sekolah')
                            ->image()
                            ->directory('school-profiles'),
                        FileUpload::make('school_stamp')
                            ->label('Stempel Sekolah')
                            ->image()
                            ->directory('school-profiles'),
                    ])->columns(3),
                Section::make('Informasi Tambahan')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $profile = SchoolProfile::first();

        if ($profile) {
            $profile->update($data);
        } else {
            SchoolProfile::create($data);
        }

        Notification::make()
            ->success()
            ->title('Profil sekolah berhasil disimpan.')
            ->send();
    }
}
