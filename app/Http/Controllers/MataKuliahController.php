<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $title = 'Master Mata Kuliah';
        $search = request()->input('search');
        
        $data = MataKuliah::when($search, function ($query) use ($search) {
                        $query->where('kode_mk', 'LIKE', '%'.$search.'%')
                              ->orWhere('nama_mk', 'LIKE', '%'.$search.'%')
                              ->orWhere('prodi', 'LIKE', '%'.$search.'%')
                              ->orWhere('fakultas', 'LIKE', '%'.$search.'%');
                    })
                    ->orderBy('fakultas', 'ASC')
                    ->orderBy('prodi', 'ASC')
                    ->orderBy('nama_mk', 'ASC')
                    ->paginate(10)
                    ->withQueryString();

        return view('mata-kuliah.index', compact(
            'title',
            'data'
        ));
    }

    public function tambah()
    {
        $title = 'Tambah Mata Kuliah';
        return view('mata-kuliah.tambah', compact(
            'title'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
        ]);

        MataKuliah::create($validated);
        return redirect('/mata-kuliah')->with('success', 'Data Mata Kuliah Berhasil Disimpan');
    }

    public function edit($id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $title = 'Edit Mata Kuliah';
        return view('mata-kuliah.edit', compact(
            'title',
            'mataKuliah'
        ));
    }

    public function update(Request $request, $id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $validated = $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk,'.$id,
            'nama_mk' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'fakultas' => 'required|string|max:255',
        ]);

        $mataKuliah->update($validated);
        return redirect('/mata-kuliah')->with('success', 'Data Mata Kuliah Berhasil Diperbarui');
    }

    public function delete($id)
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $mataKuliah->delete();
        return redirect('/mata-kuliah')->with('success', 'Data Mata Kuliah Berhasil Dihapus');
    }
}
