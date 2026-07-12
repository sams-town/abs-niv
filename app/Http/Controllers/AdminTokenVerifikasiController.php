<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\SesiDaring;
use Illuminate\Http\Request;

class AdminTokenVerifikasiController extends Controller
{
    /**
     * Tampilkan daftar laporan mengajar yang menunggu verifikasi admin.
     * Status 'valid' artinya dosen sudah input token & cocok, tapi admin belum review.
     * Status 'pending' artinya dosen belum input token.
     * Kita tampilkan semua yang status_pembayaran = 'valid' atau 'pending' untuk admin.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'valid');
        $search = $request->input('search');

        $laporan = LaporanMengajar::with([
                'dosen',
                'sesiDaring.jadwal',
            ])
            ->when($status !== 'semua', fn($q) => $q->where('status_pembayaran', $status))
            ->when($search, fn($q) => $q->whereHas('dosen', fn($q2) =>
                $q2->where('name', 'LIKE', "%{$search}%")
            ))
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.token-verifikasi.index', [
            'title'   => 'Verifikasi Token Daring',
            'laporan' => $laporan,
            'status'  => $status,
            'search'  => $search,
        ]);
    }

    /**
     * Admin approve — konfirmasi pembayaran sudah diverifikasi secara manual.
     */
    public function approve($id)
    {
        $laporan = LaporanMengajar::findOrFail($id);

        if (!in_array($laporan->status_pembayaran, ['valid', 'pending'])) {
            return redirect()->back()->with('error', 'Status laporan tidak dapat diubah.');
        }

        $laporan->update([
            'status_pembayaran' => 'approved',
            'catatan_sistem'    => ($laporan->catatan_sistem ?? '') . "\nDisetujui oleh " . auth()->user()->name . " pada " . now()->format('d/m/Y H:i'),
        ]);

        return redirect()->back()->with('success', 'Token berhasil disetujui.');
    }

    /**
     * Admin reject — tolak laporan.
     */
    public function reject($id)
    {
        $laporan = LaporanMengajar::findOrFail($id);

        $laporan->update([
            'status_pembayaran' => 'invalid',
            'catatan_sistem'    => ($laporan->catatan_sistem ?? '') . "\nDitolak oleh " . auth()->user()->name . " pada " . now()->format('d/m/Y H:i'),
        ]);

        return redirect()->back()->with('success', 'Token berhasil ditolak.');
    }

    /**
     * Generate token baru secara acak untuk sesi daring tertentu (admin reset token).
     */
    public function regenerateToken($sesiId)
    {
        $sesi = SesiDaring::findOrFail($sesiId);
        $newToken = 'TOK-' . strtoupper(\Illuminate\Support\Str::random(6));
        $sesi->update(['token_daring' => $newToken]);

        return redirect()->back()->with('success', 'Token baru berhasil dibuat: ' . $newToken);
    }
}
