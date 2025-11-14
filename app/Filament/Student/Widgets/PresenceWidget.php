<?php

namespace App\Filament\Student\Widgets;

use App\Models\Attendance;
use App\Models\AttendanceDetail;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class PresenceWidget extends Widget
{
    protected string $view = 'filament.student.widgets.presence-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public ?AttendanceDetail $todayAttendance = null;
    public ?string $status = null;
    public ?string $statusLabel = null;
    public ?string $statusColor = null;
    public ?string $statusIcon = null;
    public ?string $statusMessage = null;
    public ?string $checkInTime = null;
    public ?string $checkOutTime = null;
    public ?string $notes = null;

    public function mount(): void
    {
        $this->loadTodayAttendance();
    }

    protected function loadTodayAttendance(): void
    {
        $student = auth('student')->user();

        if (!$student) {
            $this->setNoDataStatus();
            return;
        }

        // Get today's attendance
        $this->todayAttendance = AttendanceDetail::whereHas('attendance', function ($query) use ($student) {
            $query->where('grade_id', $student->grade_id)
                ->whereDate('presence_date', today());
        })
            ->where('student_id', $student->id)
            ->first();

        if ($this->todayAttendance) {
            $this->setAttendanceStatus();
        } else {
            $this->setNotPresentStatus();
        }
    }

    protected function setAttendanceStatus(): void
    {
        $this->status = $this->todayAttendance->status;
        $this->notes = $this->todayAttendance->notes;

        // Set check-in/out times
        if ($this->todayAttendance->check_in_time) {
            $this->checkInTime = Carbon::parse($this->todayAttendance->check_in_time)->format('H:i');
        }

        if ($this->todayAttendance->check_out_time) {
            $this->checkOutTime = Carbon::parse($this->todayAttendance->check_out_time)->format('H:i');
        }

        // Set status details based on type
        switch ($this->status) {
            case 'hadir':
                $this->statusLabel = 'Hadir';
                $this->statusColor = 'success';
                $this->statusIcon = 'heroicon-o-check-circle';
                $this->statusMessage = $this->checkOutTime
                    ? 'Anda sudah melakukan check-in dan check-out hari ini'
                    : 'Anda sudah check-in. Jangan lupa check-out nanti';
                break;

            case 'izin':
                $this->statusLabel = 'Izin';
                $this->statusColor = 'warning';
                $this->statusIcon = 'heroicon-o-information-circle';
                $this->statusMessage = 'Anda telah mengajukan izin untuk tidak hadir hari ini';
                break;

            case 'sakit':
                $this->statusLabel = 'Sakit';
                $this->statusColor = 'info';
                $this->statusIcon = 'heroicon-o-heart';
                $this->statusMessage = 'Anda sedang sakit. Semoga cepat sembuh!';
                break;

            case 'alpa':
                $this->statusLabel = 'Alpa';
                $this->statusColor = 'danger';
                $this->statusIcon = 'heroicon-o-x-circle';
                $this->statusMessage = 'Anda tidak melakukan presensi dan belum ada keterangan';
                break;

            default:
                $this->statusLabel = 'Tidak Diketahui';
                $this->statusColor = 'gray';
                $this->statusIcon = 'heroicon-o-question-mark-circle';
                $this->statusMessage = 'Status tidak diketahui';
        }
    }

    protected function setNotPresentStatus(): void
    {
        $this->status = 'belum_absen';
        $this->statusLabel = 'Belum Absen';
        $this->statusColor = 'warning';
        $this->statusIcon = 'heroicon-o-clock';
        $this->statusMessage = 'Anda belum melakukan presensi hari ini. Segera lakukan check-in!';
    }

    protected function setNoDataStatus(): void
    {
        $this->status = 'no_data';
        $this->statusLabel = 'Data Tidak Ditemukan';
        $this->statusColor = 'gray';
        $this->statusIcon = 'heroicon-o-exclamation-triangle';
        $this->statusMessage = 'Data siswa tidak ditemukan dalam sistem';
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->statusColor) {
            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function getCardGradient(): string
    {
        return match ($this->statusColor) {
            'success' => 'from-green-500 to-green-600',
            'warning' => 'from-yellow-500 to-yellow-600',
            'info' => 'from-blue-500 to-blue-600',
            'danger' => 'from-red-500 to-red-600',
            default => 'from-gray-500 to-gray-600',
        };
    }

    public function getActionButton(): ?array
    {
        if ($this->status === 'belum_absen') {
            return [
                'label' => 'Check-In Sekarang',
                'url' => route('filament.student.pages.presence'),
                'icon' => 'heroicon-o-arrow-right',
            ];
        }

        if ($this->status === 'hadir' && !$this->checkOutTime) {
            return [
                'label' => 'Check-Out',
                'url' => route('filament.student.pages.presence'),
                'icon' => 'heroicon-o-arrow-right',
            ];
        }

        return null;
    }
}

