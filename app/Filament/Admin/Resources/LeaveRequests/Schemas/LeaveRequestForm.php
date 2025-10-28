<?php

namespace App\Filament\Admin\Resources\LeaveRequests\Schemas;

use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $grade = Student::find($state)->grade_id;
                            $set('grade_id', $grade);
                        }
                    })
                    ->required(),
                Select::make('grade_id')
                    ->relationship('grade', 'name')
                    ->required(),
                Select::make('academic_year_id')
                    ->relationship('academic_year', 'start_year')
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                Select::make('type')
                    ->options(['sakit' => 'Sakit', 'izin' => 'Izin'])
                    ->required(),
                Textarea::make('reason')
                    ->columnSpanFull(),
                FileUpload::make('proof_file')
                    ->required()
                    ->visibility('public')
                    ->disk('public')
                    ->directory('leave_requests')
                    ->image(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->required(),
                TextInput::make('verified_by')
                    ->numeric(),
            ]);
    }
}
