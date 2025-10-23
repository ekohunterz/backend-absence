<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('attendance_id')
                    ->numeric(),
                TextEntry::make('student.name')
                    ->label('Nama Siswa'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('check_in_time')
                    ->label('Masuk')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('check_out_time')
                    ->label('Keluar')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('location')
                    ->label('Lokasi')
                    ->placeholder('-'),
                ImageEntry::make('photo_in')
                    ->placeholder('-'),
                ImageEntry::make('photo_out')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
