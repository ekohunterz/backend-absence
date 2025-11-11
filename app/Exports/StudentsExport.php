<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class StudentsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle,
    ShouldAutoSize
{
    protected ?int $gradeId;
    protected ?string $status;
    protected ?string $gender;

    public function __construct(
        ?int $gradeId = null,
        ?string $status = null,
        ?string $gender = null
    ) {
        $this->gradeId = $gradeId;
        $this->status = $status;
        $this->gender = $gender;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $query = Student::with('grade')->orderBy('name');

        // Filter berdasarkan kelas
        if ($this->gradeId) {
            $query->where('grade_id', $this->gradeId);
        }

        // Filter berdasarkan status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Filter berdasarkan gender
        if ($this->gender) {
            $query->where('gender', $this->gender);
        }

        return $query->get();
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'NO',
            'NIS',
            'NAMA LENGKAP',
            'EMAIL',
            'JENIS KELAMIN',
            'TANGGAL LAHIR',
            'ALAMAT',
            'TELEPON SISWA',
            'NAMA ORANG TUA',
            'TELEPON ORANG TUA',
            'KELAS',
            'STATUS',
        ];
    }

    /**
     * Map data untuk setiap row
     */
    public function map($student): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $student->nis,
            $student->name,
            $student->email ?? '-',
            $student->gender ?? '-',
            $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d-m-Y') : '-',
            $student->address ?? '-',
            $student->phone ?? '-',
            $student->parent_name ?? '-',
            $student->parent_phone ?? '-',
            $student->grade?->name ?? '-',
            $student->status ?? '-',
        ];
    }



    /**
     * Style untuk Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Auto height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Border untuk semua data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:L' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Alignment untuk kolom nomor
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 15,  // NIS
            'C' => 30,  // NAMA
            'D' => 25,  // EMAIL
            'E' => 18,  // JENIS KELAMIN
            'F' => 18,  // TANGGAL LAHIR
            'G' => 35,  // ALAMAT
            'H' => 18,  // TELEPON SISWA
            'I' => 25,  // NAMA ORANG TUA
            'J' => 18,  // TELEPON ORANG TUA
            'K' => 12,  // KELAS
            'L' => 15,  // STATUS
        ];
    }

    /**
     * Judul sheet
     */
    public function title(): string
    {
        return 'Data Siswa';
    }
}