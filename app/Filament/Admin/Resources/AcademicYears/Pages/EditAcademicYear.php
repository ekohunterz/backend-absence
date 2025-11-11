<?php

namespace App\Filament\Admin\Resources\AcademicYears\Pages;

use App\Filament\Admin\Resources\AcademicYears\AcademicYearResource;
use App\Models\AcademicYear;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAcademicYear extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('activate')
                ->label('Aktifkan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn() => $this->record->activate())
                ->visible(fn() => !$this->record->is_active),

            DeleteAction::make()
                ->before(function () {
                    if ($this->record->is_active) {
                        Notification::make()
                            ->title('Tidak dapat menghapus')
                            ->body('Tahun ajaran aktif tidak dapat dihapus')
                            ->danger()
                            ->send();

                        return false;
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If is_active is changed to true, deactivate others
        if (($data['is_active'] ?? false) && !$this->record->is_active) {
            AcademicYear::where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Ensure only one semester is active per academic year
        if ($this->record->semesters()->where('is_active', true)->count() > 1) {
            $firstActive = $this->record->semesters()->where('is_active', true)->first();
            $this->record->semesters()
                ->where('id', '!=', $firstActive->id)
                ->update(['is_active' => false]);
        }

        // If academic year is deactivated, deactivate all semesters
        if (!$this->record->is_active) {
            $this->record->semesters()->update(['is_active' => false]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tahun ajaran diperbarui')
            ->body('Data tahun ajaran berhasil disimpan.');
    }
}
