<?php

namespace App\Filament\Admin\Resources\AcademicYears\Pages;

use App\Filament\Admin\Resources\AcademicYears\AcademicYearResource;
use App\Models\AcademicYear;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If is_active is true, deactivate others
        if ($data['is_active'] ?? false) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Auto-create semesters if not provided
        if ($this->record->semesters()->count() === 0) {
            $this->createDefaultSemesters();
        }

        // Activate first semester if academic year is active
        if ($this->record->is_active) {
            $firstSemester = $this->record->semesters()->first();
            if ($firstSemester) {
                $firstSemester->activate();
            }
        }
    }

    protected function createDefaultSemesters(): void
    {
        $startYear = \Carbon\Carbon::parse($this->record->start_date)->year;

        // Semester 1 (Ganjil): Juli - Desember
        $this->record->semesters()->create([
            'name' => 'Semester 1 (Ganjil)',
            'semester' => 1,
            'start_date' => "{$startYear}-07-01",
            'end_date' => "{$startYear}-12-31",
            'is_active' => $this->record->is_active,
            'description' => 'Semester Ganjil (Juli - Desember)',
        ]);

        // Semester 2 (Genap): Januari - Juni
        $this->record->semesters()->create([
            'name' => 'Semester 2 (Genap)',
            'semester' => 2,
            'start_date' => ($startYear + 1) . "-01-01",
            'end_date' => ($startYear + 1) . "-06-30",
            'is_active' => false,
            'description' => 'Semester Genap (Januari - Juni)',
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tahun ajaran dibuat')
            ->body('Tahun ajaran dan semester berhasil dibuat.');
    }
}
