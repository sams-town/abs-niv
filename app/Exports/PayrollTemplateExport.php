<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class PayrollTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'NIP', // Lookup for user_id
            'tanggal_mulai', // format YYYY-MM-DD
            'tanggal_akhir', // format YYYY-MM-DD
            'bulan',
            'tahun',
            'persentase_kehadiran',
            'no_gaji',
            'gaji_pokok',
            'total_reimbursement',
            'jumlah_tunjangan_transport',
            'uang_tunjangan_transport',
            'total_tunjangan_transport',
            'jumlah_tunjangan_makan',
            'uang_tunjangan_makan',
            'total_tunjangan_makan',
            'total_tunjangan_bpjs_kesehatan',
            'total_tunjangan_bpjs_ketenagakerjaan',
            'total_potongan_bpjs_kesehatan',
            'total_potongan_bpjs_ketenagakerjaan',
            'jumlah_mangkir',
            'uang_mangkir',
            'total_mangkir',
            'jumlah_lembur',
            'uang_lembur',
            'total_lembur',
            'jumlah_izin',
            'uang_izin',
            'total_izin',
            'bonus_pribadi',
            'bonus_team',
            'bonus_jackpot',
            'jumlah_terlambat',
            'uang_terlambat',
            'total_terlambat',
            'jumlah_kehadiran',
            'uang_kehadiran',
            'total_kehadiran',
            'saldo_kasbon',
            'bayar_kasbon',
            'jumlah_thr',
            'uang_thr',
            'total_thr',
            'loss',
            'total_penjumlahan',
            'total_pengurangan',
            'grand_total'
        ];
    }
}
