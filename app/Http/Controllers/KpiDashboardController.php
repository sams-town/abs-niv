<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiSubmission;
use App\Models\KpiPeriod;
use Illuminate\Support\Facades\Auth;

class KpiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Dashboard KPI Corporate';
        
        $periods = KpiPeriod::orderBy('start_date', 'desc')->get();
        
        $selectedPeriodId = $request->period_id ?? KpiPeriod::where('is_active', true)->value('id');
        
        // Overview of Company Performance (Average score of all approved KPIs in period)
        $averageScore = 0;
        $totalSubmissions = 0;
        
        if ($selectedPeriodId) {
            $averageScore = KpiSubmission::where('period_id', $selectedPeriodId)
                                         ->where('status', 'Approved')
                                         ->sum('score'); // Total weighted score
            
            // To get average, we sum all scores and divide by number of unique users who submitted
            $totalUsers = KpiSubmission::where('period_id', $selectedPeriodId)
                                       ->where('status', 'Approved')
                                       ->distinct('user_id')
                                       ->count('user_id');
                                       
            if ($totalUsers > 0) {
                $averageScore = $averageScore / $totalUsers; // Assuming total weights per user is 100
            }
            
            $totalSubmissions = KpiSubmission::where('period_id', $selectedPeriodId)->count();
        }

        // Leaderboard (Top Performers)
        $leaderboard = [];
        if ($selectedPeriodId) {
            $topPerformers = KpiSubmission::with('user')
                                ->where('period_id', $selectedPeriodId)
                                ->where('status', 'Approved')
                                ->selectRaw('user_id, SUM(score) as total_score')
                                ->groupBy('user_id')
                                ->orderByDesc('total_score')
                                ->take(5)
                                ->get();
                                
            $leaderboard = $topPerformers;
        }

        return view('kpi.dashboard.index', compact('title', 'periods', 'selectedPeriodId', 'averageScore', 'totalSubmissions', 'leaderboard'));
    }
}
