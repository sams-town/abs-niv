<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(KpiCategory::class, 'category_id');
    }

    public function assignments()
    {
        return $this->hasMany(KpiAssignment::class, 'kpi_id');
    }
}
