<?php

namespace App\Filament\Admin\Resources\Grades\Pages;

use App\Filament\Admin\Resources\Grades\GradeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGrade extends ViewRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
