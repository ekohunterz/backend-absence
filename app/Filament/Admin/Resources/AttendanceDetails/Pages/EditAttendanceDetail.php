<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Pages;

use App\Filament\Admin\Resources\AttendanceDetails\AttendanceDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceDetail extends EditRecord
{
    protected static string $resource = AttendanceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

}
