<?php

namespace App\Services;

use App\Models\User;
use App\Models\LogMengajar;
use Exception;

class DosenSalaryService
{
    /**
     * Calculate the salary of a Dosen for a given period.
     *
     * @param int $dosenId
     * @param string $periodeBulan Format: YYYY-MM
     * @return array
     * @throws Exception
     */
    public function calculateDosenSalary($dosenId, $periodeBulan)
    {
        $dosen = User::where('id', $dosenId)->where('tipe_user', 'dosen')->first();

        if (!$dosen) {
            throw new Exception("Dosen dengan ID {$dosenId} tidak ditemukan.");
        }

        // Get Schema Rate
        $nominalPerUnit = 0;
        $skemaName = 'Tanpa Skema';
        if ($dosen->masterSkemaHonorarium) {
            $nominalPerUnit = $dosen->masterSkemaHonorarium->nominal_per_unit;
            $skemaName = $dosen->masterSkemaHonorarium->nama_skema;
        } else {
            $nominalPerUnit = $dosen->nominal_honor ?? 0;
            $skemaName = 'Honor Standar (' . ($dosen->tipe_honorarium ?? 'Per Sesi') . ')';
        }

        // Parse month and year
        $time = strtotime($periodeBulan . '-01');
        if (!$time) {
            throw new Exception("Format periode bulan salah. Gunakan format YYYY-MM.");
        }
        $year = date('Y', $time);
        $month = date('m', $time);

        // Fetch teaching units from LogMengajar
        $totalUnit = LogMengajar::where('dosen_id', $dosenId)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('jumlah_unit');

        // Total Kotor calculation
        $totalKotor = $totalUnit * $nominalPerUnit;

        // Deductions
        $potonganbpjsKesehatan = $dosen->potongan_bpjs_kesehatan ?? 0;
        $potonganbpjsKetenagakerjaan = $dosen->potongan_bpjs_ketenagakerjaan ?? 0;
        $potonganKoperasi = $dosen->potongan_koperasi ?? 0;
        $saldoKasbon = $dosen->saldo_kasbon ?? 0;

        $totalPotongan = $potonganbpjsKesehatan + $potonganbpjsKetenagakerjaan + $potonganKoperasi + $saldoKasbon;

        // Total Bersih calculation
        $totalBersih = max(0, $totalKotor - $totalPotongan);

        return [
            'dosen' => [
                'id' => $dosen->id,
                'name' => $dosen->name,
                'skema' => $skemaName,
            ],
            'total_unit' => (float) $totalUnit,
            'nominal_per_unit' => (float) $nominalPerUnit,
            'total_kotor' => (float) $totalKotor,
            'potongan' => (float) $totalPotongan,
            'total_bersih' => (float) $totalBersih,
            'periode' => $periodeBulan
        ];
    }
}
