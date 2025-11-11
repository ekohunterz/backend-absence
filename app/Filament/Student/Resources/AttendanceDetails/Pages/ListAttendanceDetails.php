<?php

namespace App\Filament\Student\Resources\AttendanceDetails\Pages;

use App\Filament\Student\Resources\AttendanceDetails\AttendanceDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceDetails extends ListRecords
{
    protected static string $resource = AttendanceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
