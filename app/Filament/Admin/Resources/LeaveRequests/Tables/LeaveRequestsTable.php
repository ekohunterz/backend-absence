<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Tables;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('grade.name')
                    ->label('Kelas')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->description(fn($record): string => $record->semester?->name ?? '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('grade_id')
                    ->label('Kelas')
                    ->relationship('grade', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->native(false),
            ])
            ->recordActions([
                // Approve Action
                Action::make('approve')
                    ->label('')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Setujui')
                    ->requiresConfirmation()
                    ->visible(fn(Model $record): bool => $record->status === 'pending')
                    ->modalHeading('Setujui Pengajuan Izin')
                    ->modalDescription(
                        fn(Model $record): string =>
                        "Setujui pengajuan {$record->type} dari {$record->student->name} ({$record->student->nis}) untuk tanggal {$record->start_date->format('d M Y')} s/d {$record->end_date->format('d M Y')}?"
                    )
                    ->form([
                        Textarea::make('response_note')
                            ->label('Catatan (Opsional)')
                            ->placeholder('Contoh: Disetujui, segera sembuh')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function (Model $record, array $data) {
                        DB::beginTransaction();

                        try {
                            // Update leave request status
                            $record->update([
                                'status' => 'approved',
                                'response_note' => $data['response_note'] ?? null,
                                'responded_by' => auth()->id(),
                                'responded_at' => now(),
                            ]);

                            // Update attendance records
                            static::updateAttendanceRecords($record);

                            DB::commit();

                            Notification::make()
                                ->title('Pengajuan Disetujui')
                                ->body("Pengajuan {$record->type} dari {$record->student->name} telah disetujui")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            DB::rollBack();

                            Notification::make()
                                ->title('Gagal Menyetujui')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // Reject Action
                Action::make('reject')
                    ->label('')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Tolak')
                    ->requiresConfirmation()
                    ->visible(fn(Model $record): bool => $record->status === 'pending')
                    ->modalHeading('Tolak Pengajuan Izin')
                    ->modalDescription(
                        fn(Model $record): string =>
                        "Tolak pengajuan {$record->type} dari {$record->student->name}?"
                    )
                    ->form([
                        Textarea::make('response_note')
                            ->label('Alasan Penolakan (Wajib)')
                            ->placeholder('Masukkan alasan penolakan')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function (Model $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'response_note' => $data['response_note'],
                            'responded_by' => auth()->id(),
                            'responded_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan Ditolak')
                            ->body("Pengajuan {$record->type} dari {$record->student->name} telah ditolak")
                            ->warning()
                            ->send();
                    }),

                ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Detail'),

                EditAction::make()
                    ->label('')
                    ->tooltip('Ubah')
                    ->visible(fn(Model $record): bool => $record->status === 'pending'),

                DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Approve
                    BulkAction::make('bulk_approve')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Setujui Pengajuan Massal')
                        ->modalDescription(
                            fn(Collection $records): string =>
                            "Setujui {$records->count()} pengajuan izin yang dipilih?"
                        )
                        ->form([
                            Textarea::make('response_note')
                                ->label('Catatan (Opsional)')
                                ->placeholder('Catatan untuk semua pengajuan')
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $successCount = 0;
                            $failedCount = 0;

                            DB::beginTransaction();

                            try {
                                foreach ($records as $record) {
                                    if ($record->status !== 'pending') {
                                        $failedCount++;
                                        continue;
                                    }

                                    $record->update([
                                        'status' => 'approved',
                                        'response_note' => $data['response_note'] ?? null,
                                        'responded_by' => auth()->id(),
                                        'responded_at' => now(),
                                    ]);

                                    static::updateAttendanceRecords($record);
                                    $successCount++;
                                }

                                DB::commit();

                                $message = "{$successCount} pengajuan berhasil disetujui";
                                if ($failedCount > 0) {
                                    $message .= ", {$failedCount} gagal (bukan status pending)";
                                }

                                Notification::make()
                                    ->title('Proses Selesai')
                                    ->body($message)
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                DB::rollBack();

                                Notification::make()
                                    ->title('Gagal Memproses')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Bulk Reject
                    BulkAction::make('bulk_reject')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Tolak Pengajuan Massal')
                        ->modalDescription(
                            fn(Collection $records): string =>
                            "Tolak {$records->count()} pengajuan izin yang dipilih?"
                        )
                        ->form([
                            Textarea::make('response_note')
                                ->label('Alasan Penolakan (Wajib)')
                                ->placeholder('Masukkan alasan penolakan')
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $successCount = 0;
                            $failedCount = 0;

                            foreach ($records as $record) {
                                if ($record->status !== 'pending') {
                                    $failedCount++;
                                    continue;
                                }

                                $record->update([
                                    'status' => 'rejected',
                                    'response_note' => $data['response_note'],
                                    'responded_by' => auth()->id(),
                                    'responded_at' => now(),
                                ]);

                                $successCount++;
                            }

                            $message = "{$successCount} pengajuan berhasil ditolak";
                            if ($failedCount > 0) {
                                $message .= ", {$failedCount} gagal (bukan status pending)";
                            }

                            Notification::make()
                                ->title('Proses Selesai')
                                ->body($message)
                                ->warning()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Update attendance records based on approved leave request
     */
    protected static function updateAttendanceRecords(Model $leaveRequest): void
    {
        $startDate = $leaveRequest->start_date;
        $endDate = $leaveRequest->end_date;
        $student = $leaveRequest->student;

        // Loop through each date in the range
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Skip weekends
            if (!$currentDate->isWeekend()) {
                // Get or create attendance record for this date
                $attendance = Attendance::firstOrCreate(
                    [
                        'grade_id' => $leaveRequest->grade_id,
                        'presence_date' => $currentDate,
                    ],
                    [
                        'academic_year_id' => $leaveRequest->academic_year_id,
                        'semester_id' => $leaveRequest->semester_id,
                    ]
                );

                // Update or create attendance detail
                AttendanceDetail::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => $leaveRequest->type,
                        'notes' => $leaveRequest->reason,
                        'permission_proof' => $leaveRequest->proof_file,
                        'leave_request_id' => $leaveRequest->id,
                    ]
                );
            }

            $currentDate->addDay();
        }
    }
}
