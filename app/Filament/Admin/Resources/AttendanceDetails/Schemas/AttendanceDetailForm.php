<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttendanceDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('status')
                ->label('Status Kehadiran')
                ->options([
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpa' => 'Alpa',
                ])
                ->required()
                ->native(false),
        ]);
    }
}
