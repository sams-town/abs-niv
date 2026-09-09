@extends('templates.dashboard')
@section('isi')
@push('style')
<style>
    .table th  { background: #f8fafc; font-size: 12px; white-space: nowrap; }
    .table td  { font-size: 13px; white-space: nowrap; }
    .stat-num  { font-size: 18px; font-weight: 700; }
    .stat-lbl  { font-size: 11px; color: #6b7280; }
    .pct-bar   { height: 6px; border-radius: 4px; background: #e5e7eb; }
    .pct-fill  { height: 100%; border-radius: 4px; }
    td.num     { text-align: center; }
    .badge-h   { background:#d1fae5;color:#065f46; }
    .badge-a   { background:#fee2e2;color:#991b1b; }
    .badge-c   { background:#ede9fe;color:#4338ca; }
    .badge-i   { background:#dbeafe;color:#1e40af; }
    .badge-s   { background:#fce7f3;color:#9d174d; }
    .badge-l   { background:#f3f4f6;color:#374151; }
    .mini-badge{ font-size:11px;font-weight:600;border-radius:10px;padding:2px 8px;display:inline-block; }
</style>
@endpush

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold">Rekap Absen Bulanan</h4>
            <small class="text-muted">Ringkasan kehadiran pegawai per bulan</small>
        </div>
        <a href="{{ url('/rekap-absen/harian') }}" class="btn btn-outline-primary btn-sm">
            <i data-feather="list" style="width:14px"></i> Lihat Rekap Harian
        </a>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ url('/rekap-absen/bulanan') }}" class="row g-2 align-items-end">
                <div class="col-md-2 col-sm-6">
                    <label class="form-label mb-1 small fw-semibold">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        @foreach ($bulan_list as $idx => $nama)
                            @if($idx > 0)
                                <option value="{{ str_pad($idx,2,'0',STR_PAD_LEFT) }}"
                                    {{ $bulan == str_pad($idx,2,'0',STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label mb-1 small fw-semibold">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-1 small fw-semibold">Lokasi</label>
                    <select name="lokasi_id" class="form-select form-select-sm">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasi as $l)
                            <option value="{{ $l->id }}" {{ $lokasi_id == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label mb-1 small fw-semibold">Cari Nama</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Nama pegawai..." value="{{ $search }}">
                </div>
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i data-feather="search" style="width:14px"></i> Filter
                    </button>
                    <a href="{{ url('/rekap-absen/bulanan') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <a href="{{ url('/rekap-absen/bulanan/export?' . http_build_query(request()->query())) }}"
                       class="btn btn-success btn-sm">
                        <i data-feather="download" style="width:14px"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan Total --}}
    @php
        $totHadir = collect($rows)->sum('hadir');
        $totAlpha = collect($rows)->sum('alpha');
        $totCuti  = collect($rows)->sum('cuti');
        $totIzin  = collect($rows)->sum('izin');
        $totSakit = collect($rows)->sum('sakit');
        $totLibur = collect($rows)->sum('libur');
    @endphp
    <div class="row g-3 mb-3">
        @foreach([
            ['Hadir',   $totHadir, '#10b981'],
            ['Alpha',   $totAlpha, '#ef4444'],
            ['Cuti',    $totCuti,  '#6366f1'],
            ['Izin',    $totIzin,  '#3b82f6'],
            ['Sakit',   $totSakit, '#ec4899'],
            ['Libur',   $totLibur, '#6b7280'],
        ] as [$lbl, $val, $color])
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="stat-num" style="color:{{ $color }}">{{ $val }}</div>
                <div class="stat-lbl">Total {{ $lbl }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabel Rekap --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between py-2">
            <span class="fw-semibold">
                <i data-feather="bar-chart-2" style="width:16px"></i>
                Rekap {{ $bulan_list[(int)$bulan] }} {{ $tahun }}
                <span class="badge bg-secondary ms-1">{{ count($rows) }} pegawai</span>
            </span>
            <small class="text-muted">{{ \Carbon\Carbon::parse($mulai)->format('d M Y') }} – {{ \Carbon\Carbon::parse($akhir)->format('d M Y') }}</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th>Nama Pegawai</th>
                            <th>Jabatan</th>
                            <th>Lokasi</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Terlambat</th>
                            <th class="text-center">Cuti</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Libur</th>
                            <th class="text-center">Total Telat</th>
                            <th style="min-width:120px">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $row)
                            @php
                                $user = $row['user'];
                                $pct  = $row['persentase'];
                                $pctColor = $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <tr>
                                <td class="text-center text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->username }}</div>
                                </td>
                                <td class="small">{{ $user->Jabatan->nama_jabatan ?? '-' }}</td>
                                <td class="small">{{ $user->Lokasi->nama_lokasi ?? '-' }}</td>
                                <td class="num">
                                    <span class="mini-badge badge-h">{{ $row['hadir'] }}</span>
                                </td>
                                <td class="num">
                                    @if($row['alpha'] > 0)
                                        <span class="mini-badge badge-a">{{ $row['alpha'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="num">
                                    @if($row['terlambat'] > 0)
                                        <span class="mini-badge" style="background:#fef3c7;color:#92400e">{{ $row['terlambat'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="num">
                                    @if($row['cuti'] > 0)
                                        <span class="mini-badge badge-c">{{ $row['cuti'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="num">
                                    @if($row['izin'] > 0)
                                        <span class="mini-badge badge-i">{{ $row['izin'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="num">
                                    @if($row['sakit'] > 0)
                                        <span class="mini-badge badge-s">{{ $row['sakit'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="num">
                                    <span class="mini-badge badge-l">{{ $row['libur'] }}</span>
                                </td>
                                <td class="text-center small">
                                    @if($row['total_telat'] !== '0j 0m')
                                        <span class="text-danger">{{ $row['total_telat'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pct-bar flex-fill">
                                            <div class="pct-fill" style="width:{{ $pct }}%;background:{{ $pctColor }}"></div>
                                        </div>
                                        <span class="small fw-semibold" style="color:{{ $pctColor }};min-width:38px">
                                            {{ $pct }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-4">
                                    <i data-feather="inbox" style="width:32px;opacity:.4"></i>
                                    <div class="mt-2">Tidak ada data untuk filter ini</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rows) > 0)
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td colspan="4" class="text-end small">Total</td>
                            <td class="num">{{ $totHadir }}</td>
                            <td class="num">{{ $totAlpha }}</td>
                            <td class="num">{{ collect($rows)->sum('terlambat') }}</td>
                            <td class="num">{{ $totCuti }}</td>
                            <td class="num">{{ $totIzin }}</td>
                            <td class="num">{{ $totSakit }}</td>
                            <td class="num">{{ $totLibur }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>

@push('script')
<script>feather.replace();</script>
@endpush
@endsection
