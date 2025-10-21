<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Models\Grade;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->defaultImageUrl(url('https://www.gravatar.com/avatar/64e1b8d34f425d19e1ee2ea7236d3028?d=mp&r=g&s=250'))
                    ->label('')
                    ->alignEnd()
                    ->square()
                    ->grow(false),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('JK')
                    ->badge(),
                TextColumn::make('grade.name')
                    ->label('Kelas Aktif')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Nomor Telepon')
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
                SelectFilter::make('grade_id')
                    ->label('Kelas')
                    ->multiple()
                    ->relationship('grade', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('promote')
                    ->label('')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->tooltip('Naik Kelas')
                    ->accessSelectedRecords()
                    ->form([
                        Select::make('grade_id')
                            ->label('Pilih Kelas Tujuan')
                            ->options(Grade::query()->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, Model $record, Collection $selectedRecords) {
                        if ($selectedRecords->isEmpty()) {
                            $record->update([
                                'grade_id' => $data['grade_id'],
                            ]);
                        } else {
                            $selectedRecords->each(function (Model $selectedRecord) use ($data) {
                                $selectedRecord->update([
                                    'grade_id' => $data['grade_id'],
                                ]);
                            });
                        }
                        Notification::make()
                            ->title('Berhasil Naik Kelas')
                            ->success()
                            ->send();
                    }),
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
                    BulkAction::make('promote')
                        ->label('Naik Kelas')
                        ->icon('heroicon-o-arrow-up')
                        ->color('success')
                        ->requiresConfirmation()
                        ->accessSelectedRecords()
                        ->form([
                            Select::make('grade_id')
                                ->label('Pilih Kelas Tujuan')
                                ->options(Grade::pluck('name', 'id')) // tidak perlu parameter $record
                                ->preload()
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records): void {
                            $records->each(
                                fn(Model $record) =>
                                $record->update(['grade_id' => $data['grade_id']])
                            );

                            Notification::make()
                                ->title('Berhasil Naik Kelas')
                                ->body("Sebanyak {$records->count()} siswa berhasil naik kelas.")
                                ->success()
                                ->send();
                        }),

                ]),
            ]);
    }
}
