<?php

namespace App\Filament\Resources\Tutors;

use App\Filament\Resources\Tutors\Pages\ManageTutors;
use App\Models\HomeroomTeacher;
use App\Models\Tutor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class TutorResource extends Resource
{
    protected static ?string $model = Tutor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Tutor';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tutor';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Masuk')
                    ->columns(2)
                    ->schema([
                        TextInput::make('user_email')
                            ->label('Email Masuk')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignorable: fn (?Tutor $record) => $record?->user,
                            ),
                        TextInput::make('user_password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ]),
                Section::make('Data Tutor')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nip')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(255),
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email Masuk')
                    ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('L/P')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        if ($record->user) {
                            $data['user_email'] = $record->user->email;
                        }

                        return $data;
                    })
                    ->mutateDataUsing(function (array $data, Model $record): array {
                        if (isset($data['user_email'])) {
                            $record->user?->update(['email' => $data['user_email']]);
                            unset($data['user_email']);
                        }
                        if (isset($data['user_password'])) {
                            $record->user?->update(['password' => Hash::make($data['user_password'])]);
                            unset($data['user_password']);
                        }

                        return $data;
                    }),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn (Tutor $record) => "Hapus {$record->name}?")
                    ->modalDescription(function (Tutor $record): string {
                        $classes = HomeroomTeacher::where('user_id', $record->user_id)
                            ->with('classes')
                            ->get()
                            ->pluck('classes.name')
                            ->filter();

                        $msg = '';

                        if ($classes->isNotEmpty()) {
                            $msg .= 'Tutor ini adalah wali kelas untuk: '.$classes->implode(', ').".\n\n";
                        }

                        if ($record->user) {
                            $msg .= 'Akun masuk pengguna terkait juga akan ikut terhapus.';
                        }

                        if (blank($msg)) {
                            $msg = 'Apakah Anda yakin ingin menghapus tutor ini?';
                        }

                        $msg .= "\n\nTindakan ini tidak dapat dibatalkan.";

                        return $msg;
                    })
                    ->modalSubmitActionLabel('Ya, hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus tutor terpilih?')
                        ->modalDescription('Beberapa tutor mungkin adalah wali kelas dan memiliki akun masuk yang akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTutors::route('/'),
        ];
    }
}
