<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'dosen_id',
        'nama_kelas',
        'mata_kuliah',
        'waktu_mulai',
        'waktu_selesai',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function sesiDarings()
    {
        return $this->hasMany(SesiDaring::class, 'jadwal_id');
    }

    public function mataKuliahRelasi()
    {
        return $this->belongsTo(\App\Models\MataKuliah::class, 'mata_kuliah', 'nama_mk');
    }
}
