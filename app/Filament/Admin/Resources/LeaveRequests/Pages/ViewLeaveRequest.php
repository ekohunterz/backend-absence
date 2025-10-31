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

                        // Ubah status leave request jadi approved
                        $record->update(['status' => 'approved', 'verified_by' => auth()->user()->id]);

                        // Rentang tanggal izin (misal 2025-10-21 s.d 2025-10-23)
                        $period = \Carbon\CarbonPeriod::create($record->start_date, $record->end_date ?? $record->start_date);

                        foreach ($period as $date) {
                            // Buat atau ambil attendance untuk tanggal tersebut
                            $attendance = Attendance::firstOrCreate(
                                [
                                    'grade_id' => $record->grade_id,
                                    'presence_date' => $date->format('Y-m-d'),
                                ],
                                [
                                    'start_time' => now()->toTimeString(),
                                    'end_time' => now()->addHours(8)->toTimeString(),
                                    'academic_year_id' => $record->academic_year_id,
                                ]
                            );

                            // Tambahkan atau update detail attendance untuk siswa yang izin
                            AttendanceDetail::updateOrCreate(
                                [
                                    'attendance_id' => $attendance->id,
                                    'student_id' => $record->student_id,
                                ],
                                [
                                    'status' => $record->type, // izin / sakit
                                    'leave_request_id' => $record->id,
                                ]
                            );
                        }
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
                        $record->update(['status' => 'rejected', 'verified_by' => auth()->user()->id]);

                        // Jika ditolak, tandai sebagai alpa
                        $attendance = Attendance::firstOrCreate(
                            [
                                'grade_id' => $record->grade_id,
                                'presence_date' => now()->toDateString(),
                            ],
                            [ // hanya akan dieksekusi jika belum ada
                                'start_time' => now()->toTimeString(),
                                'end_time' => now()->addHours(8)->toTimeString(),
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

                            ]
                        );
                    });
                }),
            EditAction::make(),
        ];
    }
}
