<?php

namespace App\Filament\Admin\Resources\Majors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MajorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->label('Nama Jurusan')
                    ->weight(FontWeight::SemiBold)
                    ->searchable(),
                TextColumn::make('code')
                    ->sortable()
                    ->label('Kode Jurusan')
                    ->searchable(),
                TextColumn::make('grades_count')
                    ->sortable()
                    ->counts('grades')
                    ->color('warning')

                    ->weight(FontWeight::Black)
                    ->label('Jumlah Kelas'),
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

                EditAction::make()->label('')
                    ->tooltip('Ubah')
                    ->modalWidth('md'),
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
