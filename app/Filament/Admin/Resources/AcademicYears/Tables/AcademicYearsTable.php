<?php

namespace App\Filament\Admin\Resources\AcademicYears\Tables;

use App\Models\AcademicYear;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AcademicYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('is_active', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),

                TextColumn::make('start_date')
                    ->label('Periode')
                    ->date('d M Y')
                    ->description(
                        fn(AcademicYear $record): string =>
                        's/d ' . $record->end_date->format('d M Y')
                    )
                    ->sortable(),

                TextColumn::make('semesters_count')
                    ->label('Semester')
                    ->counts('semesters')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => $state . ' Semester'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    // ViewAction::make(),
                    EditAction::make(),

                    Action::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Tahun Ajaran')
                        ->modalDescription('Tahun ajaran lain akan otomatis dinonaktifkan. Lanjutkan?')
                        ->action(function (AcademicYear $record) {
                            $record->activate();

                            Notification::make()
                                ->title('Tahun ajaran diaktifkan')
                                ->success()
                                ->send();
                        })
                        ->visible(fn(AcademicYear $record) => !$record->is_active),

                    Action::make('view_semesters')
                        ->label('Lihat Semester')
                        ->icon('heroicon-o-calendar-days')
                        ->color('info')
                        ->modalHeading(fn(AcademicYear $record) => 'Semester - ' . $record->name)
                        ->modalContent(fn(AcademicYear $record) => view('filament.admin.modals.semesters-view', [
                            'semesters' => $record->semesters
                        ]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),

                    DeleteAction::make()
                        ->before(function (AcademicYear $record) {
                            if ($record->is_active) {
                                Notification::make()
                                    ->title('Tidak dapat menghapus')
                                    ->body('Tahun ajaran aktif tidak dapat dihapus')
                                    ->danger()
                                    ->send();

                                return false;
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            if ($records->contains('is_active', true)) {
                                Notification::make()
                                    ->title('Tidak dapat menghapus')
                                    ->body('Tidak dapat menghapus tahun ajaran yang aktif')
                                    ->danger()
                                    ->send();

                                return false;
                            }
                        }),
                ]),
            ]);
    }
}
