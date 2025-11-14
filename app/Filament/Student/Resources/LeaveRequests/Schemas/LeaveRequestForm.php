<?php

namespace App\Filament\Student\Resources\LeaveRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->minDate(now()->format('Y-m-d'))
                    ->reactive()
                    ->default(now()->format('Y-m-d'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->minDate(fn($get) => $get('start_date'))
                    ->disabled(fn($get) => $get('start_date') == null),
                Select::make('type')
                    ->options(['sakit' => 'Sakit', 'izin' => 'Izin'])
                    ->placeholder('Pilih Tipe')
                    ->required(),
                FileUpload::make('proof_file')
                    ->label('Bukti')
                    ->required()
                    ->visibility('public')
                    ->disk('public')
                    ->directory('leave_requests')
                    ->image(),
                Textarea::make('reason')
                    ->label('Keterangan')
                    ->required()
                    ->columnSpanFull(),

            ]);
    }
}
