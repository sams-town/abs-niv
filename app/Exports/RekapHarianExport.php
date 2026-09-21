<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use App\Models\User;
use App\Models\Lokasi;
use Carbon\Carbon;

class RekapHarianExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $tanggal;
    protected $lokasi_id;
    protected $search;

    public function __construct($tanggal, $lokasi_id = null, $search = null)
    {
        $this->tanggal   = $tanggal;
        $this->lokasi_id = $lokasi_id;
        $this->search    = $search;
    }

    public function title(): string
    {
        return 'Rekap Harian';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Username',
            'Jabatan',
            'Lokasi',
            'Shift',
            'Jam Masuk',
            'Telat',
            'Jam Pulang',
            'Status Kehadiran',
        ];
    }

    public function collection()
    {
        $users = User::with(['MappingShift' => function ($q) {
            $q->where('tanggal', $this->tanggal)->with('Shift');
        }, 'Jabatan', 'Lokasi'])
        ->pegawaiDanDosen()
        ->when($this->lokasi_id, fn($q) => $q->where('lokasi_id', $this->lokasi_id))
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->orderBy('name')
        ->get();

        $rows = collect();
        $no   = 1;

        foreach ($users as $user) {
            $ms     = $user->MappingShift->first();
            $status = $ms->status_absen ?? 'Alpha';

            // Format telat
            $telatStr = '-';
            if ($ms && $ms->telat > 0) {
                $jam   = floor($ms->telat / 3600);
                $menit = floor(($ms->telat % 3600) / 60);
                $detik = $ms->telat % 60;
                $telatStr = ($jam > 0 ? "{$jam} Jam " : '') . "{$menit} Menit";
                if ($detik > 0) $telatStr .= " {$detik} Detik";
            }

            // Shift
            $shiftStr = '-';
            if ($ms && $ms->Shift) {
                $shiftStr = $ms->Shift->nama_shift . ' (' . $ms->Shift->jam_masuk . ' - ' . $ms->Shift->jam_keluar . ')';
            }

            $rows->push([
                $no++,
                $user->name,
                $user->username,
                $user->Jabatan->nama_jabatan ?? '-',
                $user->Lokasi->nama_lokasi ?? '-',
                $shiftStr,
                $ms->jam_absen ?? '-',
                $telatStr,
                $ms->jam_pulang ?? '-',
                $status,
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Header baris judul sudah di-set via events
        // Style header tabel (baris 5)
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Header tabel
        $sheet->getStyle("A5:{$lastColumn}5")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        // Data rows
        if ($lastRow > 5) {
            for ($r = 6; $r <= $lastRow; $r++) {
                $bg = ($r % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$r}:{$lastColumn}{$r}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                ]);
            }

            // Color status kolom J
            for ($r = 6; $r <= $lastRow; $r++) {
                $statusVal = $sheet->getCell("J{$r}")->getValue();
                $color = match($statusVal) {
                    'Masuk'              => ['bg' => 'FFD1FAE5', 'fg' => 'FF065F46'],
                    'Alpha'              => ['bg' => 'FFFEE2E2', 'fg' => 'FF991B1B'],
                    'Izin Telat',
                    'Izin Pulang Cepat'  => ['bg' => 'FFFEF3C7', 'fg' => 'FF92400E'],
                    'Cuti'               => ['bg' => 'FFEDE9FE', 'fg' => 'FF4338CA'],
                    'Izin Masuk'         => ['bg' => 'FFDBEAFE', 'fg' => 'FF1E40AF'],
                    'Sakit'              => ['bg' => 'FFFCE7F3', 'fg' => 'FF9D174D'],
                    'Libur'              => ['bg' => 'FFF3F4F6', 'fg' => 'FF374151'],
                    default              => ['bg' => 'FFFEE2E2', 'fg' => 'FF991B1B'],
                };
                $sheet->getStyle("J{$r}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => $color['fg']]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color['bg']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }
        }

        // Border seluruh tabel
        $sheet->getStyle("A5:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
            ],
        ]);

        // Kolom No center
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $tanggal   = Carbon::parse($this->tanggal)->translatedFormat('d F Y');
                $lokasi    = $this->lokasi_id ? (Lokasi::find($this->lokasi_id)->nama_lokasi ?? 'Semua Lokasi') : 'Semua Lokasi';
                $generated = Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

                // Row 1 — Judul utama
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'REKAP ABSEN HARIAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Row 2 — Sub judul
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', "Tanggal: {$tanggal}  |  Lokasi: {$lokasi}");
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['argb' => 'FF64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 3 — Waktu cetak
                $sheet->mergeCells('A3:J3');
                $sheet->setCellValue('A3', "Dicetak: {$generated}");
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF94A3B8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 4 — Garis pemisah (kosong)
                $sheet->getRowDimension(4)->setRowHeight(8);

                // Row 5 (header) tinggi
                $sheet->getRowDimension(5)->setRowHeight(22);

                // Freeze panes di baris header
                $sheet->freezePane('A6');

                // Set lebar kolom spesifik
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(28);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(22);
                $sheet->getColumnDimension('G')->setWidth(12);
                $sheet->getColumnDimension('H')->setWidth(16);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(16);
            },
        ];
    }
}
