<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KpiTarget;
use App\Models\KpiEvaluation;
use App\Models\Jabatan;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KpiTargetsImport;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $year = (int) ($request->get('year', date('Y')));
            $search = $request->get('search', '');
            $jabatanId = $request->get('jabatan_id', '');
            $lokasiId = $request->get('lokasi_id', '');

            // Build query with eager loading
            $query = User::pegawaiDanDosen()
                ->with([
                    'Jabatan',
                    'Lokasi',
                    'kpiEvaluation' => function ($q) use ($year) {
                        $q->where('year', $year);
                    },
                ])
                ->withCount([
                    'kpiTargets as imported_targets_count' => function ($q) use ($year) {
                        $q->where('year', $year);
                    },
                ]);

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                });
            }

            // Apply jabatan filter
            if ($jabatanId) {
                $query->where('jabatan_id', $jabatanId);
            }

            // Apply lokasi filter
            if ($lokasiId) {
                $query->where('lokasi_id', $lokasiId);
            }

            // Get paginated data
            $pegawai = $query->orderBy('name')->paginate(10)->withQueryString();

            // Calculate statistics
            $totalPegawai = User::pegawaiDanDosen()->count();
            
            // Get evaluations for current year
            $evaluations = KpiEvaluation::with('user')->where('year', $year)->get();
            $sudahDinilai = $evaluations->where('status', 'finalized')->count();
            $belumDinilai = max($totalPegawai - $sudahDinilai, 0);

            // Calculate average score and highest performer
            $averageScore = $evaluations->whereNotNull('final_score')->avg('final_score') ?? 0;
            $highestPerformer = $evaluations->where('status', 'finalized')->sortByDesc('final_score')->first();

            // Get data for charts
            $gradeDistribution = [
                'A' => $evaluations->where('grade', 'A')->count(),
                'B' => $evaluations->where('grade', 'B')->count(),
                'C' => $evaluations->where('grade', 'C')->count(),
                'D' => $evaluations->where('grade', 'D')->count(),
            ];

            // Get jabatan and lokasi for filter dropdowns
            $jabatanList = Jabatan::orderBy('nama_jabatan')->get();
            $lokasiList = Lokasi::orderBy('nama_lokasi')->get();

            return view('kpi.index', [
                'title' => 'Manajemen KPI Korporat',
                'year' => $year,
                'pegawai' => $pegawai,
                'totalPegawai' => $totalPegawai,
                'sudahDinilai' => $sudahDinilai,
                'belumDinilai' => $belumDinilai,
                'averageScore' => $averageScore,
                'highestPerformer' => $highestPerformer,
                'gradeDistribution' => $gradeDistribution,
                'jabatanList' => $jabatanList,
                'lokasiList' => $lokasiList,
                'search' => $search,
                'jabatanId' => $jabatanId,
                'lokasiId' => $lokasiId,
            ]);
        } catch (\Exception $e) {
            // Jika terjadi error, tampilkan pesan error dan redirect kembali
            Alert::error('Error!', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect('/dashboard');
        }
    }

    // Import KPI Targets
    public function importTargets(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->is_admin === 'admin', 403);

        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            'year' => 'required|integer|min:2020',
        ]);

        try {
            Excel::import(new KpiTargetsImport((int) $request->year), $request->file('file_excel'));

            Alert::success('Berhasil!', 'Target KPI berhasil diimport secara massal!');
            return back();
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Terjadi kesalahan saat import: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    // Show KPI evaluation form for a user (handles both /evaluation/{id} and /evaluation/{userId}/{year?})
    public function showEvaluationForm($param1, $param2 = null)
    {
        $year = date('Y');
        $user = null;
        $evaluation = null;
        
        // Coba cari user berdasarkan param1 terlebih dahulu
        $user = User::find($param1);
        
        if ($user) {
            // Param1 adalah userId
            $year = $param2 ?? request('year', date('Y'));
            
            // Get or create KPI evaluation
            $evaluation = KpiEvaluation::firstOrCreate(
                ['user_id' => $user->id, 'year' => $year],
                [
                    'discipline_score' => 0,
                    'initiative_score' => 0,
                    'status' => 'draft',
                ]
            );
        } else {
            // Coba cari evaluation berdasarkan param1 (evaluationId)
            $evaluation = KpiEvaluation::findOrFail($param1);
            $user = $evaluation->user;
            $year = $evaluation->year;
        }
        
        // Get KPI targets for the user
        $kpiTargets = KpiTarget::where('user_id', $user->id)
            ->where('year', $year)
            ->get();

        return view('kpi.evaluation', [
            'title' => 'Penilaian KPI - ' . ($user->name ?? 'Pegawai'),
            'user' => $user,
            'year' => $year,
            'kpiTargets' => $kpiTargets,
            'evaluation' => $evaluation,
        ]);
    }

    // Update KPI target realization
    public function updateTarget(Request $request, $targetId)
    {
        $target = KpiTarget::findOrFail($targetId);
        
        $request->validate([
            'realization_value' => 'required|numeric|min:0',
        ]);

        $target->update([
            'realization_value' => $request->realization_value,
        ]);

        Alert::success('Berhasil!', 'Realisasi target berhasil diperbarui!');
        return back();
    }

    // Save KPI evaluation (HR assessment)
    public function saveEvaluation(Request $request, $evaluationId)
    {
        $evaluation = KpiEvaluation::findOrFail($evaluationId);
        
        $request->validate([
            'discipline_score' => 'required|numeric|min:0|max:100',
            'initiative_score' => 'required|numeric|min:0|max:100',
            'hr_notes' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,submitted,approved,finalized',
        ]);

        $evaluation->update($request->only([
            'discipline_score',
            'initiative_score',
            'hr_notes',
            'status',
        ]));

        Alert::success('Berhasil!', 'Penilaian KPI berhasil disimpan!');
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
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        KpiTarget::create($request->all());

        Alert::success('Berhasil!', 'Target KPI berhasil ditambahkan!');
        return back();
    }
}
