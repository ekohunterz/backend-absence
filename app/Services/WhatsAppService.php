<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $token;

    public function __construct()
    {
        // Konfigurasi dari .env
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->token = config('services.whatsapp.token');
    }

    /**
     * Kirim notifikasi absensi ke orangtua
     */
    public function sendAttendanceNotification(
        string $phoneNumber,
        string $studentName,
        string $status,
        string $date,
        string $time,
        string $gradeName
    ): bool {
        // Format nomor telepon (hapus 0 di awal, tambah 62)
        $phone = $this->formatPhoneNumber($phoneNumber);

        // Template pesan
        $message = $this->buildAttendanceMessage(
            $studentName,
            $status,
            $date,
            $time,
            $gradeName
        );

        return $this->sendMessage($phone, $message);
    }

    /**
     * Format nomor telepon ke format internasional
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Hapus karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum ada kode negara, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Buat template pesan absensi
     */
    protected function buildAttendanceMessage(
        string $studentName,
        string $status,
        string $date,
        string $time,
        string $gradeName
    ): string {
        $statusEmoji = match ($status) {
            'hadir' => '✅',
            'sakit' => '🤒',
            'izin' => '📝',
            'alpa' => '❌',
            default => 'ℹ️'
        };

        $statusText = match ($status) {
            'hadir' => 'HADIR',
            'sakit' => 'SAKIT',
            'izin' => 'IZIN',
            'alpa' => 'ALPA',
            default => strtoupper($status)
        };

        return "*NOTIFIKASI ABSENSI SISWA* {$statusEmoji}\n\n" .
            "Yth. Orangtua/Wali\n\n" .
            "Berikut informasi absensi anak Anda:\n\n" .
            "👤 *Nama:* {$studentName}\n" .
            "🏫 *Kelas:* {$gradeName}\n" .
            "📅 *Tanggal:* {$date}\n" .
            "🕐 *Waktu:* {$time}\n" .
            "📊 *Status:* *{$statusText}*\n\n" .
            "Terima kasih atas perhatiannya.\n\n" .
            "_Pesan otomatis dari Sistem Absensi Sekolah_";
    }

    /**
     * Kirim pesan WhatsApp
     * Contoh menggunakan Fonnte API
     */
    protected function sendMessage(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                        'target' => $phone,
                        'message' => $message,
                        'countryCode' => '62',
                    ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent successfully to {$phone}");
                return true;
            }

            Log::error("Failed to send WhatsApp to {$phone}: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan massal
     */
    public function sendBulkMessages(array $recipients): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($recipients as $recipient) {
            $sent = $this->sendAttendanceNotification(
                $recipient['phone'],
                $recipient['student_name'],
                $recipient['status'],
                $recipient['date'],
                $recipient['time'],
                $recipient['grade_name']
            );

            if ($sent) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $recipient['phone'];
            }

            // Delay untuk menghindari rate limit
            usleep(500000); // 0.5 detik
        }

        return $results;
    }
}