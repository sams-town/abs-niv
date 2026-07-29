<?php

namespace App\Imports;

use App\Models\Payroll;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PayrollImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Temukan user berdasarkan NIP (dari Excel)
        $user = User::where('nip', $row['nip'])->first();

        // Jika tidak ada user dengan NIP tersebut, lewati baris ini
        if (!$user) {
            return null;
        }

        // Cari record payroll yang sudah ada untuk user dan periode tersebut agar bisa diupdate jika ada
        $payroll = Payroll::where('user_id', $user->id)
                          ->where('bulan', $row['bulan'])
                          ->where('tahun', $row['tahun'])
                          ->first();

        $data = [
            'user_id' => $user->id,
            'tanggal_mulai' => $row['tanggal_mulai'],
            'tanggal_akhir' => $row['tanggal_akhir'],
            'bulan' => $row['bulan'],
            'tahun' => $row['tahun'],
            'persentase_kehadiran' => $row['persentase_kehadiran'] ?? '0',
            'no_gaji' => $row['no_gaji'] ?? '-',
            'gaji_pokok' => $row['gaji_pokok'] ?? 0,
            'total_reimbursement' => $row['total_reimbursement'] ?? 0,
            'jumlah_tunjangan_transport' => $row['jumlah_tunjangan_transport'] ?? 0,
            'uang_tunjangan_transport' => $row['uang_tunjangan_transport'] ?? 0,
            'total_tunjangan_transport' => $row['total_tunjangan_transport'] ?? 0,
            'jumlah_tunjangan_makan' => $row['jumlah_tunjangan_makan'] ?? 0,
            'uang_tunjangan_makan' => $row['uang_tunjangan_makan'] ?? 0,
            'total_tunjangan_makan' => $row['total_tunjangan_makan'] ?? 0,
            'total_tunjangan_bpjs_kesehatan' => $row['total_tunjangan_bpjs_kesehatan'] ?? 0,
            'total_tunjangan_bpjs_ketenagakerjaan' => $row['total_tunjangan_bpjs_ketenagakerjaan'] ?? 0,
            'total_potongan_bpjs_kesehatan' => $row['total_potongan_bpjs_kesehatan'] ?? 0,
            'total_potongan_bpjs_ketenagakerjaan' => $row['total_potongan_bpjs_ketenagakerjaan'] ?? 0,
            'jumlah_mangkir' => $row['jumlah_mangkir'] ?? 0,
            'uang_mangkir' => $row['uang_mangkir'] ?? 0,
            'total_mangkir' => $row['total_mangkir'] ?? 0,
            'jumlah_lembur' => $row['jumlah_lembur'] ?? 0,
            'uang_lembur' => $row['uang_lembur'] ?? 0,
            'total_lembur' => $row['total_lembur'] ?? 0,
            'jumlah_izin' => $row['jumlah_izin'] ?? 0,
            'uang_izin' => $row['uang_izin'] ?? 0,
            'total_izin' => $row['total_izin'] ?? 0,
            'bonus_pribadi' => $row['bonus_pribadi'] ?? 0,
            'bonus_team' => $row['bonus_team'] ?? 0,
            'bonus_jackpot' => $row['bonus_jackpot'] ?? 0,
            'jumlah_terlambat' => $row['jumlah_terlambat'] ?? 0,
            'uang_terlambat' => $row['uang_terlambat'] ?? 0,
            'total_terlambat' => $row['total_terlambat'] ?? 0,
            'jumlah_kehadiran' => $row['jumlah_kehadiran'] ?? 0,
            'uang_kehadiran' => $row['uang_kehadiran'] ?? 0,
            'total_kehadiran' => $row['total_kehadiran'] ?? 0,
            'saldo_kasbon' => $row['saldo_kasbon'] ?? 0,
            'bayar_kasbon' => $row['bayar_kasbon'] ?? 0,
            'jumlah_thr' => $row['jumlah_thr'] ?? 0,
            'uang_thr' => $row['uang_thr'] ?? 0,
            'total_thr' => $row['total_thr'] ?? 0,
            'loss' => $row['loss'] ?? 0,
            'total_penjumlahan' => $row['total_penjumlahan'] ?? 0,
            'total_pengurangan' => $row['total_pengurangan'] ?? 0,
            'grand_total' => $row['grand_total'] ?? 0,
        ];

        // Jika sudah ada, update record tersebut
        if ($payroll) {
            $payroll->update($data);
            return null; // karena update sudah dilakukan secara manual
        }

        return new Payroll($data);
    }
}
