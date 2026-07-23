<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPeriod extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function submissions()
    {
        return $this->hasMany(KpiSubmission::class, 'period_id');
    }
}
