<?php

namespace App\Helpers;

use App\Models\Cuti;
use App\Models\User;

class CutiStateHelper
{
    public static function canApproveLevel1(Cuti $cuti, User $user): bool
    {
        return $user->hasRole('kepala_cabang')
            && $cuti->status_approval_1 === 'Pending'
            && $user->lokasi_id === optional($cuti->User)->lokasi_id;
    }

    public static function canApproveLevel2(Cuti $cuti, User $user): bool
    {
        return ($user->hasRole('admin') || $user->hasRole('hrd'))
            && in_array($cuti->status_approval_1, ['Disetujui', 'Dilewati'], true)
            && $cuti->status_cuti === 'Pending';
    }

    public static function getBadgeForKaryawan(Cuti $cuti): string
    {
        if ($cuti->status_cuti === 'Diterima') {
            return 'Diterima';
        }
        if ($cuti->status_cuti === 'Ditolak' || $cuti->status_approval_1 === 'Ditolak') {
            return 'Ditolak';
        }
        if ($cuti->status_approval_1 === 'Pending' && $cuti->status_cuti === 'Pending') {
            return 'Menunggu Persetujuan Manager';
        }
        if (in_array($cuti->status_approval_1, ['Disetujui', 'Dilewati'], true) && $cuti->status_cuti === 'Pending') {
            return 'Menunggu Persetujuan Admin/HRD';
        }
        return 'Pending';
    }
}
