<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMengajar extends Model
{
    use HasFactory;

    protected $table = 'laporan_mengajars';

    protected $fillable = [
        'dosen_id',
        'sesi_daring_id',
        'token_input',
        'durasi_menit',
        'total_gaji',
        'status_pembayaran',
        'catatan_sistem',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function sesiDaring()
    {
        return $this->belongsTo(SesiDaring::class, 'sesi_daring_id');
    }
}
