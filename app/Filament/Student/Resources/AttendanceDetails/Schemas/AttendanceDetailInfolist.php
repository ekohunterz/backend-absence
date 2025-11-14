<?php

namespace App\Filament\Student\Resources\AttendanceDetails\Schemas;

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
                        TextEntry::make('attendance.presence_date')
                            ->date()
                            ->label('Tanggal'),
                        TextEntry::make('student.name')
                            ->label('Nama Siswa'),
                        TextEntry::make('status')
                            ->badge()
                            ->colors([
                                'success' => 'hadir',
                                'danger' => 'alpa',
                                'info' => 'izin',
                                'warning' => 'sakit',
                            ])->formatStateUsing(fn($state): string => ucwords((string) $state)),
                        TextEntry::make('attendance.verified_at')
                            ->label('Diverifikasi Pada')
                            ->dateTime()
                            ->placeholder('-')

                    ])->columns(2)->columnSpanFull(),
                Section::make('Detail Absensi')
                    ->collapsible()
                    ->visible(fn($record) => $record->attendance != null)
                    ->components([
                        TextEntry::make('check_in_time')
                            ->label('Masuk')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('check_out_time')
                            ->label('Keluar')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('location_in')
                            ->label('Lokasi Masuk')
                            ->placeholder('-'),
                        TextEntry::make('location_out')
                            ->label('Lokasi Keluar')
                            ->placeholder('-'),
                        ImageEntry::make('photo_in')
                            ->disk('public')
                            ->label('Foto Masuk')
                            ->placeholder('-'),
                        ImageEntry::make('photo_out')
                            ->label('Foto Keluar')
                            ->disk('public')
                            ->placeholder('-'),
                    ])->columns(2)->columnSpanFull(),
                Section::make('Detail Izin')
                    ->collapsible()
                    ->visible(fn($record) => $record->leaveRequest != null)
                    ->components([
                        TextEntry::make('leaveRequest.reason')
                            ->label('Alasan')
                            ->placeholder('-'),
                        TextEntry::make('leaveRequest.type')
                            ->label('Status')
                            ->badge()
                            ->colors([
                                'info' => 'izin',
                                'danger' => 'sakit',
                            ]),
                        PanZoomEntry::make('leaveRequest.proof_file')
                            ->imageUrl(fn($record) => asset('storage/' . $record->leaveRequest->proof_file))
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpanFull(),

                // Section::make('Timestamps')
                //     ->collapsible()
                //     ->components([
                //         TextEntry::make('created_at')
                //             ->label('Dibuat Pada')
                //             ->dateTime()
                //             ->placeholder('-'),
                //         TextEntry::make('updated_at')
                //             ->label('Diperbaharui Pada')
                //             ->dateTime()
                //             ->placeholder('-'),
                //     ])->columns(2),
            ]);
    }
}
