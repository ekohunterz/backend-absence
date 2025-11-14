<?php

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Exports\StudentsExport;
use App\Filament\Admin\Resources\Students\StudentResource;
use App\Imports\StudentsImport;
use App\Models\Grade;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('promote')
                ->label('Naik Kelas')
                ->icon('heroicon-o-arrow-up')
                ->color('success')
                ->url(route('filament.admin.pages.bulk-promotion'))
                ->openUrlInNewTab(),
            // 🔵 Button Import
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Select::make('grade_id')
                        ->label('Kelas')
                        ->placeholder('Pilih Kelas (Opsional)')
                        ->helperText('Pilih kelas jika semua siswa masuk ke kelas yang sama. Kosongkan jika kelas ada di kolom Excel.')
                        ->options(Grade::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    FileUpload::make('file')
                        ->label('File Excel')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ])
                        ->maxSize(5120) // 5MB
                        ->disk('local')
                        ->directory('imports/temp')
                        ->helperText('Format: .xlsx, .xls, atau .csv (Maks 5MB)')
                        ->downloadable(),
                ])
                ->modalHeading('Import Data Siswa')
                ->modalDescription('Upload file Excel untuk import data siswa. Download template jika belum memiliki format yang sesuai.')
                ->modalSubmitActionLabel('Import')
                ->modalWidth('2xl')
                ->action(function (array $data) {
                    return $this->importStudents($data);
                })
                ->extraModalActions([
                    // 📥 Button Download Template
                    Action::make('download_template')
                        ->label('Download Template')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->action(fn() => $this->downloadTemplate()),
                ])
                ->successNotificationTitle('Import berhasil!'),
            // Button Export dengan Filter
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->form([
                    Select::make('grade_id')
                        ->label('Filter Kelas')
                        ->placeholder('Semua Kelas')
                        ->options(Grade::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->native(false),

                    Select::make('status')
                        ->label('Filter Status')
                        ->placeholder('Semua Status')
                        ->options([
                            'aktif' => 'Aktif',
                            'non-aktif' => 'Tidak Aktif',
                            'lulus' => 'Lulus',
                            'keluar' => 'Keluar',
                        ])
                        ->native(false),

                    Select::make('gender')
                        ->label('Filter Jenis Kelamin')
                        ->placeholder('Semua')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->native(false),
                ])
                ->modalHeading('Export Data Siswa')
                ->modalDescription('Pilih filter untuk export data siswa ke Excel')
                ->modalSubmitActionLabel('Export')
                ->modalWidth('xl')
                ->action(function (array $data) {
                    return $this->exportStudents($data);
                }),

            CreateAction::make()
                ->label('Tambah Siswa')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function exportStudents(array $data)
    {
        try {
            $fileName = 'data_siswa_' . now()->format('Y-m-d_His') . '.xlsx';

            // Notifikasi proses dimulai
            Notification::make()
                ->title('Export sedang diproses...')
                ->info()
                ->send();

            // Export dengan filter (return untuk download)
            return Excel::download(
                new StudentsExport(
                    gradeId: $data['grade_id'] ?? null,
                    status: $data['status'] ?? null,
                    gender: $data['gender'] ?? null
                ),
                $fileName
            );

        } catch (\Exception $e) {
            Notification::make()
                ->title('Export gagal!')
                ->body($e->getMessage())
                ->danger()
                ->send();

            \Log::error('Student Export Error: ' . $e->getMessage());
        }
    }

    protected function importStudents(array $data): void
    {
        try {
            if (!isset($data['file']) || !$data['file']) {
                Notification::make()
                    ->title('File tidak ditemukan')
                    ->danger()
                    ->send();
                return;
            }

            $filePath = Storage::disk('local')->path($data['file']);
            $gradeId = $data['grade_id'] ?? null;

            // Validasi file exists
            if (!file_exists($filePath)) {
                throw new \Exception('File tidak ditemukan di storage');
            }

            // Import dengan error handling
            $import = new StudentsImport($gradeId);
            Excel::import($import, $filePath);

            // Get failures
            $failures = $import->failures();
            $failureCount = count($failures);

            // Hapus file temporary
            Storage::disk('local')->delete($data['file']);

            // Notifikasi berdasarkan hasil
            if ($failureCount > 0) {
                $this->handleImportWithErrors($failures, $failureCount);
            } else {
                Notification::make()
                    ->title('Import berhasil!')
                    ->body('Semua data siswa berhasil diimport')
                    ->success()
                    ->send();
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->handleValidationErrors($e->failures());
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import gagal!')
                ->body($e->getMessage())
                ->danger()
                ->send();

            \Log::error('Student Import Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleImportWithErrors($failures, int $failureCount): void
    {
        $errorMessages = collect($failures)
            ->take(5)
            ->map(fn($failure) => "Baris {$failure->row()}: " . implode(', ', $failure->errors()))
            ->implode("\n");

        if ($failureCount > 5) {
            $errorMessages .= "\n... dan " . ($failureCount - 5) . " error lainnya";
        }

        Notification::make()
            ->title('Import selesai dengan error')
            ->body("Data valid berhasil diimport. {$failureCount} baris gagal:\n\n{$errorMessages}")
            ->warning()
            ->duration(10000)
            ->send();
    }

    protected function handleValidationErrors($failures): void
    {
        $errorMessages = collect($failures)
            ->take(5)
            ->map(fn($failure) => "Baris {$failure->row()}: " . implode(', ', $failure->errors()))
            ->implode("\n");

        Notification::make()
            ->title('Validasi gagal')
            ->body($errorMessages)
            ->danger()
            ->duration(10000)
            ->send();
    }

    protected function downloadTemplate()
    {
        $templatePath = resource_path('templates/template_import_siswa.xlsx');

        if (!file_exists($templatePath)) {
            Notification::make()
                ->title('Template tidak ditemukan')
                ->body('Jalankan: php artisan app:create-import-command')
                ->danger()
                ->send();
            return;
        }

        return response()->download(
            $templatePath,
            'template_import_siswa.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="template_import_siswa.xlsx"'
            ]
        );
    }
}
