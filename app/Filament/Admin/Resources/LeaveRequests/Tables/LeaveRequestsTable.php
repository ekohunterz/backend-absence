<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academic_year.start_year')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->date()
                    ->formatStateUsing(fn($record): string => date('d M Y', strtotime($record->start_date)) . ' - ' . date('d M Y', strtotime($record->end_date)))
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->colors([
                        'info' => 'izin',
                        'warning' => 'sakit',
                        'danger' => 'alpa',
                    ]),
                ImageColumn::make('proof_file')
                    ->label('Bukti')
                    ->disk('public')
                    ->visibility('public'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'approved',
                        'warning' => 'pending',
                        'danger' => 'rejected',
                    ]),
                TextColumn::make('verifier.name')
                    ->label('Verifikasi Oleh')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('')
                    ->tooltip('Lihat Detail'),
                EditAction::make()->label('')
                    ->tooltip('Ubah'),
                DeleteAction::make()->label('')
                    ->tooltip('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
