<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsenPivotExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected array $rows;
    protected array $dates;
    protected string $mulai;
    protected string $akhir;

    public function __construct(array $rows, array $dates, string $mulai, string $akhir)
    {
        $this->rows  = $rows;
        $this->dates = $dates;
        $this->mulai = $mulai;
        $this->akhir = $akhir;
    }

    public function headings(): array
    {
        $headers = ['No', 'Nama Pegawai'];
        foreach ($this->dates as $date) {
            $headers[] = date('d/m', strtotime($date));
        }
        $headers[] = 'Hadir';
        $headers[] = 'Cuti';
        $headers[] = 'Izin';
        $headers[] = 'Alfa';
        $headers[] = 'Sakit';
        $headers[] = 'Persentase (%)';
        return [$headers];
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->rows as $i => $row) {
            $line = [$i + 1, $row['user']->name];
            foreach ($row['codes'] as $code) {
                $line[] = $code;
            }
            $summary = $row['summary'];
            $line[] = $summary['hadir'];
            $line[] = $summary['cuti'];
            $line[] = $summary['izin'];
            $line[] = $summary['alfa'];
            $line[] = $summary['sakit'];
            $line[] = $summary['persentase'] . '%';
            $data[] = $line;
        }
        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow    = $sheet->getHighestRow();

        // Border seluruh tabel
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
              ->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        // Header center + bold
        $sheet->getStyle("A1:{$highestColumn}1")
              ->getAlignment()
              ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Vertical top untuk semua
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
              ->getAlignment()
              ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Wrap text
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
              ->getAlignment()
              ->setWrapText(true);

        return [
            1 => ['font' => ['bold' => true]],
            'B' => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $dateCount  = count($this->dates);
                $startCol   = 3; // kolom C = index 3 (A=1, B=2)
                $rowCount   = count($this->rows);

                for ($rowIdx = 0; $rowIdx < $rowCount; $rowIdx++) {
                    $excelRow = $rowIdx + 2; // baris 2 ke bawah (baris 1 = header)
                    $codes    = $this->rows[$rowIdx]['codes'];
                    foreach ($codes as $colOffset => $code) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol + $colOffset);
                        $cellRef   = "{$colLetter}{$excelRow}";
                        if ($code === 'A') {
                            $sheet->getStyle($cellRef)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('FFFF00'); // kuning
                        } elseif ($code === 'S') {
                            $sheet->getStyle($cellRef)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('FFB6C1'); // merah muda
                        }
                    }
                }
            },
        ];
    }
}
