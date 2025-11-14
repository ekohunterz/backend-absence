<?php

namespace App\Filament\Admin\Resources\Majors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MajorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique()
                    ->label('Nama Jurusan')
                    ->reactive()
                    ->debounce(500)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Ambil huruf pertama dari setiap kata dan ubah ke uppercase
                        $abbreviation = collect(explode(' ', $state))
                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                            ->join('');

                        $set('code', $abbreviation);
                    })
                    ->required(),
                TextInput::make('code')
                    ->unique()
                    ->label('Kode Jurusan')
                    ->required(),
            ])->columns(1);
    }
}
