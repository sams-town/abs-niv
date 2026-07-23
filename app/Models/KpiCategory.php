<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function kpis()
    {
        return $this->hasMany(Kpi::class, 'category_id');
    }
}
