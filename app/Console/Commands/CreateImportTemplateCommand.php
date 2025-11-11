<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CreateImportTemplateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-import-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat template Excel untuk import siswa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Membuat template import siswa...');

        // Buat folder jika belum ada
        $templatePath = resource_path('templates');
        if (!file_exists($templatePath)) {
            mkdir($templatePath, 0755, true);
        }

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul sheet
        $sheet->setTitle('Data Siswa');

        // Header
        $headers = [
            'A1' => 'nis',
            'B1' => 'nama',
            'C1' => 'email',
            'D1' => 'jenis_kelamin',
            'E1' => 'tanggal_lahir',
            'F1' => 'alamat',
            'G1' => 'telepon',
            'H1' => 'nama_orangtua',
            'I1' => 'telepon_orangtua',
            'J1' => 'kelas',
            'K1' => 'status',
        ];

        // Isi header
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header
        $headerStyle = [
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
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Contoh data
        $exampleData = [
            ['12345', 'Ahmad Rizki', 'ahmad@email.com', 'L', '2010-05-15', 'Jl. Merdeka No. 10', '081234567890', 'Budi Santoso', '081283255389', 'X TKJ 1', 'aktif'],
            ['12346', 'Siti Nurhaliza', 'siti@email.com', 'P', '2010-08-20', 'Jl. Sudirman No. 25', '081234567891', 'Agus Setiawan', '081283255389', 'X TKJ 1', 'aktif'],
            ['12347', 'Budi Santoso', 'budi@email.com', 'L', '2010-03-10', 'Jl. Ahmad Yani No. 5', '081234567892', 'Suryanto', '081283255389', 'X RPL 1', 'aktif'],
        ];

        // Isi data contoh
        $row = 2;
        foreach ($exampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Set lebar kolom
        $columnWidths = [
            'A' => 12,  // nis
            'B' => 25,  // nama
            'C' => 25,  // email
            'D' => 15,  // jenis_kelamin
            'E' => 15,  // tanggal_lahir
            'F' => 35,  // alamat
            'G' => 15,  // telepon
            'H' => 25,  // nama_orangtua
            'I' => 17,  // telepon_orangtua
            'J' => 10,  // kelas
            'K' => 12,  // status
        ];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Auto height untuk semua baris
        foreach ($sheet->getRowIterator() as $row) {
            $sheet->getRowDimension($row->getRowIndex())->setRowHeight(-1);
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Tambahkan sheet instruksi
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instruksi');

        $instructions = [
            ['INSTRUKSI IMPORT DATA SISWA'],
            [''],
            ['Kolom Wajib:'],
            ['- nis: Nomor Induk Siswa (harus unik)'],
            ['- nama: Nama lengkap siswa'],
            ['- jenis_kelamin: L/P atau Laki-laki/Perempuan'],
            [''],
            ['Kolom Opsional:'],
            ['- email: Alamat email (jika diisi harus unik)'],
            ['- tanggal_lahir: Format YYYY-MM-DD (contoh: 2010-05-15)'],
            ['- alamat: Alamat lengkap siswa'],
            ['- telepon: Nomor HP siswa'],
            ['- nama_orangtua: Nama orang tua/wali'],
            ['- telepon_orangtua: Nomor HP orang tua (untuk notifikasi WhatsApp)'],
            ['- kelas: Nama kelas (opsional jika sudah dipilih di form import)'],
            ['- status: aktif/non-aktif/lulus/keluar (default: aktif)'],
            [''],
            ['Catatan:'],
            ['- Jangan ubah nama kolom di baris header'],
            ['- Hapus baris contoh sebelum import data sebenarnya'],
            ['- Nomor telepon akan otomatis diformat ke 62xxx'],
            ['- Data yang error tidak akan diimport, tapi yang valid tetap masuk'],
        ];

        $rowNum = 1;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $rowNum, $instruction[0]);
            $rowNum++;
        }

        // Style instruksi
        $instructionSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
        ]);
        $instructionSheet->getColumnDimension('A')->setWidth(80);

        // Set sheet Data Siswa sebagai active
        $spreadsheet->setActiveSheetIndex(0);

        // Save file
        $filePath = $templatePath . '/template_import_siswa.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $this->info('✅ Template berhasil dibuat!');
        $this->info('📁 Lokasi: ' . $filePath);

        return Command::SUCCESS;
    }
}
