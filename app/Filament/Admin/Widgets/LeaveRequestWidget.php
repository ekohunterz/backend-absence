<?php

namespace App\Filament\Admin\Widgets;

use App\Models\LeaveRequest;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestWidget extends TableWidget
{
    protected static ?int $sort = 5;


    protected static ?string $title = 'Permohonan Izin';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Permohonan Izin Hari Ini')
            ->query(fn(): Builder => LeaveRequest::query()->orderBy('created_at', 'desc')->whereDate('start_date', now()))
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->sortable(),
                TextColumn::make('grade.name')
                    ->label('Kelas')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal')
                    ->date()
                    ->wrap()
                    ->formatStateUsing(fn($record): string => date('d M Y', strtotime($record->start_date)) . ' - ' . date('d M Y', strtotime($record->end_date)))
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->colors([
                        'info' => 'izin',
                        'warning' => 'sakit',
                        'danger' => 'alpa',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('')
                    ->tooltip('Lihat Detail')
                    ->url(fn(LeaveRequest $record): string => route('filament.admin.resources.leave-requests.view', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
