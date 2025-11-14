<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Pages;

use App\Filament\Admin\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Approve')
                ->color('success')
                ->visible(fn($record) => $record->status === 'pending')
                ->tooltip('Terima izin')
                ->icon(Phosphor::Check)
                ->requiresConfirmation()
                ->form([
                    Textarea::make('response_note')
                        ->label('Catatan (Opsional)')
                        ->placeholder('Contoh: Disetujui, segera sembuh')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->modalHeading('Terima Permohonan Izin')
                ->modalButton('Terima')
                ->action(function ($record, array $data) {
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

            // ❌ Action Reject
            Action::make('Reject')
                ->color('danger')
                ->visible(fn($record) => $record->status === 'pending')
                ->tooltip('Tolak izin')
                ->icon(Phosphor::X)
                ->requiresConfirmation()
                ->modalHeading('Tolak Permohonan Izin')
                ->modalButton('Tolak')
                ->form([
                    Textarea::make('response_note')
                        ->label('Alasan Penolakan (Wajib)')
                        ->placeholder('Masukkan alasan penolakan')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function ($record, array $data) {
                    DB::transaction(function () use ($record, $data) {
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
                    });
                }),
            EditAction::make(),
        ];
    }

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
