<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Grade;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class StudentsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading,
    SkipsEmptyRows,
    SkipsOnFailure,
    WithSkipDuplicates
{
    use SkipsFailures;

    protected ?int $gradeId = null;


    public function __construct(?int $gradeId = null)
    {
        $this->gradeId = $gradeId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Ambil grade_id dari parameter atau dari kolom excel
        $gradeId = $this->gradeId ?? $this->getGradeIdFromRow($row);

        if (!$gradeId) {
            return null;
        }

        return new Student([
            'nis' => $row['nis'] ?? null,
            'name' => $row['nama'] ?? $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'gender' => $this->normalizeGender($row['jenis_kelamin'] ?? $row['gender'] ?? null),
            'birth_date' => $this->parseBirthDate($row['tanggal_lahir'] ?? $row['birth_date'] ?? null),
            'address' => $row['alamat'] ?? $row['address'] ?? null,
            'phone' => $this->normalizePhone($row['telepon'] ?? $row['phone'] ?? null),
            'password' => $this->hashPassword($row['nis'] ?? null),
            'parent_name' => $row['nama_orangtua'] ?? $row['parent_name'] ?? null,
            'parent_phone' => $this->normalizePhone($row['telepon_orangtua'] ?? $row['parent_phone'] ?? null),
            'status' => $row['status'] ?? 'aktif',
            'grade_id' => $gradeId,
        ]);
    }

    /**
     * Ambil grade_id dari nama kelas di excel
     */
    protected function getGradeIdFromRow(array $row): ?int
    {
        $gradeName = $row['kelas'] ?? $row['grade'] ?? null;

        if (!$gradeName) {
            return null;
        }

        $grade = Grade::where('name', $gradeName)->first();

        return $grade?->id;
    }

    /**
     * Normalisasi gender (L/P, Laki-laki/Perempuan, Male/Female)
     */
    protected function normalizeGender(?string $gender): ?string
    {
        if (!$gender) {
            return null;
        }

        $gender = strtolower(trim($gender));

        return match ($gender) {
            'l', 'laki-laki', 'laki', 'male', 'm' => 'L',
            'p', 'perempuan', 'female', 'f' => 'P',
            default => $gender
        };
    }

    /**
     * Password hashing
     */
    protected function hashPassword(?string $password): ?string
    {
        return $password ? bcrypt($password) : null;
    }

    /**
     * Parse tanggal lahir dari berbagai format
     */
    protected function parseBirthDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            // Jika sudah format date object dari excel
            if ($date instanceof \DateTime) {
                return Carbon::instance($date)->format('Y-m-d');
            }

            // Parse string date
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Normalisasi nomor telepon
     */
    protected function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Hapus karakter non-digit kecuali +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum ada kode negara, tambahkan 62
        if (substr($phone, 0, 1) !== '+' && substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Validasi data
     */
    public function rules(): array
    {
        return [
            'nis' => [
                'required',
                'max:20',
                Rule::unique('students', 'nis')
            ],
            'nama' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('students', 'email')
            ],
            'jenis_kelamin' => 'required|in:laki-laki,perempuan,L,P,l,p,Laki-laki,Perempuan,male,female,M,F',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'nama_orangtua' => 'nullable|string|max:255',
            'telepon_orangtua' => 'nullable|string|max:20',
            'status' => 'nullable|in:aktif,non-aktif,lulus,keluar',
        ];
    }

    /**
     * Custom attribute names untuk pesan error
     */
    public function customValidationAttributes(): array
    {
        return [
            'nis' => 'NIS',
            'nama' => 'Nama',
            'email' => 'Email',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat' => 'Alamat',
            'telepon' => 'Telepon',
            'nama_orangtua' => 'Nama Orang Tua',
            'telepon_orangtua' => 'Telepon Orang Tua',
            'status' => 'Status',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'nis.required' => 'NIS wajib diisi',
            'nis.unique' => 'NIS :input sudah terdaftar',
            'nama.required' => 'Nama siswa wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email :input sudah terdaftar',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L/P atau Laki-laki/Perempuan',
        ];
    }

    /**
     * Batch insert untuk performa
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk reading untuk file besar
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get failures untuk ditampilkan ke user
     */
    public function getFailures(): array
    {
        return $this->failures()->all();
    }
}
