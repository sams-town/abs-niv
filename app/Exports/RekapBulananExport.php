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
use App\Models\User;
use App\Models\Lokasi;
use Carbon\Carbon;

class RekapBulananExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $bulan;
    protected $tahun;
    protected $lokasi_id;
    protected $search;

    public function __construct($bulan, $tahun, $lokasi_id = null, $search = null)
    {
        $this->bulan     = $bulan;
        $this->tahun     = $tahun;
        $this->lokasi_id = $lokasi_id;
        $this->search    = $search;
    }

    public function title(): string
    {
        return 'Rekap Bulanan';
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
            'Hadir',
            'Alpha',
            'Terlambat',
            'Cuti',
            'Izin',
            'Sakit',
            'Libur',
            'Total Telat',
            '% Kehadiran',
        ];
    }

    public function collection()
    {
        $mulai = "{$this->tahun}-{$this->bulan}-01";
        $akhir = date('Y-m-t', strtotime($mulai));

        $tanggal_range = [];
        $current = strtotime($mulai);
        while ($current <= strtotime($akhir)) {
            $tanggal_range[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }

        $users = User::with(['MappingShift' => function ($q) use ($mulai, $akhir) {
            $q->whereBetween('tanggal', [$mulai, $akhir]);
        }, 'Jabatan', 'Lokasi'])
        ->pegawaiDanDosen()
        ->when($this->lokasi_id, fn($q) => $q->where('lokasi_id', $this->lokasi_id))
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->orderBy('name')
        ->get();

        $rows = collect();
        $no   = 1;

        foreach ($users as $user) {
            $shifts = $user->MappingShift->keyBy('tanggal');
            $hadir = $terlambat = $cuti = $izin = $sakit = $libur = $alpha = 0;
            $total_telat_detik = 0;

            foreach ($tanggal_range as $tgl) {
                $ms     = $shifts->get($tgl);
                $status = $ms->status_absen ?? null;
                match ($status) {
                    'Masuk'             => $hadir++,
                    'Izin Telat'        => ($hadir++ && $terlambat++),
                    'Izin Pulang Cepat' => ($hadir++ && $terlambat++),
                    'Cuti'              => $cuti++,
                    'Izin Masuk'        => $izin++,
                    'Sakit'             => $sakit++,
                    'Libur'             => $libur++,
                    default             => $alpha++,
                };
                if ($ms && $ms->telat > 0) {
                    $total_telat_detik += (int) $ms->telat;
                }
            }

            $total_hari  = count($tanggal_range);
            $persentase  = $total_hari > 0 ? round((($hadir + $libur) / $total_hari) * 100, 1) : 0;
            $jam_telat   = floor($total_telat_detik / 3600);
            $menit_telat = floor(($total_telat_detik % 3600) / 60);

            $rows->push([
                $no++,
                $user->name,
                $user->username,
                $user->Jabatan->nama_jabatan ?? '-',
                $user->Lokasi->nama_lokasi ?? '-',
                $hadir,
                $alpha,
                $terlambat,
                $cuti,
                $izin,
                $sakit,
                $libur,
                "{$jam_telat}j {$menit_telat}m",
                "{$persentase}%",
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Header tabel (baris 5)
        $sheet->getStyle("A5:{$lastColumn}5")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        if ($lastRow > 5) {
            // Zebra striping rows
            for ($r = 6; $r <= $lastRow; $r++) {
                $bg = ($r % 2 === 0) ? 'FFF8FAFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$r}:{$lastColumn}{$r}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                // Center angka
                $sheet->getStyle("F{$r}:N{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Warna kolom % kehadiran (kolom N)
            for ($r = 6; $r <= $lastRow; $r++) {
                $pctVal = floatval($sheet->getCell("N{$r}")->getValue());
                $color = $pctVal >= 80 ? ['bg' => 'FFD1FAE5', 'fg' => 'FF065F46']
                       : ($pctVal >= 50 ? ['bg' => 'FFFEF3C7', 'fg' => 'FF92400E']
                       : ['bg' => 'FFFEE2E2', 'fg' => 'FF991B1B']);
                $sheet->getStyle("N{$r}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => $color['fg']]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color['bg']]],
                ]);
            }

            // Baris total (baris terakhir + 1)
            $totalRow = $lastRow + 1;
            $sheet->setCellValue("A{$totalRow}", 'TOTAL');
            $sheet->mergeCells("A{$totalRow}:E{$totalRow}");

            // Sum kolom F s.d. L (hadir s.d. libur)
            foreach (['F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
                $sheet->setCellValue("{$col}{$totalRow}", "=SUM({$col}6:{$col}{$lastRow})");
            }

            $sheet->getStyle("A{$totalRow}:N{$totalRow}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FF0F172A']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        // Border
        $endRow = $lastRow + 1;
        $sheet->getStyle("A5:{$lastColumn}{$endRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF94A3B8']],
            ],
        ]);

        // No kolom center
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function registerEvents(): array
    {
        $bulan_list = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return [
            AfterSheet::class => function (AfterSheet $event) use ($bulan_list) {
                $sheet     = $event->sheet->getDelegate();
                $namaBulan = $bulan_list[(int)$this->bulan] . ' ' . $this->tahun;
                $mulai     = Carbon::parse("{$this->tahun}-{$this->bulan}-01")->format('d/m/Y');
                $akhir     = Carbon::parse("{$this->tahun}-{$this->bulan}-01")->endOfMonth()->format('d/m/Y');
                $lokasi    = $this->lokasi_id ? (Lokasi::find($this->lokasi_id)->nama_lokasi ?? 'Semua Lokasi') : 'Semua Lokasi';
                $generated = Carbon::now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

                // Baris 1 — Judul
                $sheet->mergeCells('A1:N1');
                $sheet->setCellValue('A1', 'REKAP ABSEN BULANAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Baris 2 — Periode
                $sheet->mergeCells('A2:N2');
                $sheet->setCellValue('A2', "Periode: {$namaBulan} ({$mulai} – {$akhir})  |  Lokasi: {$lokasi}");
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['argb' => 'FF64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Baris 3 — Waktu cetak
                $sheet->mergeCells('A3:N3');
                $sheet->setCellValue('A3', "Dicetak: {$generated}");
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['argb' => 'FF94A3B8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Baris 4 — Kosong
                $sheet->getRowDimension(4)->setRowHeight(8);

                // Header tinggi
                $sheet->getRowDimension(5)->setRowHeight(22);

                // Freeze
                $sheet->freezePane('A6');

                // Lebar kolom
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(28);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(25);
                foreach (['F','G','H','I','J','K','L'] as $col) {
                    $sheet->getColumnDimension($col)->setWidth(10);
                }
                $sheet->getColumnDimension('M')->setWidth(13);
                $sheet->getColumnDimension('N')->setWidth(13);
            },
        ];
    }
}
