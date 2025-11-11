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
                        TextEntry::make('presence_date')
                            ->label('Tanggal')
                            ->date(),
                        TextEntry::make('grade.name')
                            ->label('Kelas'),
                        TextEntry::make('verifier.name')
                            ->label('Diverifikasi Oleh')
                            ->placeholder('-'),
                        TextEntry::make('verified_at')
                            ->label('Diverifikasi Pada')
                            ->placeholder('-')
                            ->dateTime(),
                        TextEntry::make('academicYear.name')
                            ->label('Tahun Ajaran'),
                        TextEntry::make('semester.name')
                            ->label('Semester'),
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
