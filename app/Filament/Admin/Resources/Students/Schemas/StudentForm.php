<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use App\Models\Grade;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->label('Avatar')
                    ->image()
                    ->imageEditor()
                    ->imagePreviewHeight('250')
                    ->panelAspectRatio('6:5')
                    ->panelLayout('integrated')
                    ->disk('public')
                    ->directory('avatars')
                    ->columnSpan('2'),
                Grid::make(2)
                    ->schema([
                        TextInput::make('nis')
                            ->label('NIS')
                            ->unique()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->confirmed()
                            ->revealable()
                            ->prefixIcon(Phosphor::PasswordDuotone)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state): bool => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->columnSpan(1),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->required(fn(string $context): bool => $context === 'create')
                            ->password()
                            ->revealable()
                            ->prefixIcon(Phosphor::PasswordDuotone)
                            ->columnSpan(1),
                        DatePicker::make('birth_date')
                            ->required()
                            ->minDate('2000-01-01')
                            ->maxDate('today')
                            ->label('Tanggal Lahir'),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel(),
                        TextInput::make('parent_name')
                            ->label('Nama Orang Tua')
                            ->required(),
                        TextInput::make('parent_phone')
                            ->label('Nomor Telepon Orang Tua')
                            ->tel(),
                        Textarea::make('address')
                            ->label('Alamat'),
                        Select::make('grade_id')
                            ->label('Kelas')
                            ->relationship('grade', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),


                    ])->columnSpan('4'),
            ])->columns(6);
    }
}
