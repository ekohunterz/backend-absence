<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use UnitEnum;
use BackedEnum;

class Presence extends Page
{
    protected string $view = 'filament.admin.pages.presence';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::MapPinArea;
}
