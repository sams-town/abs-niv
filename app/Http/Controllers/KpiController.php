<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KpiTarget;
use App\Models\KpiEvaluation;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->get('year', date('Y')));

        $pegawai = User::pegawaiDanDosen()
            ->with([
                'Jabatan',
                'Lokasi',
                'kpiEvaluation' => function ($query) use ($year) {
                    $query->where('year', $year);
                },
            ])
            ->withCount([
                'kpiTargets as imported_targets_count' => function ($query) use ($year) {
                    $query->where('year', $year);
                },
            ])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalPegawai = User::pegawaiDanDosen()->count();
        $sudahDinilai = KpiEvaluation::where('year', $year)
            ->where('status', 'finalized')
            ->distinct('user_id')
            ->count('user_id');
        $belumDinilai = max($totalPegawai - $sudahDinilai, 0);

        return view('kpi.index', [
            'title' => 'Manajemen Penilaian KPI',
            'year' => $year,
            'pegawai' => $pegawai,
            'totalPegawai' => $totalPegawai,
            'sudahDinilai' => $sudahDinilai,
            'belumDinilai' => $belumDinilai,
        ]);
    }

    // Show KPI evaluation form for a user
    public function showEvaluationForm($userId, $year = null)
    {
        $year = $year ?? request('year', date('Y'));
        $user = User::findOrFail($userId);
        
        // Get or create KPI targets for the user
        $kpiTargets = KpiTarget::where('user_id', $userId)
            ->where('year', $year)
            ->get();
        
        // Get or create KPI evaluation
        $evaluation = KpiEvaluation::firstOrCreate(
            ['user_id' => $userId, 'year' => $year],
            [
                'discipline_score' => 0,
                'initiative_score' => 0,
                'status' => 'draft'
            ]
        );

        return view('kpi.evaluation', [
            'title' => 'Penilaian KPI',
            'user' => $user,
            'year' => $year,
            'kpiTargets' => $kpiTargets,
            'evaluation' => $evaluation
        ]);
    }

    // Update KPI target realization
    public function updateTarget(Request $request, $targetId)
    {
        $target = KpiTarget::findOrFail($targetId);
        
        $request->validate([
            'realization_value' => 'required|numeric|min:0'
        ]);

        $target->update([
            'realization_value' => $request->realization_value
        ]);

        Alert::success('Berhasil', 'Realisasi berhasil diperbarui!');
        return back();
    }

    // Save KPI evaluation (HR assessment)
    public function saveEvaluation(Request $request, $evaluationId)
    {
        $evaluation = KpiEvaluation::findOrFail($evaluationId);
        
        $request->validate([
            'discipline_score' => 'required|numeric|min:0|max:100',
            'initiative_score' => 'required|numeric|min:0|max:100',
            'hr_notes' => 'nullable|string',
            'status' => 'required|in:draft,submitted,approved,finalized'
        ]);

        $evaluation->update($request->only([
            'discipline_score',
            'initiative_score',
            'hr_notes',
            'status'
        ]));

        Alert::success('Berhasil', 'Penilaian KPI berhasil disimpan!');
        return back();
    }

    // Add KPI target
    public function addTarget(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020',
            'indicator_name' => 'required|string|max:255',
            'target_value' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0|max:100'
        ]);

        KpiTarget::create($request->all());

        Alert::success('Berhasil', 'Target KPI berhasil ditambahkan!');
        return back();
    }
}
