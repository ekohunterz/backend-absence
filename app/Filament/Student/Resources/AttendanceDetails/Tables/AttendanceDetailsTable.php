<?php

namespace App\Filament\Student\Resources\AttendanceDetails\Tables;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('attendance.presence_date', 'desc')
            ->columns([
                TextColumn::make('attendance.presence_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('check_in_time')
                    ->label('Presensi Masuk')
                    ->placeholder('-')
                    ->time(),
                TextColumn::make('check_out_time')
                    ->label('Presensi Keluar')
                    ->placeholder('-')
                    ->time(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'hadir' => 'success',
                            'izin' => 'info',
                            'sakit' => 'warning',
                            'alpa' => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),
            ])
            ->filters([

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Hingga Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('attendances.presence_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('attendances.presence_date', '<=', $date),
                            );
                    }),
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                    ])
            ])
            ->recordActions([
                ViewAction::make(),

            ])
            ->toolbarActions([

            ]);
    }
}
