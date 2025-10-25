<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Pages;

use App\Filament\Admin\Resources\AttendanceDetails\AttendanceDetailResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttendanceDetail extends ViewRecord
{
    protected static string $resource = AttendanceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

}
