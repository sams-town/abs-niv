<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\SesiDaring;
use App\Services\SesiDaringService;
use Exception;

class SesiDaringController extends Controller
{
    protected $sesiDaringService;

    public function __construct(SesiDaringService $sesiDaringService)
    {
        $this->sesiDaringService = $sesiDaringService;
    }

    /**
     * Show form to add Sesi Daring.
     */
    public function create($jadwalId)
    {
        $jadwal = Jadwal::findOrFail($jadwalId);
        return view('sesi_daring.create', [
            'title' => 'Tambah Sesi Daring',
            'jadwal' => $jadwal
        ]);
    }

    /**
     * Store new Sesi Daring with strict URL validation.
     */
    public function store(Request $request)
    {
        // URL validation regex to ensure a completely valid protocol and domain
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'meeting_url' => [
                'required',
                'string',
                'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
            ],
            'meeting_id' => 'required|string|max:255',
            'passcode' => 'required|string|max:255',
            'catatan' => 'nullable|string'
        ], [
            'meeting_url.regex' => 'Meeting URL harus berupa alamat URL yang valid (menggunakan http/https).',
        ]);

        SesiDaring::create([
            'jadwal_id' => $request->jadwal_id,
            'meeting_url' => $request->meeting_url,
            'meeting_id' => $request->meeting_id,
            'passcode' => $request->passcode,
            'status_sesi' => 'scheduled',
            'catatan' => $request->catatan,
        ]);

        return redirect('/dosen')->with('success', 'Sesi Daring berhasil dijadwalkan.');
    }

    /**
     * Start live session.
     */
    public function start($id)
    {
        try {
            $this->sesiDaringService->startLiveSession($id, auth()->id());
            return redirect()->back()->with('success', 'Sesi Daring telah dimulai (LIVE).');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * End live session.
     */
    public function end($id)
    {
        try {
            $result = $this->sesiDaringService->endLiveSession($id);
            if ($result['status_pembayaran'] === 'invalid') {
                return redirect()->back()->with('warning', $result['catatan_sistem']);
            }
            return redirect()->back()->with('success', 'Sesi Daring berhasil diselesaikan. Penggajian dihitung.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
