<?php

namespace App\Filament\Student\Resources\AttendanceDetails\Pages;

use App\Filament\Student\Resources\AttendanceDetails\AttendanceDetailResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceDetail extends ViewRecord
{
    protected static string $resource = AttendanceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
