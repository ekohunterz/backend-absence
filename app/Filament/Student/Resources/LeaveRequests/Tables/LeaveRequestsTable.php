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
use Illuminate\Database\Eloquent\Model;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Tidak ada permohonan izin')
            ->emptyStateIcon('heroicon-o-document-text')
            ->columns([
                TextColumn::make('start_date')
                    ->label('Periode')
                    ->date('d M Y')
                    ->description(
                        fn($record): string =>
                        $record->start_date !== $record->end_date
                        ? 's/d ' . $record->end_date->format('d M Y') . ' (' . $record->days_count . ' hari)'
                        : '1 hari'
                    )
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'izin' => 'warning',
                        'sakit' => 'info',
                        'alpa' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50)
                    ->tooltip(fn(Model $record): string => $record->reason)
                    ->wrap()
                    ->toggleable(),
                ImageColumn::make('proof_file')
                    ->label('Bukti')
                    ->disk('public')
                    ->visibility('public')
                    ->size(60)
                    ->defaultImageUrl(url('/images/no-file.png')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),
                TextColumn::make('respondedBy.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('-')
                    ->description(
                        fn($record): ?string =>
                        $record->responded_at
                        ? $record->responded_at->diffForHumans()
                        : null
                    )
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('response_note')
                    ->label('Catatan')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->native(false),
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ])
                    ->native(false),
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

            ])
        ;
    }
}
