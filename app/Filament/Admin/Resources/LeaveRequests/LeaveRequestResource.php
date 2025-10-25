<?php

namespace App\Filament\Admin\Resources\LeaveRequests;

use App\Filament\Admin\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Admin\Resources\LeaveRequests\Pages\ViewLeaveRequest;
use App\Filament\Admin\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Admin\Resources\LeaveRequests\Schemas\LeaveRequestInfolist;
use App\Filament\Admin\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::ArchiveDuotone;

    protected static ?string $recordTitleAttribute = 'date';

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    protected static ?int $navigationSort = 3;

    protected static ?string $pluralLabel = 'Permintaan Izin';

    protected static ?string $singularLabel = 'Permintaan Izin';

    protected static ?string $modelLabel = 'Permintaan Izin';

    public static function form(Schema $schema): Schema
    {
        return LeaveRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeaveRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
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
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'view' => ViewLeaveRequest::route('/{record}'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
