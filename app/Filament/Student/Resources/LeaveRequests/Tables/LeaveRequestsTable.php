<?php

namespace App\Filament\Student\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->date()
                    ->formatStateUsing(fn($record): string => date('d M Y', strtotime($record->start_date)) . ' - ' . date('d M Y', strtotime($record->end_date)))
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->description(fn($record): string => $record->semester->name)
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->colors([
                        'info' => 'izin',
                        'danger' => 'sakit',
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
                    ->label('Diverifikasi Oleh')
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
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Hingga Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date)->orWhereDate('end_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date)->orWhereDate('end_date', '>=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label('')
                    ->tooltip('Lihat Detail'),
                EditAction::make()->label('')
                    ->tooltip('Ubah')
                    ->disabled(fn($record): bool => $record->status !== 'pending')
                    ->visible(fn($record): bool => $record->status === 'pending'),
                DeleteAction::make()->label('')
                    ->tooltip('Hapus')
                    ->disabled(fn($record): bool => $record->status !== 'pending')
                    ->visible(fn($record): bool => $record->status === 'pending'),
            ])
            ->toolbarActions([

            ]);
    }
}
