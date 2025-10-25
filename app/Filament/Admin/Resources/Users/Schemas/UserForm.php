<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use Date;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

final class UserForm
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
                    ->columnSpan('2'),
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255),
                        TextInput::make('nip')
                            ->required()
                            ->minLength(16)
                            ->maxLength(16)
                            ->unique('users', 'nip', ignoreRecord: true),
                        TextInput::make('email')
                            ->required()
                            ->prefixIcon(Phosphor::EnvelopeDuotone)
                            ->email(),
                        TextInput::make('phone')
                            ->required()
                            ->prefixIcon(Phosphor::PhoneDuotone)
                            ->tel(),
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
                            ->required(fn(string $context): bool => $context === 'create')
                            ->password()
                            ->revealable()
                            ->prefixIcon(Phosphor::PasswordDuotone)
                            ->columnSpan(1),
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required(),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->prefixIcon(Phosphor::ShieldCheckDuotone)
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ])->columnSpan('4'),
            ])
            ->columns(6);
    }
}
