<?php

namespace App\Http\Controllers;

use App\Models\StatusPajak;
use Illuminate\Http\Request;

class StatusPajakController extends Controller
{
    public function index()
    {
        $title = 'Status Pajak';
        $search = request()->input('search');
        $status_pajak = StatusPajak::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->paginate(10)
        ->withQueryString();

        return view('status-pajak.index', compact(
            'title',
            'status_pajak',
        ));
    }

    public function tambah()
    {
        $title = 'Status Pajak';

        return view('status-pajak.tambah', compact(
            'title',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'ptkp' => 'required',
        ]);

        $validated['ptkp'] = $request->ptkp ? str_replace(',', '', $request->ptkp) : 0;
        StatusPajak::create($validated);

        return redirect('/status-pajak')->with('success', 'Data Berhasil Ditambah');
    }

    public function edit($id)
    {
        $title = 'Status Pajak';
        $pajak = StatusPajak::find($id);

        return view('status-pajak.edit', compact(
            'title',
            'pajak',
        ));
    }

    public function update(Request $request, $id)
    {
        $pajak = StatusPajak::find($id);
        $validated = $request->validate([
            'name' => 'required',
            'ptkp' => 'required',
        ]);

        $validated['ptkp'] = $request->ptkp ? str_replace(',', '', $request->ptkp) : 0;
        $pajak->update($validated);

        return redirect('/status-pajak')->with('success', 'Data Berhasil Diupdate');
    }

    public function delete($id)
    {
        $pajak = StatusPajak::find($id);
        $pajak->delete();
        return back()->with('success', 'Data Berhasil Dihapus');
    }

}
