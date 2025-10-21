<?php

namespace App\Filament\Admin\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('start_year')
                    ->label('Tahun Mulai')
                    ->required(),
                TextInput::make('end_year')
                    ->label('Tahun Selesai')
                    ->required(),
                Select::make('semester')
                    ->options(['Ganjil' => 'Ganjil', 'Genap' => 'Genap'])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(false)
                    ->inline(false)
                    ->required(),
            ]);
    }
}
