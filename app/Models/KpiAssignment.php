<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiAssignment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class, 'kpi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(jabatan::class, 'jabatan_id');
    }

    public function submissions()
    {
        return $this->hasMany(KpiSubmission::class, 'kpi_assignment_id');
    }
}
