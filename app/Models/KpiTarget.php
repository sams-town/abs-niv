<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiTarget extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'indicator_name',
        'target_value',
        'realization_value',
        'weight',
        'calculated_score'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Automatically calculate calculated_score when saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function (KpiTarget $target) {
            // Pastikan target_value dan weight tersedia dan valid
            $targetValue = (float) ($target->target_value ?? 0);
            $weight = (float) ($target->weight ?? 0);
            $realizationValue = $target->realization_value;

            if ($targetValue > 0 && $realizationValue !== null && is_numeric($realizationValue)) {
                $target->calculated_score = ((float) $realizationValue / $targetValue) * $weight;
            } else {
                $target->calculated_score = 0;
            }
        });
    }
}
