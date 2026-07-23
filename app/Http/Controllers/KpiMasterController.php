<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KpiCategory;
use App\Models\KpiPeriod;
use Illuminate\Support\Facades\DB;

class KpiMasterController extends Controller
{
    // Kategori
    public function indexKategori()
    {
        $title = 'Master Kategori KPI';
        $kategori = KpiCategory::all();
        return view('kpi.master.kategori', compact('title', 'kategori'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        KpiCategory::create($request->all());
        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function updateKategori(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'weight' => 'required|numeric|min:0|max:100',
        ]);

        $kategori = KpiCategory::findOrFail($id);
        $kategori->update($request->all());
        return redirect()->back()->with('success', 'Kategori berhasil diupdate!');
    }

    public function deleteKategori($id)
    {
        KpiCategory::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }

    // Periode
    public function indexPeriode()
    {
        $title = 'Master Periode KPI';
        $periode = KpiPeriod::all();
        return view('kpi.master.periode', compact('title', 'periode'));
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        KpiPeriod::create($request->all());
        return redirect()->back()->with('success', 'Periode berhasil ditambahkan!');
    }

    public function updatePeriode(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        $periode = KpiPeriod::findOrFail($id);
        $periode->update($request->all());
        return redirect()->back()->with('success', 'Periode berhasil diupdate!');
    }

    public function deletePeriode($id)
    {
        KpiPeriod::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Periode berhasil dihapus!');
    }
}
