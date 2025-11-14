<?php

namespace App\Filament\Student\Resources\LeaveRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use SolutionForest\FilamentPanzoom\Infolists\Components\PanZoomEntry;

class LeaveRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.name')
                    ->label('Nama Siswa'),
                TextEntry::make('grade.name')
                    ->label('Kelas'),
                TextEntry::make('academic_year.name')
                    ->label('Tahun Ajaran'),
                TextEntry::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date(),
                TextEntry::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date(),
                TextEntry::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->colors([
                        'info' => 'izin',
                        'danger' => 'sakit',
                    ]),
                TextEntry::make('reason')
                    ->label('Alasan/Keterangan lengkap')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'approved',
                        'warning' => 'pending',
                    ]),
                TextEntry::make('respondedBy.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('-'),
                TextEntry::make('responded_at')
                    ->label('Diverifikasi Pada')
                    ->dateTime()
                    ->placeholder('-'),
                PanZoomEntry::make('proof_file')
                    ->imageUrl(fn($record) => asset('storage/' . $record->proof_file))
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
