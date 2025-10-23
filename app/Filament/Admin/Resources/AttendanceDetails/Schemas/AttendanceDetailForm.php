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
        return $schema
            ->components([
                TextInput::make('attendance_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'])
                    ->default('hadir')
                    ->required(),
                DateTimePicker::make('check_in_time'),
                DateTimePicker::make('check_out_time'),
                TextInput::make('location'),
                TextInput::make('photo_in'),
                TextInput::make('photo_out'),
                TextInput::make('leave_request_id')
                    ->numeric(),
            ]);
    }
}
