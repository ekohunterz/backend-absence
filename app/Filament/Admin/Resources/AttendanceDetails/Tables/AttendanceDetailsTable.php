<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class AttendanceDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('student.nis')
                    ->label('NIS')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'izin',
                        'info' => 'sakit',
                        'danger' => 'alpa',
                    ]),
                TextColumn::make('check_in_time')
                    ->label('Masuk')
                    ->time()
                    ->sortable(),
                TextColumn::make('check_out_time')
                    ->label('Keluar')
                    ->time()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                ImageColumn::make('photo_in')
                    ->label('Foto Masuk')
                    ->searchable(),
                ImageColumn::make('photo_out')
                    ->label('Foto Keluar')
                    ->searchable(),
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
