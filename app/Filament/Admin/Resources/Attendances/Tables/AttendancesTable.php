<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use App\Models\AcademicYear;
use App\Models\Attendance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('presence_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->timezone('Asia/Jakarta')
                    ->sortable(),
                TextColumn::make('grade.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label('Diverifikasi Pada')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('verifier.name')
                    ->label('Diverifikasi Oleh')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran & Semester')
                    ->description(fn(Attendance $record): string => $record->semester->getTypeLabel() ?? '-')
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
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('presence_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('presence_date', '<=', $date),
                            );
                    })

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
