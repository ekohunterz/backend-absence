<?php

namespace App\Filament\Admin\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tahun Ajaran')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Tahun Ajaran')
                            ->placeholder('2024/2025')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Format: YYYY/YYYY'),

                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $start = \Carbon\Carbon::parse($state);
                                    // Auto set end date (1 year later)
                                    $set('end_date', $start->copy()->addYear()->subDay()->format('Y-m-d'));
                                }
                            }),

                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('start_date'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Hanya 1 tahun ajaran yang dapat aktif dalam 1 waktu')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Semester')
                    ->description('Semester akan otomatis dibuat saat menyimpan tahun ajaran')
                    ->schema([
                        Repeater::make('semesters')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Semester')
                                    ->required()
                                    ->maxLength(255)
                                    ->default(fn($get) => $get('semester') == 1 ? 'Semester 1 (Ganjil)' : 'Semester 2 (Genap)'),

                                Select::make('semester')
                                    ->label('Tipe Semester')
                                    ->options([
                                        1 => 'Semester 1 (Ganjil)',
                                        2 => 'Semester 2 (Genap)',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('name', $state == 1 ? 'Semester 1 (Ganjil)' : 'Semester 2 (Genap)');
                                    }),

                                DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                                DatePicker::make('end_date')
                                    ->label('Tanggal Selesai')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->after('start_date'),

                                Toggle::make('is_active')
                                    ->label('Semester Aktif')
                                    ->helperText('Hanya 1 semester yang dapat aktif per tahun ajaran')
                                    ->default(false),

                            ])
                            ->columns(2)
                            ->defaultItems(2)
                            ->collapsible()
                            ->maxItems(2)
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Semester')
                            ->addActionLabel('Tambah Semester')
                            ->reorderable(false)
                            ->deletable(fn($state) => count($state ?? []) > 2)
                    ])
            ]);

    }
}
