<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogMengajar extends Model
{
    use HasFactory;

    protected $table = 'log_mengajars';

    protected $fillable = [
        'dosen_id',
        'kelas_id',
        'tanggal',
        'jumlah_unit',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}
