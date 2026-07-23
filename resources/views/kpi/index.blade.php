@extends('templates.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Manajemen Penilaian KPI</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">KPI</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <span class="badge bg-light text-primary border mb-2">Tahun Penilaian {{ $year }}</span>
                                <h4 class="mb-1">Monitoring Penilaian KPI Pegawai</h4>
                                <p class="text-muted mb-0">
                                    Pantau status import target, progres penilaian, dan hasil akhir KPI seluruh pegawai dalam satu halaman.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary" disabled>
                                    <i class="fa fa-file-excel-o me-2"></i>Import Target KPI Excel
                                </button>
                                <button type="button" class="btn btn-primary" disabled>
                                    <i class="fa fa-download me-2"></i>Export Rekap Tahunan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Total Pegawai</span>
                                <h3 class="mb-1">{{ number_format($totalPegawai) }}</h3>
                                <small class="text-muted">Pegawai dan dosen aktif pada modul KPI</small>
                            </div>
                            <div class="square-box bg-light-primary">
                                <i data-feather="users" class="text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Sudah Dinilai</span>
                                <h3 class="mb-1 text-success">{{ number_format($sudahDinilai) }}</h3>
                                <small class="text-muted">Status evaluasi sudah `finalized`</small>
                            </div>
                            <div class="square-box bg-light-success">
                                <i data-feather="check-circle" class="text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Belum Dinilai</span>
                                <h3 class="mb-1 text-warning">{{ number_format($belumDinilai) }}</h3>
                                <small class="text-muted">Masih menunggu finalisasi penilaian</small>
                            </div>
                            <div class="square-box bg-light-warning">
                                <i data-feather="clock" class="text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <div>
                                <h5 class="mb-1">Daftar Penilaian Pegawai</h5>
                                <span class="text-muted">Menampilkan data penilaian KPI tahun {{ $year }} dengan pagination 10 data per halaman.</span>
                            </div>
                            <form action="{{ url('/kpi') }}" method="GET" class="d-flex align-items-center gap-2">
                                <label for="year" class="mb-0 text-muted">Tahun</label>
                                <input type="number" id="year" name="year" class="form-control" value="{{ $year }}" min="2020" max="2100" style="max-width: 120px;">
                                <button type="submit" class="btn btn-outline-secondary">Terapkan</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai &amp; Email</th>
                                        <th>Jabatan/Unit</th>
                                        <th>Status Import Target</th>
                                        <th>Nilai Akhir</th>
                                        <th>Grade</th>
                                        <th>Status Penilaian</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pegawai as $item)
                                        @php
                                            $evaluation = $item->kpiEvaluation;
                                            $statusPenilaian = $evaluation->status ?? 'belum_dinilai';
                                            $statusImport = $item->imported_targets_count > 0;
                                            $statusBadgeMap = [
                                                'finalized' => 'success',
                                                'approved' => 'primary',
                                                'submitted' => 'info',
                                                'draft' => 'warning',
                                                'belum_dinilai' => 'secondary',
                                            ];
                                            $statusBadgeClass = $statusBadgeMap[$statusPenilaian] ?? 'secondary';
                                        @endphp
                                        <tr>
                                            <td>{{ $pegawai->firstItem() + $loop->index }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $item->name }}</div>
                                                <div class="text-muted">{{ $item->email ?: '-' }}</div>
                                            </td>
                                            <td>
                                                <div>{{ optional($item->Jabatan)->nama_jabatan ?? 'Belum diatur' }}</div>
                                                <small class="text-muted">{{ optional($item->Lokasi)->nama_lokasi ?? 'Unit belum diatur' }}</small>
                                            </td>
                                            <td>
                                                @if ($statusImport)
                                                    <span class="badge bg-light-success text-success">Sudah Import</span>
                                                @else
                                                    <span class="badge bg-light-warning text-warning">Belum Import</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-semibold">
                                                    {{ $evaluation && $evaluation->final_score !== null ? number_format($evaluation->final_score, 2) : '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light-primary text-primary">
                                                    {{ $evaluation->grade ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light-{{ $statusBadgeClass }} text-{{ $statusBadgeClass }}">
                                                    {{ $statusPenilaian === 'belum_dinilai' ? 'Belum Dinilai' : ucfirst(str_replace('_', ' ', $statusPenilaian)) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ url('/kpi/evaluation/' . $item->id . '?year=' . $year) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit me-1"></i>{{ $evaluation ? 'Review' : 'Beri Nilai' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Data pegawai untuk penilaian KPI belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 mt-4">
                            <div class="text-muted">
                                Menampilkan {{ $pegawai->firstItem() ?? 0 }} - {{ $pegawai->lastItem() ?? 0 }} dari {{ $pegawai->total() }} data.
                            </div>
                            <div>
                                {{ $pegawai->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
