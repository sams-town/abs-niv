<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\MappingShift;
use App\Models\dinasLuar;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class ShiftController extends Controller
{
    public function index()
    {
        try {
            @ini_set('memory_limit', '512M');
            Carbon::setLocale('id');

            $search = request()->input('search');
            $today  = now()->format('Y-m-d');
            $cutoff = now()->subDays(14)->format('Y-m-d');

            $shiftsQuery = Shift::when($search, function ($q) use ($search) {
                return $q->where('nama_shift', 'LIKE', "%{$search}%");
            })->orderBy('nama_shift');

            $shifts = $shiftsQuery->get();
            $totalShift = $shifts->count();
            $shiftIds = $shifts->modelKeys();

            try {
                if (empty($shiftIds)) {
                    $allMappings = collect();
                } else {
                    $allMappings = MappingShift::query()
                        ->select(['id', 'shift_id', 'user_id', 'tanggal', 'lock_location'])
                        ->whereIn('shift_id', $shiftIds)
                        ->where('tanggal', '>=', $cutoff)
                        ->orderBy('tanggal', 'asc')
                        ->get();
                }
            } catch (\Throwable $e) {
                $allMappings = collect();
            }

            $userIdsNeeded = $allMappings->pluck('user_id')->unique()->filter()->values()->all();
            $usersById = [];
            if (!empty($userIdsNeeded)) {
                try {
                    $users = User::with(['Jabatan:id,nama_jabatan'])
                        ->whereIn('id', $userIdsNeeded)
                        ->get(['id', 'name', 'tipe_user', 'jabatan_id']);
                    foreach ($users as $u) $usersById[$u->id] = $u;
                } catch (\Throwable $e) { $usersById = []; }
            }

            $groupedByShift = [];
            foreach ($allMappings as $m) {
                $sid = intval($m->shift_id);
                $uid = intval($m->user_id);
                if ($sid <= 0 || $uid <= 0) continue;
                if (!isset($groupedByShift[$sid])) $groupedByShift[$sid] = [];
                if (!isset($groupedByShift[$sid][$uid])) {
                    $groupedByShift[$sid][$uid] = [
                        'uid'           => $uid,
                        'dates'         => [],
                        'lock_location' => intval($m->lock_location),
                        'mapping_ids'   => [],
                    ];
                }
                if (!empty($m->tanggal)) $groupedByShift[$sid][$uid]['dates'][] = $m->tanggal;
                if (!empty($m->id))      $groupedByShift[$sid][$uid]['mapping_ids'][] = $m->id;
            }

            foreach ($shifts as $shift) {
                $assigned = [];
                $perShift = $groupedByShift[$shift->id] ?? [];
                foreach ($perShift as $g) {
                    $uid = intval($g['uid']);
                    if (!isset($usersById[$uid])) continue;
                    $dates = array_values(array_unique(array_filter($g['dates'] ?? [])));
                    $dateRangeStr = '';
                    if (!empty($dates)) {
                        sort($dates);
                        $ranges = [];
                        $startStr = $dates[0];
                        $prevStr  = $dates[0];
                        $startT = strtotime($startStr);
                        $prevT  = $startT;
                        for ($i = 1; $i < count($dates); $i++) {
                            $currT = strtotime($dates[$i]);
                            if (($currT - $prevT) > 86400) {
                                $ranges[] = $this->dateRangeStrSafe($startStr, $prevStr);
                                $startStr = $dates[$i];
                                $startT   = $currT;
                            }
                            $prevStr = $dates[$i];
                            $prevT   = $currT;
                        }
                        $ranges[] = $this->dateRangeStrSafe($startStr, $prevStr);
                        $dateRangeStr = implode(', ', array_filter($ranges));
                    }
                    $assigned[] = [
                        'user'          => $usersById[$uid],
                        'range'         => $dateRangeStr,
                        'lock_location' => intval($g['lock_location'] ?? 0),
                        'mapping_ids'   => empty($g['mapping_ids']) ? '' : implode(',', array_map('strval', $g['mapping_ids'])),
                    ];
                }
                $shift->assigned_employees = $assigned;
            }

            try {
                $karyawanAktif = User::pegawaiDanDosen()->where(function ($q) use ($today) {
                    $q->whereNull('masa_berlaku')->orWhere('masa_berlaku', '>', $today);
                })->count();
            } catch (\Throwable $e) { $karyawanAktif = 180; }

            try {
                $jadwalTerjadwal = MappingShift::whereNotNull('shift_id')->count();
            } catch (\Throwable $e) { $jadwalTerjadwal = 0; }

            try {
                $allUsers = User::pegawaiDanDosen()->orderBy('name')->get(['id', 'name', 'tipe_user']);
            } catch (\Throwable $e) { $allUsers = collect(); }

            return view('shift.index', [
                'title'            => 'Shift',
                'shifts'           => $shifts,
                'total_shift'      => $totalShift,
                'karyawan_aktif'   => $karyawanAktif,
                'jadwal_terjadwal' => $jadwalTerjadwal,
                'all_users'        => $allUsers,
            ]);
        } catch (\Throwable $e) {
            logger()->error('Shift index fatal error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('<h2>Terjadi kesalahan saat memuat halaman Shift.</h2>'
                . '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>'
                . '<br><a href="' . url('/dashboard') . '">Kembali ke Dashboard</a>', 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }
    }

    private function dateRangeStrSafe($startStr, $endStr): string
    {
        try {
            $start = Carbon::parse($startStr);
            $end   = Carbon::parse($endStr);
            Carbon::setLocale('id');
            if ($start->equalTo($end)) return $start->translatedFormat('d M y');
            if ($start->month === $end->month && $start->year === $end->year) {
                return $start->translatedFormat('d') . ' - ' . $end->translatedFormat('d M y');
            }
            return $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M y');
        } catch (\Throwable $e) {
            return (string)$startStr . ($startStr === $endStr ? '' : ' s/d ' . $endStr);
        }
    }

    private function dateRangeSafe($start, $end): string
    {
        try {
            if (!($start instanceof Carbon)) $start = Carbon::parse($start);
            if (!($end instanceof Carbon))   $end   = Carbon::parse($end);
            return $this->dateRangeStrSafe($start->toDateString(), $end->toDateString());
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function dateRange(Carbon $start, Carbon $end): string
    {
        return $this->dateRangeSafe($start, $end);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'shift_id'      => 'required|integer',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        $userIds = $request->user_ids ?? ($request->user_id ? [$request->user_id] : []);
        $userIds = array_values(array_unique(array_filter(array_map('intval', (array)$userIds), function ($v) { return $v > 0; })));

        if (empty($userIds)) {
            return redirect('/shift')->with('error', 'Pilih minimal satu pegawai.');
        }

        try {
            $start = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $end   = Carbon::parse($request->tanggal_akhir)->startOfDay();
        } catch (\Throwable $e) {
            return redirect('/shift')->with('error', 'Format tanggal tidak valid.');
        }
        if ($end->lt($start)) {
            return redirect('/shift')->with('error', 'Tanggal akhir tidak boleh kurang dari tanggal mulai.');
        }

        $maxDays = 92;
        if ($start->diffInDays($end) + 1 > $maxDays) {
            return redirect('/shift')->with('error', "Maksimal {$maxDays} hari sekaligus (kurangi rentang tanggal).");
        }

        $shiftId       = intval($request->shift_id);
        $lockLocation  = intval($request->lock_location ?? 0);
        $now           = now()->toDateTimeString();

        $datesArr = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $datesArr[] = $cursor->toDateString();
            $cursor->addDay();
        }
        unset($cursor);

        if (empty($datesArr)) {
            return redirect('/shift')->with('error', 'Tidak ada tanggal di rentang yang dipilih.');
        }

        try {
            \DB::beginTransaction();
            $totalUsers = count($userIds);
            $totalDates = count($datesArr);
            $batchSize  = 500;
            $allRows    = [];
            foreach ($userIds as $uid) {
                foreach ($datesArr as $d) {
                    $allRows[] = [
                        'user_id'       => $uid,
                        'shift_id'      => $shiftId,
                        'tanggal'       => $d,
                        'lock_location' => $lockLocation,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }
            }
            unset($userIds, $datesArr);

            foreach (array_chunk($allRows, $batchSize) as $chunk) {
                \DB::table('mapping_shifts')->upsert(
                    $chunk,
                    ['user_id', 'tanggal'],
                    ['shift_id', 'lock_location', 'updated_at']
                );
            }
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            logger()->error('Shift assign bulk error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect('/shift')->with('error', 'Gagal menyimpan penugasan: ' . $e->getMessage());
        }

        return redirect('/shift')->with('success', "Penugasan Shift Berhasil ({$totalUsers} pegawai × {$totalDates} hari).");
    }

    public function deleteAssignment($id)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', explode(',', $id ?? '')), function ($v) { return $v > 0; })));
        if (empty($ids)) {
            return redirect('/shift')->with('error', 'ID penugasan tidak valid.');
        }
        try {
            MappingShift::whereIn('id', $ids)->delete();
        } catch (\Throwable $e) {
            return redirect('/shift')->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
        return redirect('/shift')->with('success', 'Penugasan Shift Berhasil Dihapus');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=Template_Import_Shift.csv',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];
        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Karyawan*', 'Nama (Info)', 'ID Shift*', 'Nama Shift (Info)', 'Tgl Mulai* (DD/MM/YYYY)', 'Tgl Akhir* (DD/MM/YYYY)', 'Lock Location (1/0)']);
            $users  = User::limit(2)->get();
            $shifts = Shift::limit(2)->get();
            foreach ($users as $i => $u) {
                $s = $shifts[$i] ?? $shifts[0] ?? null;
                if ($s) fputcsv($file, [$u->id, $u->name, $s->id, $s->nama_shift, date('d/m/Y'), date('d/m/Y', strtotime('+7 days')), '1']);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        @ini_set('memory_limit', '1024M');
        $request->validate(['file_excel' => 'required']);
        try {
            $rows = Excel::toArray([], $request->file('file_excel'))[0] ?? [];
        } catch (\Throwable $e) {
            return redirect('/shift')->with('error', 'Gagal baca file Excel: '.$e->getMessage());
        }

        $now = now()->toDateTimeString();
        $batchSize = 500;
        $buffer = [];
        $totalInsert = 0;
        $errors = [];

        $flush = function () use (&$buffer, &$totalInsert, $now) {
            if (empty($buffer)) return;
            try {
                \DB::table('mapping_shifts')->upsert(
                    $buffer,
                    ['user_id', 'tanggal'],
                    ['shift_id', 'lock_location', 'updated_at']
                );
                $totalInsert += count($buffer);
            } catch (\Throwable $e) {
                throw $e;
            }
            $buffer = [];
        };

        try {
            \DB::beginTransaction();
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $uid = intval($row[0] ?? 0);
                $sid = intval($row[2] ?? 0);
                if ($uid <= 0 || $sid <= 0) continue;
                try {
                    $start = $this->parseDate($row[4] ?? null);
                    $end   = $this->parseDate($row[5] ?? null);
                    if (!$start || !$end) continue;
                    $s = Carbon::parse($start)->startOfDay();
                    $e = Carbon::parse($end)->startOfDay();
                    if ($e->lt($s)) continue;
                    if ($s->diffInDays($e) + 1 > 185) { $errors[] = "Row $i: rentang >185 hari skip"; continue; }
                    $lock = intval($row[6] ?? 0);
                    for ($d = $s->copy(); $d->lte($e); $d->addDay()) {
                        $buffer[] = [
                            'user_id'       => $uid,
                            'shift_id'      => $sid,
                            'tanggal'       => $d->toDateString(),
                            'lock_location' => $lock,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ];
                        if (count($buffer) >= $batchSize) $flush();
                    }
                } catch (\Throwable $e) { $errors[] = "Row $i: " . $e->getMessage(); continue; }
            }
            $flush();
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return redirect('/shift')->with('error', 'Import gagal: ' . $e->getMessage());
        }

        $msg = "Import Berhasil ({$totalInsert} baris diproses).";
        if (!empty($errors)) $msg .= ' Lewati '.count($errors).' baris bermasalah.';
        return redirect('/shift')->with('success', $msg);
    }

    private function parseDate($val): string
    {
        if (is_numeric($val)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val))->format('Y-m-d');
        }
        try {
            return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
        } catch (\Exception $e) {
            return Carbon::parse($val)->format('Y-m-d');
        }
    }

    public function create()
    {
        return view('shift.create', ['title' => 'Tambah Shift']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|max:255',
            'jam_masuk'  => 'required',
            'jam_keluar' => 'required',
            'jam_mulai_istirahat'   => 'nullable',
            'jam_selesai_istirahat' => 'nullable',
            'toleransi' => 'nullable|integer|min:0|max:480',
        ]);
        $validated = $request->validated();
        $validated['toleransi'] = intval($validated['toleransi'] ?? 0);
        Shift::create($validated);
        return redirect('/shift')->with('success', 'Shift Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        return view('shift.edit', ['title' => 'Edit Shift', 'shift' => Shift::findOrFail(intval($id))]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required|max:255',
            'jam_masuk'  => 'required',
            'jam_keluar' => 'required',
            'jam_mulai_istirahat'   => 'nullable',
            'jam_selesai_istirahat' => 'nullable',
            'toleransi' => 'nullable|integer|min:0|max:480',
        ]);
        try {
            $validated = $request->validated();
            $validated['toleransi'] = intval($validated['toleransi'] ?? 0);
            Shift::findOrFail(intval($id))->update($validated);
        } catch (\Throwable $e) {
            return redirect('/shift')->with('error', 'Gagal update Shift: '.$e->getMessage());
        }
        return redirect('/shift')->with('success', 'Shift Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $id = intval($id);
        try {
            $exists = \DB::table('mapping_shifts')->where('shift_id', $id)->exists()
                   || \DB::table('dinas_luars')->where('shift_id', $id)->exists();
            if ($exists) {
                Alert::error('Gagal', 'Shift masih digunakan oleh pegawai!');
                return back();
            }
            Shift::where('id', $id)->delete();
        } catch (\Throwable $e) {
            return redirect('/shift')->with('error', 'Gagal hapus Shift: '.$e->getMessage());
        }
        return redirect('/shift')->with('success', 'Shift Berhasil Dihapus');
    }

    public function show($id) {}
}
