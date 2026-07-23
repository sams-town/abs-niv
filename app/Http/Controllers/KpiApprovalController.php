<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiSubmission;
use Illuminate\Support\Facades\Auth;

class KpiApprovalController extends Controller
{
    public function index()
    {
        $title = 'Approval KPI Karyawan';
        // Ambil semua KPI yang berstatus Submitted
        $submissions = KpiSubmission::with(['assignment.kpi', 'user', 'period'])
            ->where('status', 'Submitted')
            ->get();
            
        $history = KpiSubmission::with(['assignment.kpi', 'user', 'period'])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->get();

        return view('kpi.approval.index', compact('title', 'submissions', 'history'));
    }

    public function approve($id)
    {
        $submission = KpiSubmission::findOrFail($id);
        $submission->update([
            'status' => 'Approved',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'KPI berhasil disetujui!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'feedback' => 'required|string'
        ]);

        $submission = KpiSubmission::findOrFail($id);
        $submission->update([
            'status' => 'Rejected',
            'feedback' => $request->feedback,
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'KPI ditolak!');
    }
}
