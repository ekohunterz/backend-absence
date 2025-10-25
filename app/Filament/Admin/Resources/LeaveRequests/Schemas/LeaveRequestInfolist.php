<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Schemas;

use Filament\Infolists\Components\ImageEntry;
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
                TextEntry::make('date')
                    ->label('Tanggal')
                    ->date(),
                TextEntry::make('type')
                    ->label('Jenis')
                    ->badge(),
                TextEntry::make('reason')
                    ->label('Alasan')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('verifier.name')
                    ->label('Verifikasi Oleh')
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
