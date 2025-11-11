<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Siswa')
                    ->collapsible()
                    ->schema([
                        ImageEntry::make('avatar_url')
                            ->hiddenLabel()
                            ->defaultImageUrl(function ($record) {
                                $hash = md5(mb_strtolower(mb_trim($record->email)));

                                return 'https://www.gravatar.com/avatar/' . $hash . '?d=mp&r=g&s=250';
                            })
                            ->placeholder('-'),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nis')
                                    ->label('NIS'),
                                TextEntry::make('name')
                                    ->label('Nama'),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn($state) => $state == 'L' ? 'Laki-laki' : 'Perempuan'),
                                TextEntry::make('birth_date')
                                    ->date()
                                    ->label('Tanggal Lahir')
                                    ->placeholder('-'),
                                TextEntry::make('address')
                                    ->label('Alamat')
                                    ->placeholder('-'),
                                TextEntry::make('phone')
                                    ->label('Nomor Telepon')
                                    ->placeholder('-'),
                                TextEntry::make('grade.name')
                                    ->label('Kelas')
                                    ->placeholder('-'),
                                TextEntry::make('parent_name')
                                    ->label('Nama Orang Tua')
                                    ->placeholder('-'),
                                TextEntry::make('parent_phone')
                                    ->label('Nomor Telepon Orang Tua')
                                    ->placeholder('-'),
                            ])->columnSpan('5'),

                    ])->columns(6)->columnSpanFull(),
                Section::make('')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }
}
