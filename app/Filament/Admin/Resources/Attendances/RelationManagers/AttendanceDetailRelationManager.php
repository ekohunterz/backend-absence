<?php

namespace App\Filament\Admin\Resources\Attendances\RelationManagers;

use App\Filament\Admin\Resources\AttendanceDetails\AttendanceDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class AttendanceDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $relatedResource = AttendanceDetailResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([

            ]);
    }
}
