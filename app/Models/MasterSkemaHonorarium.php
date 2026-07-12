<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSkemaHonorarium extends Model
{
    use HasFactory;

    protected $table = 'master_skema_honorariums';

    protected $fillable = [
        'nama_skema',
        'nominal_per_unit',
        'deskripsi',
    ];

    public function dosens()
    {
        return $this->hasMany(User::class, 'master_skema_honorarium_id');
    }
}
