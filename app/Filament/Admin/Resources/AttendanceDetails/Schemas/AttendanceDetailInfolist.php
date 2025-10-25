<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use SolutionForest\FilamentPanzoom\Infolists\Components\PanZoomEntry;

class AttendanceDetailInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextEntry::make('attendance.date')
                            ->date()
                            ->label('Tanggal'),
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
                            ->label('Foto Masuk')
                            ->placeholder('-'),
                        ImageEntry::make('photo_out')
                            ->label('Foto Keluar')
                            ->placeholder('-'),
                    ])->columns(2)->columnSpanFull(),
                Section::make('Detail Izin')
                    ->collapsible()
                    ->components([
                        TextEntry::make('leaveRequest.reason')
                            ->label('Alasan')
                            ->placeholder('-'),
                        TextEntry::make('leaveRequest.type')
                            ->label('Status')
                            ->badge(),
                        PanZoomEntry::make('leaveRequest.proof_file')
                            ->imageUrl(fn($record) => asset('storage/' . $record->leaveRequest->proof_file))
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Timestamps')
                    ->collapsible()
                    ->components([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Diperbaharui Pada')
                            ->dateTime()
                            ->placeholder('-'),
                    ])->columns(2),
            ]);
    }
}
