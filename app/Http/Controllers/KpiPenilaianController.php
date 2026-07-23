<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiAssignment;
use App\Models\KpiSubmission;
use App\Models\KpiPeriod;
use Illuminate\Support\Facades\Auth;

class KpiPenilaianController extends Controller
{
    public function index()
    {
        $title = 'Penilaian KPI';
        $user = Auth::user();
        
        // Find active period
        $activePeriod = KpiPeriod::where('is_active', true)->first();
        
        // Get all KPIs assigned directly to user OR to user's jabatan
        $assignments = KpiAssignment::with(['kpi.category', 'submissions' => function($q) use ($activePeriod) {
            if($activePeriod) {
                $q->where('period_id', $activePeriod->id);
            }
        }])
        ->where('user_id', $user->id)
        ->orWhere('jabatan_id', $user->jabatan_id)
        ->get();

        return view('kpi.penilaian.index', compact('title', 'assignments', 'activePeriod'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'kpi_assignment_id' => 'required|exists:kpi_assignments,id',
            'period_id' => 'required|exists:kpi_periods,id',
            'actual_value' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $assignment = KpiAssignment::with('kpi')->findOrFail($request->kpi_assignment_id);
        
        // Calculate score
        $target = $assignment->kpi->target_value;
        $actual = $request->actual_value;
        $weight = $assignment->weight;
        $type = $assignment->kpi->type;

        $score = 0;
        if ($target != 0) {
            if ($type == 'Higher is Better') {
                $achievement = ($actual / $target) * 100;
            } else {
                $achievement = ($target / $actual) * 100;
            }
            // Cap achievement at max 120% maybe? Standard cap is often 100 or 120
            if($achievement > 120) $achievement = 120;
            
            $score = ($achievement * $weight) / 100;
        }

        $submission = KpiSubmission::updateOrCreate(
            [
                'kpi_assignment_id' => $assignment->id,
                'period_id' => $request->period_id,
                'user_id' => Auth::id()
            ],
            [
                'actual_value' => $actual,
                'score' => $score,
                'notes' => $request->notes,
                'status' => 'Submitted',
                'submitted_at' => now(),
            ]
        );

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('kpi_attachments', 'public');
            $submission->update(['attachment' => $path]);
        }

        return redirect()->back()->with('success', 'Pencapaian KPI berhasil disubmit!');
    }
}
