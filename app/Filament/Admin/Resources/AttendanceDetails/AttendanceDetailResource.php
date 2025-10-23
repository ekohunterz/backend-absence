<?php

namespace App\Filament\Admin\Resources\AttendanceDetails;

use App\Filament\Admin\Resources\AttendanceDetails\Pages\CreateAttendanceDetail;
use App\Filament\Admin\Resources\AttendanceDetails\Pages\EditAttendanceDetail;
use App\Filament\Admin\Resources\AttendanceDetails\Pages\ListAttendanceDetails;
use App\Filament\Admin\Resources\AttendanceDetails\Pages\ViewAttendanceDetail;
use App\Filament\Admin\Resources\AttendanceDetails\Schemas\AttendanceDetailForm;
use App\Filament\Admin\Resources\AttendanceDetails\Schemas\AttendanceDetailInfolist;
use App\Filament\Admin\Resources\AttendanceDetails\Tables\AttendanceDetailsTable;
use App\Models\AttendanceDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AttendanceDetailResource extends Resource
{
    protected static ?string $model = AttendanceDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return AttendanceDetailForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceDetailInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceDetailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceDetails::route('/'),
            'create' => CreateAttendanceDetail::route('/create'),
            'view' => ViewAttendanceDetail::route('/{record}'),
            'edit' => EditAttendanceDetail::route('/{record}/edit'),
        ];
    }
}
