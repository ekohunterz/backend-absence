<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use App\Models\AcademicYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time()
                    ->sortable(),
                TextColumn::make('grade.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('verifier.name')
                    ->label('Verifikasi Oleh')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('academicYear.start_year')
                    ->label('Tahun Ajaran & Semester')
                    ->formatStateUsing(fn($state): string => $state . '/' . $state + 1)
                    ->description(fn($record): string => $record->academicYear->semester)
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
                SelectFilter::make('grade_id')
                    ->label('Kelas')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->relationship('grade', 'name'),
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
