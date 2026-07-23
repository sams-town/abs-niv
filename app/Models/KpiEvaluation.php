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

    // Calculate final score and grade when saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function (KpiEvaluation $evaluation) {
            // Get total quantitative score from kpi targets
            $totalQuantitative = $evaluation->kpiTargets()->sum('calculated_score');
            $totalQualitative = ($evaluation->discipline_score ?? 0) + ($evaluation->initiative_score ?? 0);
            // Assuming qualitative has total weight of 40 (20 each), quantitative 60
            $evaluation->final_score = $totalQuantitative + ($totalQualitative / 2);
            
            // Determine grade
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
