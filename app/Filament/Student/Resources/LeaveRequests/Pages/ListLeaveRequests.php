<?php

namespace App\Filament\Student\Resources\LeaveRequests\Pages;

use App\Filament\Student\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Izin')
                ->icon('heroicon-s-plus'),
        ];
    }
}
