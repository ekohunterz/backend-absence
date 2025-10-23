<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextEntry::make('date')
                            ->label('Tanggal')
                            ->date(),
                        TextEntry::make('start_time')
                            ->label('Mulai')
                            ->time(),
                        TextEntry::make('end_time')
                            ->label('Selesai')
                            ->time(),
                        TextEntry::make('grade.name')
                            ->label('Kelas'),
                        TextEntry::make('verifier.name')
                            ->label('Verifikasi Oleh')
                            ->placeholder('-'),
                        TextEntry::make('academicYear.start_year')
                            ->label('Tahun Ajaran & Semester')
                            ->formatStateUsing(fn($state, $record): string => $state . '/' . $state + 1 . ' (' . $record->academicYear->semester . ')'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Rekap Absensi')
                    ->components([
                        TextEntry::make('present_count')
                            ->label('Hadir'),
                        TextEntry::make('absent_count')
                            ->label('Tanpa Keterangan'),
                        TextEntry::make('sick_count')
                            ->label('Sakit'),
                        TextEntry::make('leave_count')
                            ->label('Izin'),

                    ])
                    ->columns(4)
                    ->columnSpanFull(),

            ]);
    }
}
