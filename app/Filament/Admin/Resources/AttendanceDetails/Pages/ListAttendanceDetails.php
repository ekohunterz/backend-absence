<?php

namespace App\Filament\Admin\Resources\AttendanceDetails\Pages;

use App\Filament\Admin\Resources\AttendanceDetails\AttendanceDetailResource;
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
    public static function canCreate(): bool
    {
        return false;
    }


}
