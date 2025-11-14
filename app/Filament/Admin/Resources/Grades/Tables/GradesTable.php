<?php

namespace App\Filament\Admin\Resources\Grades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('grades.created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->sortable()
                    ->weight('semibold')
                    ->searchable(),
                TextColumn::make('major.name')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('students_count')
                    ->counts('students')
                    ->color('warning')
                    ->suffix(' Siswa')
                    ->label('Jumlah Siswa'),
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
                SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->searchable()
                    ->preload()
                    ->relationship('major', 'name'),

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
