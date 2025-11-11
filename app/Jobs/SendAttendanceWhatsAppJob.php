<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAttendanceWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah maksimal percobaan jika gagal
     */
    public int $tries = 3;

    /**
     * Timeout dalam detik
     */
    public int $timeout = 60;

    /**
     * Delay antar retry (dalam detik)
     */
    public int $backoff = 10;

    protected Student $student;
    protected string $status;
    protected string $date;
    protected string $time;
    protected string $gradeName;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Student $student,
        string $status,
        string $date,
        string $time,
        string $gradeName
    ) {
        $this->student = $student;
        $this->status = $status;
        $this->date = $date;
        $this->time = $time;
        $this->gradeName = $gradeName;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsappService): void
    {
        // Validasi nomor HP orangtua
        if (empty($this->student->parent_phone)) {
            Log::warning("Student {$this->student->name} has no parent phone number");
            return;
        }

        // Kirim notifikasi
        $sent = $whatsappService->sendAttendanceNotification(
            phoneNumber: $this->student->parent_phone,
            studentName: $this->student->name,
            status: $this->status,
            date: $this->date,
            time: $this->time,
            gradeName: $this->gradeName
        );

        if ($sent) {
            Log::info("WhatsApp notification sent successfully", [
                'student_id' => $this->student->id,
                'student_name' => $this->student->name,
                'phone' => $this->student->parent_phone,
                'status' => $this->status
            ]);
        } else {
            // Lempar exception untuk trigger retry
            throw new \Exception("Failed to send WhatsApp to {$this->student->parent_phone}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send WhatsApp notification after {$this->tries} attempts", [
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'phone' => $this->student->parent_phone,
            'error' => $exception->getMessage()
        ]);

        // Opsional: Kirim notifikasi ke admin bahwa ada pengiriman yang gagal
        // Notification::make()...
    }
}
