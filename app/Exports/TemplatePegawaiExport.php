<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePegawaiExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'Nama*', 'Email*', 'Username*', 'Password*', 'Telepon*', 'Lokasi*', 'Tanggal Lahir*',
            'Jenis Kelamin* (Laki-Laki/Perempuan)', 'Tanggal Masuk*', 'Role*', 'Divisi*', 'Is Admin (user/admin)',
            'Nama Ibu Kandung*', 'Tipe User (pegawai/dosen)', 'Status Pajak ID', 'Alamat', 'Alamat Domisili', 
            'Kontak Darurat Nama', 'Kontak Darurat HP', 'Kontak Darurat Hubungan', 'KTP', 'Kartu Keluarga', 
            'BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'NPWP', 'SIM', 'NIP', 'No Kontrak', 'Tanggal Mulai Kontrak',
            'Tanggal Berakhir Kontrak', 'No Rekening', 'Nama Rekening', 'Cuti', 'Izin Masuk',
            'Izin Telat', 'Izin Pulang Cepat', 'Cuti Melahirkan', 'Cuti Kematian', 'Gaji Pokok',
            'Tunjangan Makan', 'Tunjangan Transport', 'Tunjangan BPJS Kesehatan', 'Tunjangan BPJS Ketenagakerjaan',
            'Lembur', 'Kehadiran', 'THR', 'Bonus Pribadi', 'Bonus Team', 'Bonus Jackpot', 'Terlambat',
            'Batas Terlambat', 'Mangkir', 'Potongan BPJS Kesehatan', 'Potongan Koperasi', 'Kode Pos'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Budi Santoso', 'budi@example.com', 'budi123', '12345678', '08123456789', 'Kantor Pusat',
                '1995-05-20', 'Laki-Laki', '2023-01-10', 'pegawai', 'IT Support', 'user', 'Siti Aminah',
                'pegawai', '1', 'Jl. Merdeka No. 123, Jakarta', 'Jl. Merdeka No. 123, Jakarta', 'Siti Aminah',
                '08123456789', 'Orang Tua', '3171234567890001', '3171234567890001', '1234567890',
                '1234567890', '1234567890', 'A12345678', '123456', 'KTR-001', '2023-01-10',
                '2024-01-10', '1234567890', 'Budi Santoso', '12', '3', '3', '3', '90', '3',
                '5000000', '500000', '300000', '200000', '200000', '100000', '100000', '500000',
                '200000', '100000', '50000', '5', '100000', '50000', '12345'
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
