<?php

namespace App\Filament\Admin\Resources\Majors\Pages;

use App\Filament\Admin\Resources\Majors\MajorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMajor extends CreateRecord
{
    protected static string $resource = MajorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function canCreateAnother(): bool
    {
        return false;
    }
}
