@extends('templates.dashboard')
@php
    use App\Models\TransaksiMengajar;
    use App\Models\User;

    $bulanList = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $bulanAktif = request('bulan', date('n'));
    $tahunAktif = request('tahun', date('Y'));

    $rows = TransaksiMengajar::with('dosen')
        ->whereMonth('tanggal', $bulanAktif)
        ->whereYear('tanggal', $tahunAktif)
        ->selectRaw('dosen_id, COUNT(*) as jumlah_sesi, SUM(nominal_honor) as total_honor, SUM(total_gaji) as total_gaji')
        ->groupBy('dosen_id')
        ->get();
@endphp
@section('isi')
<div style="padding:24px">
    <div class="mb-4">
        <h3 style="font-weight:800;color:#0f172a;margin:0">Payroll Dosen</h3>
        <p class="text-muted mb-0" style="font-size:14px">Kelola penggajian dosen berdasarkan sesi mengajar yang telah diverifikasi</p>
    </div>

    <form method="GET" id="filterForm" class="d-flex gap-3 align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <label class="mb-0 fw-semibold" style="font-size:14px">Bulan</label>
            <select name="bulan" class="form-select form-select-sm" style="width:130px" onchange="document.getElementById('filterForm').submit()">
                @foreach($bulanList as $id => $nama)
                    @if($id==0) @continue @endif
                    <option value="{{ $id }}" {{ $bulanAktif==$id?'selected':'' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="mb-0 fw-semibold" style="font-size:14px">Tahun</label>
            <select name="tahun" class="form-select form-select-sm" style="width:90px" onchange="document.getElementById('filterForm').submit()">
                @for($y=date('Y');$y>=date('Y')-5;$y--)
                    <option value="{{ $y }}" {{ $tahunAktif==$y?'selected':'' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>

    @if($rows->count() == 0)
    <div class="alert alert-info d-flex align-items-center gap-2 rounded-3">
        <i data-feather="info" style="width:16px"></i>
        <span>Belum ada data payroll dosen untuk <strong>{{ $bulanList[$bulanAktif] }} {{ $tahunAktif }}</strong>.
        Pastikan sesi mengajar sudah diverifikasi di <a href="{{ url('/admin/token-verifikasi') }}">Verifikasi Token</a>.</span>
    </div>
    @else
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="py-3">Nama Dosen</th>
                            <th class="py-3">NIDN</th>
                            <th class="py-3">Bulan</th>
                            <th class="py-3 text-center">Jumlah Sesi</th>
                            <th class="py-3">Total Honorarium</th>
                            <th class="py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                        <tr style="border-bottom:1px solid #f5f5f5">
                            <td class="px-4 py-3">{{ $i+1 }}</td>
                            <td class="py-3" style="font-weight:600">{{ optional($row->dosen)->name ?? '-' }}</td>
                            <td class="py-3 text-muted">{{ optional($row->dosen)->nidn ?? '-' }}</td>
                            <td class="py-3">{{ $bulanList[$bulanAktif] }} {{ $tahunAktif }}</td>
                            <td class="py-3 text-center">
                                <span class="badge bg-primary rounded-pill">{{ $row->jumlah_sesi }} sesi</span>
                            </td>
                            <td class="py-3" style="font-weight:700;color:#1a7a44">
                                Rp {{ number_format($row->total_gaji ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge rounded-pill px-3" style="background:#e0f2fe;color:#0369a1">Terhitung</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8f9fa;border-top:2px solid #eee">
                            <td colspan="4" class="px-4 py-3 fw-semibold text-end">Total Seluruh Honorarium:</td>
                            <td class="py-3 text-center fw-bold">{{ $rows->sum('jumlah_sesi') }} sesi</td>
                            <td class="py-3" style="font-weight:800;color:#1a7a44">
                                Rp {{ number_format($rows->sum('total_gaji'), 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
