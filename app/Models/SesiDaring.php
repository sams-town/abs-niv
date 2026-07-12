<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiDaring extends Model
{
    use HasFactory;

    protected $table = 'sesi_darings';

    protected $fillable = [
        'jadwal_id',
        'meeting_url',
        'meeting_id',
        'passcode',
        'status_sesi',
        'start_time',
        'end_time',
        'catatan',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function laporanMengajars()
    {
        return $this->hasMany(LaporanMengajar::class, 'sesi_daring_id');
    }
}
