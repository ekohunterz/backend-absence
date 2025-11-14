<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Pages;

use App\Filament\Admin\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\Student;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['grade_id'] = Student::find($data['student_id'])->grade_id;

        return $data;
    }


}
