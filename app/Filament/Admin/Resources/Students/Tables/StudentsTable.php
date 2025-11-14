<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Services\PromotionService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->defaultImageUrl(url('https://www.gravatar.com/avatar/64e1b8d34f425d19e1ee2ea7236d3028?d=mp&r=g&s=250'))
                    ->label('')
                    ->alignEnd()
                    ->square()
                    ->grow(false),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('JK')
                    ->badge(),
                TextColumn::make('grade.name')
                    ->label('Kelas Aktif')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('Kelas')
                    ->multiple()
                    ->relationship('grade', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions([
                // Promote Single Student
                Action::make('promote')
                    ->label('')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->tooltip('Naik Kelas')
                    ->modalHeading('Naik Kelas')
                    ->modalDescription(fn(Model $record) => "Pindahkan {$record->name} ke kelas baru")
                    ->form([
                        Select::make('grade_id')
                            ->label('Kelas Tujuan')
                            ->options(Grade::orderBy('name')->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Pilih kelas tujuan untuk siswa ini')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $grade = Grade::find($state);
                                if ($grade && $grade->capacity) {
                                    $currentCount = $grade->students()->where('status', 'aktif')->count();
                                    $set('capacity_info', "Kapasitas: {$currentCount}/{$grade->capacity}");
                                }
                            }),
                        Textarea::make('reason')
                            ->label('Alasan')
                            ->placeholder('Opsional: Masukkan alasan perpindahan kelas')
                            ->rows(3),
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(AcademicYear::orderByDesc('name')->pluck('name', 'id'))
                            ->default(fn() => AcademicYear::where('is_active', true)->first()?->id)
                            ->required()
                            ->helperText('Tahun ajaran untuk mencatat riwayat'),
                    ])
                    ->action(function (array $data, Model $record) {
                        $service = new PromotionService();

                        $result = $service->promoteSingleStudent(
                            $record,
                            $data['grade_id'],
                            $data['reason'] ?? null,
                            $data['academic_year_id']
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->title('Berhasil Naik Kelas')
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal Naik Kelas')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),

                // View Grade History
                Action::make('history')
                    ->label('')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->tooltip('Riwayat Kelas')
                    ->modalHeading('Riwayat Perpindahan Kelas')
                    ->modalContent(fn(Model $record) => view('filament.admin.components.grade-history', [
                        'student' => $record,
                        'histories' => (new PromotionService())->getStudentHistory($record->id)
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Detail'),

                EditAction::make()
                    ->label('')
                    ->tooltip('Ubah'),

                DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk Promote
                    BulkAction::make('bulk_promote')
                        ->label('Naik Kelas Massal')
                        ->icon('heroicon-o-arrow-up')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Naik Kelas Massal')
                        ->modalDescription(fn(Collection $records) => "Pindahkan {$records->count()} siswa ke kelas baru")
                        ->form([
                            Select::make('grade_id')
                                ->label('Kelas Tujuan')
                                ->options(Grade::orderBy('name')->pluck('name', 'id'))
                                ->preload()
                                ->searchable()
                                ->required()
                                ->helperText('Semua siswa terpilih akan dipindahkan ke kelas ini'),
                            Textarea::make('reason')
                                ->label('Alasan')
                                ->placeholder('Contoh: Naik kelas tahun ajaran 2024/2025')
                                ->rows(3),
                            Select::make('academic_year_id')
                                ->label('Tahun Ajaran')
                                ->options(AcademicYear::orderByDesc('name')->pluck('name', 'id'))
                                ->default(fn() => AcademicYear::where('is_active', true)->first()?->id)
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            $service = new PromotionService();

                            $result = $service->promoteMultipleStudents(
                                $records,
                                $data['grade_id'],
                                $data['reason'] ?? null,
                                $data['academic_year_id']
                            );

                            if ($result['success']) {
                                $message = $result['message'];

                                if ($result['failed_count'] > 0) {
                                    $message .= "\n\nGagal: {$result['failed_count']} siswa";
                                }

                                Notification::make()
                                    ->title('Proses Selesai')
                                    ->body($message)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Gagal')
                                    ->body($result['message'])
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Graduate Students
                    BulkAction::make('graduate')
                        ->label('Luluskan')
                        ->icon('heroicon-o-academic-cap')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Luluskan Siswa')
                        ->modalDescription(fn(Collection $records) => "Luluskan {$records->count()} siswa menjadi alumni")
                        ->form([
                            DatePicker::make('graduation_date')
                                ->label('Tanggal Kelulusan')
                                ->default(now())
                                ->required(),
                            Select::make('academic_year_id')
                                ->label('Tahun Ajaran')
                                ->options(AcademicYear::orderByDesc('name')->pluck('name', 'id'))
                                ->default(fn() => AcademicYear::where('is_active', true)->first()?->id)
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan')
                                ->placeholder('Catatan kelulusan (opsional)')
                                ->rows(3),
                        ])
                        ->action(function (array $data, Collection $records) {
                            $service = new PromotionService();

                            $result = $service->graduateStudents(
                                $records,
                                $data['academic_year_id']
                            );

                            if ($result['success']) {
                                Notification::make()
                                    ->title('Selamat!')
                                    ->body($result['message'])
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Gagal')
                                    ->body($result['message'])
                                    ->danger()
                                    ->send();
                            }
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
