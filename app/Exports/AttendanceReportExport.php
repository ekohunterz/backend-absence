<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle
{
    protected $reportData;
    protected $maxDays;

    public function __construct(array $reportData, int $maxDays = 31)
    {
        $this->reportData = $reportData;
        $this->maxDays = $maxDays;
    }

    public function collection()
    {
        $rows = collect();

        if (empty($this->reportData['students'])) {
            return $rows;
        }

        foreach ($this->reportData['students'] as $student) {
            $row = [
                $student['name'],
                $student['nis'],
                $student['stats']['hadir'],
                $student['stats']['sakit'],
                $student['stats']['izin'],
                $student['stats']['alpa'],
            ];

            // Add all days for each month
            foreach ($student['months'] as $monthData) {
                for ($day = 1; $day <= $this->maxDays; $day++) {
                    $dayData = $monthData['days'][$day] ?? null;

                    if ($dayData === null) {
                        $row[] = '';
                    } elseif (isset($dayData['is_weekend']) && $dayData['is_weekend'] && !isset($dayData['status'])) {
                        $row[] = '•';
                    } elseif (isset($dayData['status'])) {
                        $status = $dayData['status'];
                        $row[] = match ($status) {
                            'hadir' => 'H',
                            'sakit' => 'S',
                            'izin' => 'I',
                            'alpa' => 'A',
                            default => '-',
                        };
                    } else {
                        $row[] = '';
                    }
                }
            }

            $rows->push($row);
        }

        return $rows;
    }

    public function headings(): array
    {
        $headings = [
            ['LAPORAN ABSENSI SISWA'],
            [
                ($this->reportData['semester']->academicYear->name ?? '-') . ' - ' .
                ($this->reportData['semester']->name ?? '-') . ' - Kelas ' .
                ($this->reportData['grade']->name ?? '-')
            ],
            [], // Empty row
        ];

        // Main headers (month names) - will be merged later
        $mainHeaders = ['NAMA', 'NIS', 'H', 'S', 'I', 'A'];
        foreach ($this->reportData['months'] as $month) {
            // Only add month name once, will be merged across all days
            $mainHeaders[] = $month['month_name_short'];
            for ($i = 1; $i < $this->maxDays; $i++) {
                $mainHeaders[] = ''; // Empty cells for merging
            }
        }
        $headings[] = $mainHeaders;

        // Day numbers
        $dayHeaders = ['', '', '', '', '', ''];
        foreach ($this->reportData['months'] as $month) {
            for ($day = 1; $day <= $this->maxDays; $day++) {
                $dayHeaders[] = $day;
            }
        }
        $headings[] = $dayHeaders;

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->getColumnLetter(6 + (count($this->reportData['months']) * $this->maxDays));
        $lastRow = count($this->reportData['students']) + 5;

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(25); // NAMA
        $sheet->getColumnDimension('B')->setWidth(15); // NIS
        for ($i = 3; $i <= 6; $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setWidth(5); // H, S, I, A
        }
        for ($i = 7; $i <= (6 + (count($this->reportData['months']) * $this->maxDays)); $i++) {
            $sheet->getColumnDimension($this->getColumnLetter($i))->setWidth(3); // Days
        }

        // Title
        $sheet->mergeCells('A1:' . $lastColumn . '1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Subtitle
        $sheet->mergeCells('A2:' . $lastColumn . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Merge cells for month names (row 4)
        $startCol = 7; // After NAMA, NIS, H, S, I, A
        foreach ($this->reportData['months'] as $index => $month) {
            $endCol = $startCol + $this->maxDays - 1;
            $startColLetter = $this->getColumnLetter($startCol);
            $endColLetter = $this->getColumnLetter($endCol);

            // Merge month name across all days
            $sheet->mergeCells($startColLetter . '4:' . $endColLetter . '4');

            $startCol = $endCol + 1;
        }

        // Merge NAMA and NIS vertically (rows 4-5)
        $sheet->mergeCells('A4:A5');
        $sheet->mergeCells('B4:B5');
        $sheet->mergeCells('C4:C5'); // H
        $sheet->mergeCells('D4:D5'); // S
        $sheet->mergeCells('E4:E5'); // I
        $sheet->mergeCells('F4:F5'); // A

        // Headers styling
        $sheet->getStyle('A4:' . $lastColumn . '5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
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
                ],
            ],
        ]);

        // Data rows
        if ($lastRow > 5) {
            $sheet->getStyle('A6:' . $lastColumn . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Name & NIS columns (left align)
            $sheet->getStyle('A6:B' . $lastRow)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Color coding for status
            $this->applyStatusColors($sheet, $lastRow);
        }

        // Auto height
        foreach ($sheet->getRowIterator() as $row) {
            $sheet->getRowDimension($row->getRowIndex())->setRowHeight(-1);
        }

        // Freeze panes (freeze headers)
        $sheet->freezePane('G6'); // Freeze up to column F (stats) and row 5 (headers)

        return [];
    }

    protected function applyStatusColors(Worksheet $sheet, int $lastRow): void
    {
        $startCol = 7; // After NAMA, NIS, H, S, I, A

        $studentIndex = 0;
        for ($row = 6; $row <= $lastRow; $row++) {
            if (!isset($this->reportData['students'][$studentIndex])) {
                break;
            }

            $col = $startCol;
            $student = $this->reportData['students'][$studentIndex];

            foreach ($student['months'] ?? [] as $monthData) {
                for ($day = 1; $day <= $this->maxDays; $day++) {
                    $cell = $this->getColumnLetter($col) . $row;
                    $value = $sheet->getCell($cell)->getValue();

                    $color = match ($value) {
                        'H' => '22C55E', // green
                        'S' => 'EAB308', // yellow
                        'I' => '3B82F6', // blue
                        'A' => 'EF4444', // red
                        '•' => 'E5E7EB', // gray for weekend
                        default => null,
                    };

                    if ($color) {
                        $fontColor = ($value === '•') ? '6B7280' : 'FFFFFF';

                        $sheet->getStyle($cell)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $color],
                            ],
                            'font' => [
                                'color' => ['rgb' => $fontColor],
                                'bold' => ($value !== '•')
                            ],
                        ]);
                    }

                    $col++;
                }
            }

            $studentIndex++;
        }
    }

    protected function getColumnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intval($index / 26);
        }
        return $letter;
    }

    public function title(): string
    {
        return 'Laporan Absensi';
    }
}