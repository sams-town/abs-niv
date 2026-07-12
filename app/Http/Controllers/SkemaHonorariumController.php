<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterSkemaHonorarium;

class SkemaHonorariumController extends Controller
{
    public function index()
    {
        $skemas = MasterSkemaHonorarium::orderBy('nama_skema', 'asc')->get();
        return view('skema_honorarium.index', [
            'title' => 'Master Skema Honorarium',
            'skemas' => $skemas
        ]);
    }

    public function create()
    {
        return view('skema_honorarium.create', [
            'title' => 'Tambah Skema Honorarium'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_skema' => 'required|string|max:255',
            'nominal_per_unit' => 'required',
            'deskripsi' => 'nullable|string'
        ]);

        $nominal = $request->nominal_per_unit ? str_replace('.', '', $request->nominal_per_unit) : 0;
        $nominal = str_replace(',', '', $nominal);

        MasterSkemaHonorarium::create([
            'nama_skema' => $request->nama_skema,
            'nominal_per_unit' => $nominal,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect('/skema-honorarium')->with('success', 'Skema Honorarium Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $skema = MasterSkemaHonorarium::findOrFail($id);
        return view('skema_honorarium.edit', [
            'title' => 'Edit Skema Honorarium',
            'skema' => $skema
        ]);
    }

    public function update(Request $request, $id)
    {
        $skema = MasterSkemaHonorarium::findOrFail($id);

        $request->validate([
            'nama_skema' => 'required|string|max:255',
            'nominal_per_unit' => 'required',
            'deskripsi' => 'nullable|string'
        ]);

        $nominal = $request->nominal_per_unit ? str_replace('.', '', $request->nominal_per_unit) : 0;
        $nominal = str_replace(',', '', $nominal);

        $skema->update([
            'nama_skema' => $request->nama_skema,
            'nominal_per_unit' => $nominal,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect('/skema-honorarium')->with('success', 'Skema Honorarium Berhasil Diperbarui');
    }

    public function destroy($id)
    {
        $skema = MasterSkemaHonorarium::findOrFail($id);
        $skema->delete();

        return redirect('/skema-honorarium')->with('success', 'Skema Honorarium Berhasil Dihapus');
    }
}
