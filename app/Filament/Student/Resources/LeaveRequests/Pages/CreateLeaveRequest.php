<?php

namespace App\Filament\Student\Resources\LeaveRequests\Pages;

use App\Filament\Student\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\AcademicYear;
use App\Models\AttendanceDetail;
use App\Models\Semester;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function canCreateAnother(): bool
    {
        return false;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge($data, [
            'student_id' => auth('student')->id(),
            'grade_id' => auth('student')->user()->grade_id,
            'end_date' => $data['end_date'] ?? $data['start_date'],
            'academic_year_id' => AcademicYear::where('is_active', true)->first()->id,
            'semester_id' => Semester::where('is_active', true)->first()->id,
            'status' => 'pending',
        ]);
    }

    public function beforeValidate(): void
    {
        //check if student already present
        $attendance = AttendanceDetail::where('student_id', auth('student')->id())
            ->where('status', 'hadir')
            ->whereDate('created_at', $this->data['start_date'])
            ->first();

        if ($attendance) {
            Notification::make()
                ->title('Error')
                ->body('Anda sudah tercatat hadir hari ini')
                ->danger()
                ->send();

            $this->halt();
        }

    }
}
