<?php

namespace App\Http\Controllers;

use App\Models\MasterSkemaHonorarium;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkemaHonorariumController extends Controller
{
    /**
     * Tampilkan daftar konfigurasi tarif per dosen.
     */
    public function index()
    {
        $dosens = User::dosen()
            ->where('status_aktif', true)
            ->with('masterSkemaHonorarium')
            ->orderBy('name')
            ->get();

        $skemas = MasterSkemaHonorarium::orderBy('nama_skema')->get();

        return view('skema_honorarium.index', [
            'title'  => 'Master Tarif Mengajar',
            'dosens' => $dosens,
            'skemas' => $skemas,
        ]);
    }

    /**
     * Simpan konfigurasi tarif untuk dosen tertentu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_id'          => 'required|exists:users,id',
            'status_kepegawaian' => 'required|string',
            'gaji_pokok'        => 'nullable|numeric|min:0',
            'tunjangan'         => 'nullable|numeric|min:0',
            'tarif_daring'      => 'nullable|numeric|min:0',
            'tarif_luring'      => 'nullable|numeric|min:0',
            'is_aktif'          => 'nullable',
        ]);

        $dosen = User::findOrFail($request->dosen_id);

        // Buat atau update skema khusus untuk dosen ini
        $namaSKema = $request->status_kepegawaian . ' - ' . $dosen->name;
        $tarif = $request->tarif_daring ?? $request->tarif_luring ?? 0;

        $skema = MasterSkemaHonorarium::updateOrCreate(
            ['nama_skema' => $namaSKema],
            [
                'nominal_per_unit' => $tarif,
                'deskripsi' => 'Tarif Daring: Rp ' . number_format($request->tarif_daring ?? 0, 0, ',', '.')
                             . ' | Tarif Luring: Rp ' . number_format($request->tarif_luring ?? 0, 0, ',', '.'),
            ]
        );

        // Update user
        $dosen->update([
            'status_kepegawaian'         => $request->status_kepegawaian,
            'nominal_honor'              => $request->tarif_daring ?? 0,
            'master_skema_honorarium_id' => $skema->id,
        ]);

        return redirect('/skema-honorarium')->with('success', 'Konfigurasi tarif berhasil disimpan.');
    }

    /**
     * Update konfigurasi dosen (via modal edit).
     */
    public function update(Request $request, $id)
    {
        $dosen = User::findOrFail($id);

        $request->validate([
            'status_kepegawaian' => 'required|string',
            'gaji_pokok'        => 'nullable|numeric|min:0',
            'tunjangan'         => 'nullable|numeric|min:0',
            'tarif_daring'      => 'nullable|numeric|min:0',
            'tarif_luring'      => 'nullable|numeric|min:0',
        ]);

        $tarif = $request->tarif_daring ?? $request->tarif_luring ?? 0;

        if ($dosen->masterSkemaHonorarium) {
            $dosen->masterSkemaHonorarium->update([
                'nominal_per_unit' => $tarif,
                'deskripsi' => 'Tarif Daring: Rp ' . number_format($request->tarif_daring ?? 0, 0, ',', '.')
                             . ' | Tarif Luring: Rp ' . number_format($request->tarif_luring ?? 0, 0, ',', '.'),
            ]);
        }

        $dosen->update([
            'status_kepegawaian' => $request->status_kepegawaian,
            'nominal_honor'      => $tarif,
        ]);

        return redirect('/skema-honorarium')->with('success', 'Konfigurasi tarif berhasil diperbarui.');
    }

    /**
     * Legacy: untuk backward compat route resource.
     */
    public function create() { return redirect('/skema-honorarium'); }
    public function edit($id) { return redirect('/skema-honorarium'); }
    public function destroy($id)
    {
        $dosen = User::find($id);
        if ($dosen) {
            $dosen->update(['master_skema_honorarium_id' => null, 'nominal_honor' => 0, 'status_kepegawaian' => null]);
        }
        return redirect('/skema-honorarium')->with('success', 'Konfigurasi tarif dihapus.');
    }
}
