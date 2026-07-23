<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiEvaluation extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'discipline_score',
        'initiative_score',
        'hr_notes',
        'final_score',
        'grade',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kpiTargets(): HasMany
    {
        return $this->hasMany(KpiTarget::class, 'user_id', 'user_id')
            ->where('year', $this->year);
    }

    // Calculate final score and grade when saving (Corporate Logic)
    protected static function boot()
    {
        parent::boot();

        static::saving(function (KpiEvaluation $evaluation) {
            // Get total quantitative score from kpi targets
            $totalQuantitativeRaw = $evaluation->kpiTargets()->sum('calculated_score');
            
            // Normalize quantitative score to 0-70 range (weight 70%)
            $totalQuantitative = min(max($totalQuantitativeRaw, 0), 70);
            
            // Qualitative: 0-30 range (weight 30% - 15% each for discipline & initiative)
            $disciplineScore = min(max($evaluation->discipline_score ?? 0, 0), 100);
            $initiativeScore = min(max($evaluation->initiative_score ?? 0, 0), 100);
            $totalQualitative = ($disciplineScore * 0.15) + ($initiativeScore * 0.15);

            // Final score: 0-100
            $evaluation->final_score = round($totalQuantitative + $totalQualitative, 2);
            
            // Determine grade (Corporate Standard)
            if ($evaluation->final_score >= 85) {
                $evaluation->grade = 'A';
            } elseif ($evaluation->final_score >= 70) {
                $evaluation->grade = 'B';
            } elseif ($evaluation->final_score >= 60) {
                $evaluation->grade = 'C';
            } else {
                $evaluation->grade = 'D';
            }
        });
    }
}
