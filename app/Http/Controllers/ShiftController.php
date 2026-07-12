<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\MappingShift;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = request()->input('search');
        $shifts = Shift::when($search, function ($query) use ($search) {
                    $query->where('nama_shift', 'LIKE', '%' . $search . '%');
                })
                ->orderBy('nama_shift', 'ASC')
                ->get(); // Fetch all to display as cards as per design

        $total_shift = Shift::count();
        $karyawan_aktif = User::where(function ($query) {
            $query->whereNull('masa_berlaku')
                  ->orWhere('masa_berlaku', '>', date('Y-m-d'));
        })->count();
        $jadwal_terjadwal = MappingShift::whereNotNull('shift_id')->count();

        // Group mapping shifts by shift and user to get assignment date ranges
        foreach ($shifts as $s) {
            $shift_mappings = MappingShift::with('User')
                ->where('shift_id', $s->id)
                ->where('tanggal', '>=', date('Y-m-d', strtotime('-30 days'))) // show recent/upcoming
                ->get();
                
            $grouped = [];
            foreach ($shift_mappings as $m) {
                if (!$m->User) continue;
                $uid = $m->user_id;
                if (!isset($grouped[$uid])) {
                    $grouped[$uid] = [
                        'user' => $m->User,
                        'dates' => [],
                        'lock_location' => $m->lock_location,
                        'mappings' => []
                    ];
                }
                $grouped[$uid]['dates'][] = $m->tanggal;
                $grouped[$uid]['mappings'][] = $m->id;
            }
            
            $assigned_employees = [];
            foreach ($grouped as $uid => $g) {
                $dates = $g['dates'];
                sort($dates);
                
                $ranges = [];
                if (!empty($dates)) {
                    $start = Carbon::parse($dates[0]);
                    $prev = Carbon::parse($dates[0]);
                    for ($i = 1; $i < count($dates); $i++) {
                        $curr = Carbon::parse($dates[$i]);
                        if ($curr->diffInDays($prev) > 1) {
                            $ranges[] = $this->formatDateRange($start, $prev);
                            $start = $curr;
                        }
                        $prev = $curr;
                    }
                    $ranges[] = $this->formatDateRange($start, $prev);
                }
                
                $assigned_employees[] = [
                    'user' => $g['user'],
                    'range' => implode(', ', $ranges),
                    'lock_location' => $g['lock_location'],
                    'mapping_ids' => implode(',', $g['mappings'])
                ];
            }
            $s->assigned_employees = $assigned_employees;
        }

        $all_users = User::orderBy('name', 'asc')->get();

        return view('shift.index', [
            'title' => 'Shift',
            'shifts' => $shifts,
            'total_shift' => $total_shift,
            'karyawan_aktif' => $karyawan_aktif,
            'jadwal_terjadwal' => $jadwal_terjadwal,
            'all_users' => $all_users
        ]);
    }

    private function formatDateRange($start, $end)
    {
        Carbon::setLocale('id');
        if ($start->equalTo($end)) {
            return $start->translatedFormat('d M y');
        }
        if ($start->month === $end->month && $start->year === $end->year) {
            return $start->translatedFormat('d') . ' - ' . $end->translatedFormat('d M y');
        }
        return $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M y');
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Template_Import_Shift_Pegawai.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Karyawan*', 'Nama Karyawan (Info)', 'ID Shift*', 'Nama Shift (Info)', 'Tanggal Mulai* (DD/MM/YYYY)', 'Tanggal Akhir* (DD/MM/YYYY)', 'Lock Location (1/0)'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $users = User::limit(2)->get();
            $shifts = Shift::limit(2)->get();
            
            foreach ($users as $index => $user) {
                $shift = isset($shifts[$index]) ? $shifts[$index] : ($shifts[0] ?? null);
                if ($shift) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $shift->id,
                        $shift->nama_shift,
                        date('d/m/Y'),
                        date('d/m/Y', strtotime('+7 days')),
                        '1'
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required'
        ]);

        $file = $request->file('file_excel');
        $rows = Excel::toArray([], $file)[0];
        
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[0])) continue;

            $userId = $row[0];
            $shiftId = $row[2];
            $startDateStr = $row[4];
            $endDateStr = $row[5];
            $lockLocation = isset($row[6]) ? $row[6] : 0;

            try {
                $startDate = $this->parseExcelDate($startDateStr);
                $endDate = $this->parseExcelDate($endDateStr);
            } catch (\Exception $e) {
                continue;
            }

            if ($startDate && $endDate) {
                $begin = new \DateTime($startDate);
                $end = new \DateTime($endDate);
                $end = $end->modify('+1 day');

                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($begin, $interval ,$end);

                foreach ($daterange as $date) {
                    $tanggal = $date->format("Y-m-d");

                    MappingShift::updateOrCreate(
                        ['user_id' => $userId, 'tanggal' => $tanggal],
                        ['shift_id' => $shiftId, 'lock_location' => $lockLocation]
                    );
                }
            }
        }

        return redirect('/shift')->with('success', 'Jadwal Shift Berhasil Di-import');
    }

    private function parseExcelDate($dateVal)
    {
        if (is_numeric($dateVal)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal))->format('Y-m-d');
        }
        try {
            return Carbon::createFromFormat('d/m/Y', $dateVal)->format('Y-m-d');
        } catch (\Exception $e) {
            return Carbon::parse($dateVal)->format('Y-m-d');
        }
    }

    public function deleteAssignment($id)
    {
        $ids = explode(',', $id);
        MappingShift::whereIn('id', $ids)->delete();
        return redirect('/shift')->with('success', 'Penugasan Shift Berhasil Dihapus');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'shift_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        $userIds = $request->user_ids;
        if (!$userIds && $request->user_id) {
            $userIds = [$request->user_id];
        }

        if (empty($userIds)) {
            return redirect('/shift')->with('error', 'Pilih minimal satu pegawai atau dosen');
        }

        $begin = new \DateTime($request->tanggal_mulai);
        $end = new \DateTime($request->tanggal_akhir);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);

        foreach ($userIds as $userId) {
            foreach ($daterange as $date) {
                $tanggal = $date->format("Y-m-d");
                MappingShift::updateOrCreate(
                    ['user_id' => $userId, 'tanggal' => $tanggal],
                    ['shift_id' => $request->shift_id, 'lock_location' => $request->lock_location ?? 0]
                );
            }
        }

        return redirect('/shift')->with('success', 'Penugasan Shift Berhasil Dibuat');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shift.create', [
            'title' => 'Tambah Data Master Shift'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_shift' => 'required|max:255',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
            'jam_mulai_istirahat' => 'nullable',
            'jam_selesai_istirahat' => 'nullable'
        ]);

        Shift::create($validatedData);
        return redirect('/shift')->with('success', 'Data Berhasil di Tambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view("shift.edit", [
            'title' => 'Edit Shift',
            'shift' => Shift::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_shift' => 'required|max:255',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
            'jam_mulai_istirahat' => 'nullable',
            'jam_selesai_istirahat' => 'nullable'
        ]);

        Shift::where('id', $id)->update($validatedData);
        return redirect('/shift')->with('success', 'Data Berhasil di Update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $check = MappingShift::where('shift_id', $id)->count();
        $check2 = dinasLuar::where('shift_id', $id)->count();
        if ($check > 0 || $check2 > 0) {
            Alert::error('Failed', 'Masih Ada User Yang Menggunakan Shift Ini!');
            return back();
        } else {
            $delete = Shift::find($id);
            $delete->delete();
        }
        return redirect('/shift')->with('success', 'Data Berhasil di Delete');
    }
}
