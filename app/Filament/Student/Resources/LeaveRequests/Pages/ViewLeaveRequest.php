<?php

namespace App\Filament\Student\Resources\LeaveRequests\Pages;

use App\Filament\Student\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
