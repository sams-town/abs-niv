<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class DosenExport implements FromQuery, WithColumnFormatting, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
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
            'Nama Dosen',
            'NIDN',
            'Jabatan Akademik',
            'Mata Kuliah',
            'Username',
            'Email',
            'Nomor Handphone',
            'Lokasi Kantor',
            'Tanggal Lahir',
            'Gender',
            'Tanggal Masuk',
            'Pendidikan Terakhir',
            'Status Kepegawaian',
            'Tipe Honorarium',
            'Nominal Honor',
            'Nomor KTP',
            'Nomor NPWP',
            'Alamat',
            'Status Aktif'
        ];
    }

    public function map($model): array
    {
        return [
            $model->name ?? '-',
            $model->nidn ?? '-',
            $model->jabatan_akademik ?? '-',
            $model->mata_kuliah ?? '-',
            $model->username ?? '-',
            $model->email ?? '-',
            $model->telepon ?? '-',
            $model->Lokasi->nama_lokasi ?? '-',
            $model->tgl_lahir ?? '-',
            $model->gender ?? '-',
            $model->tgl_join ?? '-',
            $model->pendidikan_terakhir ?? '-',
            $model->status_kepegawaian ?? '-',
            $model->tipe_honorarium ?? '-',
            'Rp ' . number_format($model->nominal_honor, 0, ',', '.'),
            $model->ktp ?? '-',
            $model->npwp ?? '-',
            $model->alamat ?? '-',
            $model->status_aktif ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function columnFormats(): array
    {
        return [
        ];
    }

    public function query()
    {
        $search = request()->input('search');
        $data = User::dosen()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%'.$search.'%')
                  ->orWhere('email', 'LIKE', '%'.$search.'%')
                  ->orWhere('telepon', 'LIKE', '%'.$search.'%')
                  ->orWhere('username', 'LIKE', '%'.$search.'%')
                  ->orWhere('nidn', 'LIKE', '%'.$search.'%');
            });
        })
        ->orderBy('name', 'ASC');

        return $data;
    }
}
