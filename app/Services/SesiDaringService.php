<?php

namespace App\Services;

use App\Models\SesiDaring;
use App\Models\LaporanMengajar;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SesiDaringService
{
    /**
     * Start a live online session.
     *
     * @param int $sesiDaringId
     * @param int $userId
     * @return SesiDaring
     * @throws Exception
     */
    public function startLiveSession($sesiDaringId, $userId)
    {
        return DB::transaction(function () use ($sesiDaringId, $userId) {
            $sesi = SesiDaring::with('jadwal')->findOrFail($sesiDaringId);

            if ($sesi->status_sesi !== 'scheduled') {
                throw new Exception("Sesi hanya bisa dimulai jika dalam status 'scheduled'. Status saat ini: {$sesi->status_sesi}");
            }

            // Authorization: must be the owner of the schedule or an admin
            $user = User::findOrFail($userId);
            $isOwner = $sesi->jadwal->dosen_id == $userId;
            $isAdmin = $user->hasRole('admin') || $user->is_admin == 'admin';

            if (!$isOwner && !$isAdmin) {
                throw new Exception("Anda tidak berhak memulai sesi ini karena Anda bukan pemilik jadwal.");
            }

            $sesi->update([
                'status_sesi' => 'live',
                'start_time' => Carbon::now(),
            ]);

            return $sesi;
        });
    }

    /**
     * End a live online session and calculate wage records.
     *
     * @param int $sesiDaringId
     * @return array
     * @throws Exception
     */
    public function endLiveSession($sesiDaringId)
    {
        return DB::transaction(function () use ($sesiDaringId) {
            $sesi = SesiDaring::with('jadwal.dosen.masterSkemaHonorarium')->findOrFail($sesiDaringId);

            if ($sesi->status_sesi !== 'live') {
                throw new Exception("Sesi hanya bisa dihentikan jika dalam status 'live'. Status saat ini: {$sesi->status_sesi}");
            }

            $endTime = Carbon::now();
            $startTime = Carbon::parse($sesi->start_time);
            
            // Calculate duration in minutes
            $durasiMenit = $startTime->diffInMinutes($endTime);

            $sesi->update([
                'status_sesi' => 'ended',
                'end_time' => $endTime,
            ]);

            $dosen = $sesi->jadwal->dosen;
            
            // Retrieve rate
            $rate = 0;
            if ($dosen->masterSkemaHonorarium) {
                $rate = $dosen->masterSkemaHonorarium->nominal_per_unit;
            } else {
                $rate = $dosen->nominal_honor ?? 0;
            }

            $statusPembayaran = 'pending';
            $catatanSistem = null;
            $totalGaji = 0;

            if ($durasiMenit >= 1) {
                $totalGaji = 1.00 * $rate;
                $statusPembayaran = 'valid';
                $catatanSistem = "Sesi valid. Durasi: {$durasiMenit} menit dengan tarif Rp " . number_format($rate, 0, ',', '.') . " per unit.";

                // Create LogMengajar record
                \App\Models\LogMengajar::create([
                    'dosen_id' => $dosen->id,
                    'kelas_id' => $sesi->jadwal->nama_kelas,
                    'tanggal' => $endTime->toDateString(),
                    'jumlah_unit' => 1.00,
                ]);

                // Create TransaksiMengajar record
                \App\Models\TransaksiMengajar::create([
                    'dosen_id' => $dosen->id,
                    'kelas_id' => $sesi->jadwal->nama_kelas,
                    'tanggal' => $endTime->toDateString(),
                    'jumlah_sesi_token' => 1.00,
                    'nominal_honor' => $rate,
                ]);
            } else {
                $statusPembayaran = 'invalid';
                $catatanSistem = "Peringatan: Durasi sesi kurang dari 1 menit ({$durasiMenit} menit). Sesi tidak valid untuk dibayar.";
            }

            // Create LaporanMengajar record
            $laporan = LaporanMengajar::create([
                'dosen_id' => $dosen->id,
                'sesi_daring_id' => $sesi->id,
                'durasi_menit' => $durasiMenit,
                'total_gaji' => $totalGaji,
                'status_pembayaran' => $statusPembayaran,
                'catatan_sistem' => $catatanSistem,
            ]);

            return [
                'sesi' => $sesi,
                'laporan' => $laporan,
                'durasi_menit' => $durasiMenit,
                'total_gaji' => $totalGaji,
                'status_pembayaran' => $statusPembayaran,
                'catatan_sistem' => $catatanSistem
            ];
        });
    }
}
