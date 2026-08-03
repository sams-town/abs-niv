<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateDosenExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama*', 'Email*', 'Username*', 'Password*', 'Telepon*', 'Lokasi*', 'Tanggal Lahir*',
            'Jenis Kelamin* (Laki-Laki/Perempuan)', 'Tanggal Masuk*', 'Role*', 'Jabatan*', 'Is Admin (user/admin)',
            'Nama Ibu Kandung*', 'Status Pajak ID', 'Alamat', 'Alamat Domisili', 
            'NIDN*', 'NIP', 'Gelar Depan', 'Gelar Belakang', 'Program Studi', 'Pendidikan Terakhir',
            'Status Kepegawaian', 'Tipe Honorarium', 'Nominal Honor', 'Jabatan Akademik', 'Mata Kuliah'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Dr. Budi Santoso', 'budi.dosen@example.com', 'budidosen', '12345678', '08123456789', 'Kampus Utama',
                '1980-05-20', 'Laki-Laki', '2015-01-10', 'dosen', 'Dosen Tetap', 'user',
                'Siti Aminah', '1', 'Jl. Pendidikan No. 1', 'Jl. Pendidikan No. 1',
                '0011223344', '198005202015011001', 'Dr.', 'M.Kom.', 'Sistem Informasi', 'S3',
                'PNS', 'Per SKS', '50000', 'Lektor', 'Pemrograman Web, Basis Data'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
