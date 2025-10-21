<?php

namespace App\Filament\Admin\Resources\Grades\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GradeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->required(),
                Select::make('major_id')
                    ->label('Jurusan')
                    ->relationship('major', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }
}
