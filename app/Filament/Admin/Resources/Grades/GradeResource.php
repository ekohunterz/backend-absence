<?php

namespace App\Filament\Admin\Resources\Grades;

use App\Filament\Admin\Resources\Grades\Pages\CreateGrade;
use App\Filament\Admin\Resources\Grades\Pages\EditGrade;
use App\Filament\Admin\Resources\Grades\Pages\ListGrades;
use App\Filament\Admin\Resources\Grades\Pages\ViewGrade;
use App\Filament\Admin\Resources\Grades\RelationManagers\StudentsRelationManager;
use App\Filament\Admin\Resources\Grades\Schemas\GradeForm;
use App\Filament\Admin\Resources\Grades\Schemas\GradeInfolist;
use App\Filament\Admin\Resources\Grades\Tables\GradesTable;
use App\Models\Grade;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $pluralLabel = 'Kelas';

    protected static ?string $singularLabel = 'Kelas';

    protected static ?string $modelLabel = 'kelas';

    public static function form(Schema $schema): Schema
    {
        return GradeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GradeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GradesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'students' => StudentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrades::route('/'),
            // 'create' => CreateGrade::route('/create'),
            'view' => ViewGrade::route('/{record}'),
            'edit' => EditGrade::route('/{record}/edit'),
        ];
    }
}
