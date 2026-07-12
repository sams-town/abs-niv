<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class PajakExport implements FromQuery, WithColumnFormatting, WithMapping, WithHeadings,ShouldAutoSize,WithStyles
{
    use Exportable;

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        //BORDER
        $sheet->getStyle("A1:$highestColumn" . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // HEADER
        $sheet->getStyle("A1:" . $highestColumn . "1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // WRAP TEXT
        $sheet->getStyle("A1:$highestColumn" . $highestRow)->getAlignment()->setWrapText(true);

        // ALIGNMENT TEXT
        $sheet->getStyle("A1:$highestColumn" . $highestRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        //BOLD FIRST ROW
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Bulan',
            'Tahun',
            'Status Pajak',
            'Penghasilan Bruto',
            'Penghasilan Netto Sebulan',
            'Penghasilan Netto Setahun',
            'PTKP',
            'PKP Setahun',
            'PPH 21 Setahun',
            'PPH 21 Sebulan',
        ];
    }

    public function map($model): array
    {

        Carbon::setLocale('id');
        $bulan = Carbon::createFromFormat('m', $model->bulan)->translatedFormat('F');

        if ($model->total_penjumlahan > 500000) {
            $biaya_jabatan = 500000;
        } else {
            $biaya_jabatan = $model->total_penjumlahan * (5/100);
        }

        $pengurang_netto = $biaya_jabatan + $model->total_potongan_bpjs_kesehatan + $model->total_potongan_bpjs_ketenagakerjaan;

        $netto = $model->total_penjumlahan - $pengurang_netto;
        $netto_year = $netto * 12;

        $pkp = $netto_year - $model->user->sp->ptkp;
        $pkp = $pkp < 0 ? 0 : $pkp;

        if ($pkp <= 50000000) {
            $tax = 5;
        } else if ($pkp > 50000000 && $pkp <= 250000000) {
            $tax = 15;
        } else if ($pkp > 250000000 && $pkp <= 500000000) {
            $tax = 25;
        } else {
            $tax = 50;
        }

        $pph21year = $pkp * ($tax/100);
        $pph21month = $pph21year / 12;

        return [
            $model->user->name ?? '-',
            $bulan ?? '-',
            $model->tahun ?? '-',
            $model->user->sp->name ?? '-',
            $model->total_penjumlahan ?? '-',
            $netto ?? '-',
            $netto_year ?? '-',
            $model->user->sp->ptkp ?? '-',
            $pkp ?? '-',
            $pph21year ?? '-',
            $pph21month ?? '-',
        ];


    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function query()
    {
        date_default_timezone_set('Asia/Jakarta');
        $bulan = request()->input('bulan');
        $tahun = request()->input('tahun');
        $modelroll = Payroll::when($bulan, function ($query) use ($bulan) {
            $query->where('bulan', $bulan);
        })
        ->when($tahun, function ($query) use ($tahun) {
            $query->where('tahun', $tahun);
        })
        ->orderBy('no_gaji', 'DESC');

        return $modelroll;
    }
}
