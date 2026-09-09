@extends('templates.dashboard')
@section('isi')
@push('style')
<style>
    .summary-card { border-radius: 12px; padding: 16px 20px; color: #fff; min-width: 120px; }
    .badge-hadir   { background: #10b981; }
    .badge-alpha   { background: #ef4444; }
    .badge-terlambat { background: #f59e0b; }
    .badge-cuti    { background: #6366f1; }
    .badge-izin    { background: #3b82f6; }
    .badge-sakit   { background: #ec4899; }
    .badge-libur   { background: #6b7280; }
    .status-pill   { font-size: 11px; font-weight: 600; border-radius: 20px; padding: 3px 10px; display: inline-block; }
    .pill-hadir    { background: #d1fae5; color: #065f46; }
    .pill-alpha    { background: #fee2e2; color: #991b1b; }
    .pill-terlambat{ background: #fef3c7; color: #92400e; }
    .pill-cuti     { background: #ede9fe; color: #4338ca; }
    .pill-izin     { background: #dbeafe; color: #1e40af; }
    .pill-sakit    { background: #fce7f3; color: #9d174d; }
    .pill-libur    { background: #f3f4f6; color: #374151; }
    .table th      { background: #f8fafc; font-size: 13px; white-space: nowrap; }
    .foto-absen    { width: 40px; height: 40px; object-fit: cover; border-radius: 6px; cursor: pointer; }
    .no-foto       { width: 40px; height: 40px; background: #e5e7eb; border-radius: 6px; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:18px; }
</style>
@endpush

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold">Rekap Absen Harian</h4>
            <small class="text-muted">Data kehadiran pegawai per tanggal</small>
        </div>
        <a href="{{ url('/rekap-absen/bulanan') }}" class="btn btn-outline-primary btn-sm">
            <i data-feather="calendar" style="width:14px"></i> Lihat Rekap Bulanan
        </a>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ url('/rekap-absen/harian') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-1 small fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}">
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
                <div class="col-md-3 col-sm-6">
                    <label class="form-label mb-1 small fw-semibold">Cari Nama</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama pegawai..." value="{{ $search }}">
                </div>
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i data-feather="search" style="width:14px"></i> Filter
                    </button>
                    <a href="{{ url('/rekap-absen/harian') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-2">
            <div class="summary-card badge-hadir text-center shadow-sm">
                <div class="fs-4 fw-bold">{{ $summary['hadir'] }}</div>
                <div class="small">Hadir</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="summary-card badge-alpha text-center shadow-sm">
                <div class="fs-4 fw-bold">{{ $summary['alpha'] }}</div>
                <div class="small">Alpha</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="summary-card badge-terlambat text-center shadow-sm">
                <div class="fs-4 fw-bold">{{ $summary['terlambat'] }}</div>
                <div class="small">Terlambat</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="summary-card badge-cuti text-center shadow-sm">
                <div class="fs-4 fw-bold">{{ $summary['cuti'] }}</div>
                <div class="small">Cuti</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="summary-card badge-izin text-center shadow-sm">
                <div class="fs-4 fw-bold">{{ $summary['izin'] }}</div>
                <div class="small">Izin</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="summary-card badge-sakit text-center shadow-sm">
                <div class="fs-4 fw-bold">{{ $summary['sakit'] }}</div>
                <div class="small">Sakit</div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between py-2">
            <span class="fw-semibold">
                <i data-feather="list" style="width:16px"></i>
                Data Kehadiran — {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                <span class="badge bg-secondary ms-1">{{ $users->count() }} pegawai</span>
            </span>
            <a href="{{ url('/rekap-absen/harian/export?' . http_build_query(request()->query())) }}"
               class="btn btn-success btn-sm">
                <i data-feather="download" style="width:14px"></i> Export Excel
            </a>
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
                            <th>Shift</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Telat</th>
                            <th class="text-center">Jam Pulang</th>
                            <th class="text-center">Foto</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $i => $user)
                            @php
                                $ms     = $user->MappingShift->first();
                                $status = $ms->status_absen ?? null;
                                $pillClass = match($status) {
                                    'Masuk'             => 'pill-hadir',
                                    'Izin Telat',
                                    'Izin Pulang Cepat' => 'pill-terlambat',
                                    'Cuti'              => 'pill-cuti',
                                    'Izin Masuk'        => 'pill-izin',
                                    'Sakit'             => 'pill-sakit',
                                    'Libur'             => 'pill-libur',
                                    default             => 'pill-alpha',
                                };
                                $statusLabel = $status ?? 'Alpha';

                                // Hitung telat
                                $telatStr = '-';
                                if ($ms && $ms->telat > 0) {
                                    $jam   = floor($ms->telat / 3600);
                                    $menit = floor(($ms->telat % 3600) / 60);
                                    $telatStr = ($jam > 0 ? "{$jam}j " : '') . "{$menit}m";
                                }
                            @endphp
                            <tr>
                                <td class="text-center text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->username }}</div>
                                </td>
                                <td class="small">{{ $user->Jabatan->nama_jabatan ?? '-' }}</td>
                                <td class="small">{{ $user->Lokasi->nama_lokasi ?? '-' }}</td>
                                <td class="small">
                                    @if($ms && $ms->Shift)
                                        {{ $ms->Shift->nama_shift ?? '' }}<br>
                                        <span class="text-muted">{{ $ms->Shift->jam_masuk }} - {{ $ms->Shift->jam_keluar }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center small">{{ $ms->jam_absen ?? '-' }}</td>
                                <td class="text-center small">
                                    @if($ms && $ms->telat > 0)
                                        <span class="text-danger fw-semibold">{{ $telatStr }}</span>
                                    @else
                                        <span class="text-success">-</span>
                                    @endif
                                </td>
                                <td class="text-center small">{{ $ms->jam_pulang ?? '-' }}</td>
                                <td class="text-center">
                                    @if($ms && $ms->foto_jam_absen)
                                        <img src="{{ asset('storage/' . $ms->foto_jam_absen) }}"
                                             class="foto-absen"
                                             onclick="window.open(this.src,'_blank')"
                                             title="Foto Masuk">
                                    @else
                                        <div class="no-foto mx-auto"><i data-feather="user" style="width:16px"></i></div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="status-pill {{ $pillClass }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i data-feather="inbox" style="width:32px;opacity:.4"></i>
                                    <div class="mt-2">Tidak ada data untuk filter ini</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('script')
<script>feather.replace();</script>
@endpush
@endsection
