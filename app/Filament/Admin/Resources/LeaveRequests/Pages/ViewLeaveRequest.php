<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Pages;

use App\Filament\Admin\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
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
                ->modalHeading('Terima Permohonan Izin')
                ->modalButton('Terima')
                ->action(function ($record) {
                    DB::transaction(function () use ($record) {
                        $record->update(['status' => 'approved']);

                        // cari atau buat attendance di tanggal tersebut
                        $attendance = Attendance::firstOrCreate(
                            [
                                'grade_id' => $record->grade_id,
                                'date' => now()->toDateString(),
                            ],
                            [ // hanya akan dieksekusi jika belum ada
                                'start_time' => now()->toTimeString(),
                                'end_time' => now()->addHours(8)->toTimeString(),
                                'verified_by' => auth()->id(),
                                'academic_year_id' => $record->academic_year_id,
                            ]
                        );

                        // tambahkan atau update detail attendance
                        AttendanceDetail::updateOrCreate(
                            [
                                'attendance_id' => $attendance->id,
                                'student_id' => $record->student_id,
                            ],
                            [
                                'status' => $record->type,
                                'leave_request_id' => $record->id,
                                'verified_by' => auth()->id(),
                            ]
                        );
                    });
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
                ->action(function ($record) {
                    DB::transaction(function () use ($record) {
                        $record->update(['status' => 'rejected']);

                        // Jika ditolak, tandai sebagai alpa
                        $attendance = Attendance::firstOrCreate(
                            [
                                'grade_id' => $record->grade_id,
                                'date' => now()->toDateString(),
                            ],
                            [ // hanya akan dieksekusi jika belum ada
                                'start_time' => now()->toTimeString(),
                                'end_time' => now()->addHours(8)->toTimeString(),
                                'verified_by' => auth()->id(),
                                'academic_year_id' => $record->academic_year_id,
                            ]
                        );
                        AttendanceDetail::updateOrCreate(
                            [
                                'attendance_id' => $attendance->id,
                                'student_id' => $record->student_id,
                            ],
                            [
                                'status' => 'alpa',
                                'leave_request_id' => $record->id,
                            ]
                        );
                    });
                }),
            EditAction::make(),
        ];
    }
}
