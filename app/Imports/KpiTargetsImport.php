<?php

namespace App\Imports;

use App\Models\KpiTarget;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
class KpiTargetsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find user by email or nip or name (fallback)
        $user = User::where('email', $row['email'] ?? null)
            ->orWhere('nip', $row['nip'] ?? null)
            ->orWhere('name', $row['nama_lengkap'] ?? $row['name'] ?? null)
            ->first();

        if (!$user) {
            return null;
        }

        // Normalize input values
        $targetValue = (float) ($row['target_value'] ?? $row['target'] ?? 0);
        $weight = (float) ($row['weight'] ?? $row['bobot'] ?? 0);
        $realizationSource = $row['realization_value'] ?? $row['realisasi'] ?? null;
        $realizationValue = $realizationSource === null || $realizationSource === ''
            ? null
            : (float) $realizationSource;

        // Calculate calculated score automatically
        $calculatedScore = null;
        if ($targetValue > 0 && $realizationValue !== null) {
            $calculatedScore = ($realizationValue / $targetValue) * $weight;
        }

        // Create or update KpiTarget
        return KpiTarget::updateOrCreate(
            [
                'user_id' => $user->id,
                'year' => $this->year,
                'indicator_name' => $row['indicator_name'] ?? $row['indikator'] ?? 'Untitled Indicator',
            ],
            [
                'target_value' => $targetValue,
                'realization_value' => $realizationValue,
                'weight' => $weight,
                'calculated_score' => $calculatedScore,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'email' => 'nullable|string|email',
            'nip' => 'nullable|string',
            'nama_lengkap' => 'nullable|string',
            'indicator_name' => 'required|string|max:255',
            'target_value' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0|max:100',
            'realization_value' => 'nullable|numeric|min:0',
        ];
    }
}
