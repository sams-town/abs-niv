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
            if ($target->target_value > 0 && $target->realization_value !== null) {
                $target->calculated_score = ($target->realization_value / $target->target_value) * $target->weight;
            } else {
                $target->calculated_score = 0;
            }
        });
    }
}
