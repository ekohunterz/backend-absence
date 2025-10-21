<?php

namespace App\Filament\Admin\Resources\Majors;

use App\Filament\Admin\Resources\Majors\Pages\CreateMajor;
use App\Filament\Admin\Resources\Majors\Pages\EditMajor;
use App\Filament\Admin\Resources\Majors\Pages\ListMajors;
use App\Filament\Admin\Resources\Majors\Schemas\MajorForm;
use App\Filament\Admin\Resources\Majors\Tables\MajorsTable;
use App\Models\Major;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MajorResource extends Resource
{
    protected static ?string $model = Major::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $pluralLabel = 'Jurusan';

    protected static ?string $singularLabel = 'Jurusan';

    protected static ?string $modelLabel = 'jurusan';

    public static function form(Schema $schema): Schema
    {
        return MajorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MajorsTable::configure($table);
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
            'index' => ListMajors::route('/'),
            // 'create' => CreateMajor::route('/create'),
            // 'edit' => EditMajor::route('/{record}/edit'),
        ];
    }
}
