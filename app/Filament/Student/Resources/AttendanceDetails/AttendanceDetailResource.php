<?php

namespace App\Filament\Student\Resources\AttendanceDetails;

use App\Filament\Student\Resources\AttendanceDetails\Pages\CreateAttendanceDetail;
use App\Filament\Student\Resources\AttendanceDetails\Pages\EditAttendanceDetail;
use App\Filament\Student\Resources\AttendanceDetails\Pages\ListAttendanceDetails;
use App\Filament\Student\Resources\AttendanceDetails\Pages\ViewAttendanceDetail;
use App\Filament\Student\Resources\AttendanceDetails\Schemas\AttendanceDetailForm;
use App\Filament\Student\Resources\AttendanceDetails\Schemas\AttendanceDetailInfolist;
use App\Filament\Student\Resources\AttendanceDetails\Tables\AttendanceDetailsTable;
use App\Models\AttendanceDetail;
use BackedEnum;
use UnitEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceDetailResource extends Resource
{
    protected static ?string $model = AttendanceDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Phosphor::Clock;

    protected static ?string $pluralLabel = 'Riwayat Presensi';

    protected static ?string $singularLabel = 'Riwayat Presensi';

    protected static ?string $modelLabel = 'Riwayat Presensi';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('student_id', auth('student')->user()->id);
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
            'view' => ViewAttendanceDetail::route('/{record}'),
        ];
    }
}
