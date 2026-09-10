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
        $search = request()->input('search');

        $shifts = Shift::when($search, fn($q) => $q->where('nama_shift', 'LIKE', "%$search%"))
            ->orderBy('nama_shift')
            ->get();

        foreach ($shifts as $shift) {
            $mappings = MappingShift::with('User')
                ->where('shift_id', $shift->id)
                ->where('tanggal', '>=', now()->subDays(30)->format('Y-m-d'))
                ->get();

            $grouped = [];
            foreach ($mappings as $m) {
                if (!$m->User) continue;
                $uid = $m->user_id;
                if (!isset($grouped[$uid])) {
                    $grouped[$uid] = [
                        'user'          => $m->User,
                        'dates'         => [],
                        'lock_location' => $m->lock_location,
                        'mapping_ids'   => [],
                    ];
                }
                $grouped[$uid]['dates'][]       = $m->tanggal;
                $grouped[$uid]['mapping_ids'][] = $m->id;
            }

            $assigned = [];
            foreach ($grouped as $g) {
                $dates = $g['dates'];
                sort($dates);
                $ranges = [];
                if (!empty($dates)) {
                    $start = Carbon::parse($dates[0]);
                    $prev  = Carbon::parse($dates[0]);
                    for ($i = 1; $i < count($dates); $i++) {
                        $curr = Carbon::parse($dates[$i]);
                        if ($curr->diffInDays($prev) > 1) {
                            $ranges[] = $this->dateRange($start, $prev);
                            $start    = $curr;
                        }
                        $prev = $curr;
                    }
                    $ranges[] = $this->dateRange($start, $prev);
                }
                $assigned[] = [
                    'user'          => $g['user'],
                    'range'         => implode(', ', $ranges),
                    'lock_location' => $g['lock_location'],
                    'mapping_ids'   => implode(',', $g['mapping_ids']),
                ];
            }
            $shift->assigned_employees = $assigned;
        }

        return view('shift.index', [
            'title'           => 'Shift',
            'shifts'          => $shifts,
            'total_shift'     => Shift::count(),
            'karyawan_aktif'  => User::pegawaiDanDosen()->whereNull('masa_berlaku')->orWhere('masa_berlaku', '>', now()->format('Y-m-d'))->count(),
            'jadwal_terjadwal'=> MappingShift::whereNotNull('shift_id')->count(),
            'all_users'       => User::pegawaiDanDosen()->orderBy('name')->get(),
        ]);
    }

    private function dateRange(Carbon $start, Carbon $end): string
    {
        Carbon::setLocale('id');
        if ($start->equalTo($end)) return $start->translatedFormat('d M y');
        if ($start->month === $end->month && $start->year === $end->year) {
            return $start->translatedFormat('d') . ' - ' . $end->translatedFormat('d M y');
        }
        return $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M y');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'shift_id'      => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        $userIds = $request->user_ids ?? ($request->user_id ? [$request->user_id] : []);

        if (empty($userIds)) {
            return redirect('/shift')->with('error', 'Pilih minimal satu pegawai.');
        }

        $dates = new \DatePeriod(
            new \DateTime($request->tanggal_mulai),
            new \DateInterval('P1D'),
            (new \DateTime($request->tanggal_akhir))->modify('+1 day')
        );

        foreach ($userIds as $userId) {
            foreach ($dates as $date) {
                MappingShift::updateOrCreate(
                    ['user_id' => $userId, 'tanggal' => $date->format('Y-m-d')],
                    ['shift_id' => $request->shift_id, 'lock_location' => $request->lock_location ?? 0]
                );
            }
        }

        return redirect('/shift')->with('success', 'Penugasan Shift Berhasil Dibuat');
    }

    public function deleteAssignment($id)
    {
        MappingShift::whereIn('id', explode(',', $id))->delete();
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
        $request->validate(['file_excel' => 'required']);
        $rows = Excel::toArray([], $request->file('file_excel'))[0];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[0])) continue;
            try {
                $start = $this->parseDate($row[4]);
                $end   = $this->parseDate($row[5]);
            } catch (\Exception $e) { continue; }

            if ($start && $end) {
                $dates = new \DatePeriod(
                    new \DateTime($start),
                    new \DateInterval('P1D'),
                    (new \DateTime($end))->modify('+1 day')
                );
                foreach ($dates as $date) {
                    MappingShift::updateOrCreate(
                        ['user_id' => $row[0], 'tanggal' => $date->format('Y-m-d')],
                        ['shift_id' => $row[2], 'lock_location' => $row[6] ?? 0]
                    );
                }
            }
        }
        return redirect('/shift')->with('success', 'Import Berhasil');
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
        ]);
        Shift::create($request->validated());
        return redirect('/shift')->with('success', 'Shift Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        return view('shift.edit', ['title' => 'Edit Shift', 'shift' => Shift::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift' => 'required|max:255',
            'jam_masuk'  => 'required',
            'jam_keluar' => 'required',
            'jam_mulai_istirahat'   => 'nullable',
            'jam_selesai_istirahat' => 'nullable',
        ]);
        Shift::where('id', $id)->update($request->validated());
        return redirect('/shift')->with('success', 'Shift Berhasil Diupdate');
    }

    public function destroy($id)
    {
        if (MappingShift::where('shift_id', $id)->exists() || dinasLuar::where('shift_id', $id)->exists()) {
            Alert::error('Gagal', 'Shift masih digunakan oleh pegawai!');
            return back();
        }
        Shift::findOrFail($id)->delete();
        return redirect('/shift')->with('success', 'Shift Berhasil Dihapus');
    }

    public function show($id) {}
}
