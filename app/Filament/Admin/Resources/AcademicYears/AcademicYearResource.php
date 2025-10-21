<?php

namespace App\Filament\Admin\Resources\AcademicYears;

use App\Filament\Admin\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Admin\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Admin\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Admin\Resources\AcademicYears\Schemas\AcademicYearForm;
use App\Filament\Admin\Resources\AcademicYears\Tables\AcademicYearsTable;
use App\Models\AcademicYear;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'start_year';

    protected static ?string $pluralLabel = 'Tahun Akademik';

    protected static ?string $singularLabel = 'Tahun Akademik';

    protected static ?string $modelLabel = 'tahun akademik';

    public static function form(Schema $schema): Schema
    {
        return AcademicYearForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicYearsTable::configure($table);
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
            'index' => ListAcademicYears::route('/'),
            // 'create' => CreateAcademicYear::route('/create'),
            // 'edit' => EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
