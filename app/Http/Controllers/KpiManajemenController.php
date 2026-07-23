<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kpi;
use App\Models\KpiCategory;
use App\Models\KpiAssignment;
use App\Models\User;
use App\Models\jabatan;

class KpiManajemenController extends Controller
{
    public function index()
    {
        $title = 'Manajemen Kamus KPI';
        $kpis = Kpi::with('category')->get();
        $categories = KpiCategory::all();
        return view('kpi.manajemen.index', compact('title', 'kpis', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:kpi_categories,id',
            'name' => 'required',
            'unit' => 'required',
            'type' => 'required|in:Higher is Better,Lower is Better',
            'target_value' => 'required|numeric',
        ]);

        Kpi::create($request->all());
        return redirect()->back()->with('success', 'Kamus KPI berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:kpi_categories,id',
            'name' => 'required',
            'unit' => 'required',
            'type' => 'required|in:Higher is Better,Lower is Better',
            'target_value' => 'required|numeric',
        ]);

        $kpi = Kpi::findOrFail($id);
        $kpi->update($request->all());
        return redirect()->back()->with('success', 'Kamus KPI berhasil diupdate!');
    }

    public function delete($id)
    {
        Kpi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kamus KPI berhasil dihapus!');
    }

    // Assignment Logic
    public function assignIndex($kpi_id)
    {
        $kpi = Kpi::findOrFail($kpi_id);
        $title = 'Assign KPI: ' . $kpi->name;
        $assignments = KpiAssignment::with(['user', 'jabatan'])->where('kpi_id', $kpi_id)->get();
        $users = User::all();
        $jabatans = jabatan::all();

        return view('kpi.manajemen.assign', compact('title', 'kpi', 'assignments', 'users', 'jabatans'));
    }

    public function assignStore(Request $request, $kpi_id)
    {
        $request->validate([
            'assign_type' => 'required|in:user,jabatan',
            'weight' => 'required|numeric|min:0.01|max:100',
        ]);

        $data = [
            'kpi_id' => $kpi_id,
            'weight' => $request->weight
        ];

        if ($request->assign_type == 'user') {
            $request->validate(['user_id' => 'required|exists:users,id']);
            $data['user_id'] = $request->user_id;
        } else {
            $request->validate(['jabatan_id' => 'required|exists:jabatans,id']);
            $data['jabatan_id'] = $request->jabatan_id;
        }

        KpiAssignment::create($data);
        return redirect()->back()->with('success', 'Assignment KPI berhasil ditambahkan!');
    }

    public function assignDelete($assignment_id)
    {
        KpiAssignment::findOrFail($assignment_id)->delete();
        return redirect()->back()->with('success', 'Assignment KPI berhasil dihapus!');
    }
}
