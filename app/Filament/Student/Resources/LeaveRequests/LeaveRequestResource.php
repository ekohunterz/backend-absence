<?php

namespace App\Filament\Student\Resources\LeaveRequests;

use App\Filament\Student\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Student\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Student\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Student\Resources\LeaveRequests\Pages\ViewLeaveRequest;
use App\Filament\Student\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Student\Resources\LeaveRequests\Schemas\LeaveRequestInfolist;
use App\Filament\Student\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use UnitEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::ArchiveDuotone;

    protected static ?string $pluralLabel = 'Izin';

    protected static ?string $singularLabel = 'Izin';

    protected static ?string $modelLabel = 'Izin';

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('student_id', auth('student')->user()->id);
    }

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
