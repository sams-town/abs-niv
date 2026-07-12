<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiMengajar extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transaksi) {
            $transaksi->total_gaji = $transaksi->jumlah_sesi_token * $transaksi->nominal_honor;
        });
    }
}
