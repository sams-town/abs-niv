@extends('templates.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Manajemen KPI Korporat</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">KPI</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Top Card: Filters & Import -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div class="w-100">
                                <span class="badge bg-light text-primary border mb-2">Tahun Penilaian: {{ $year }}</span>
                                <h4 class="mb-1">Monitoring Kinerja Keseluruhan Perusahaan</h4>
                                <p class="text-muted mb-0 small">Kelola target, penilaian, dan laporan KPI karyawan secara terpusat</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap gap-2">
                                @if(auth()->check() && auth()->user()->is_admin === 'admin')
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="fa fa-file-excel-o me-2"></i>Import Target KPI Excel
                                    </button>
                                @endif
                                <button class="btn btn-primary" type="button" disabled>
                                    <i class="fa fa-download me-2"></i>Export Rekap Tahunan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistic Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Total Karyawan</span>
                                <h3 class="mb-1">{{ number_format($totalPegawai ?? 0) }}</h3>
                                <small class="text-muted">Karyawan & Dosen Aktif</small>
                            </div>
                            <div class="square-box bg-light-primary">
                                <i data-feather="users" class="text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Rata-rata Skor Perusahaan</span>
                                <h3 class="mb-1 text-success">{{ number_format($averageScore ?? 0, 1) }}</h3>
                                <small class="text-muted">Dari {{ number_format($sudahDinilai ?? 0) }} penilaian</small>
                            </div>
                            <div class="square-box bg-light-success">
                                <i data-feather="trending-up" class="text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Sudah Dinilai (Finalized)</span>
                                <h3 class="mb-1 text-primary">{{ number_format($sudahDinilai ?? 0) }}</h3>
                                <small class="text-muted">Karyawan dengan penilaian final</small>
                            </div>
                            <div class="square-box bg-light-info">
                                <i data-feather="check-circle" class="text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-muted d-block mb-1">Kinerja Tertinggi</span>
                                <h3 class="mb-1 text-warning">{{ $highestPerformer && $highestPerformer->user ? $highestPerformer->user->name : '-' }}</h3>
                                <small class="text-muted">
                                    Skor: {{ $highestPerformer && $highestPerformer->final_score ? number_format($highestPerformer->final_score, 1) : '-' }}
                                </small>
                            </div>
                            <div class="square-box bg-light-warning">
                                <i data-feather="award" class="text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h5 class="mb-1">Distribusi Grade Karyawan</h5>
                        <span class="text-muted small">Tahun {{ $year }}</span>
                    </div>
                    <div class="card-body">
                        <canvas id="gradeChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h5 class="mb-1">Status Penilaian</h5>
                        <span class="text-muted small">Tahun {{ $year }}</span>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <h5 class="mb-0">Daftar Seluruh Karyawan</h5>

                            <!-- Filters -->
                            <form action="{{ url('/kpi') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                                <input type="number" name="year" class="form-control" value="{{ $year }}" min="2020" max="2100" style="max-width: 120px;" placeholder="Tahun">

                                <select class="form-select" name="jabatan_id" style="max-width: 200px;">
                                    <option value="">Semua Jabatan</option>
                                    @foreach($jabatanList ?? [] as $j)
                                        <option value="{{ $j->id }}" {{ $jabatanId == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                    @endforeach
                                </select>

                                <select class="form-select" name="lokasi_id" style="max-width: 200px;">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($lokasiList ?? [] as $l)
                                        <option value="{{ $l->id }}" {{ $lokasiId == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                                    @endforeach
                                </select>

                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Cari nama, email, NIP...">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
                                </div>

                                <a href="{{ url('/kpi') }}" class="btn btn-light">Reset</a>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama &amp; Email</th>
                                        <th>Jabatan / Unit</th>
                                        <th class="text-center">Status Target</th>
                                        <th class="text-center">Skor Akhir</th>
                                        <th class="text-center">Grade</th>
                                        <th class="text-center">Status Penilaian</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pegawai ?? [] as $index => $item)
                                        @php
                                            $evaluation = $item->kpiEvaluation;
                                            $statusPenilaian = $evaluation->status ?? 'belum_dinilai';
                                            $statusImport = $item->imported_targets_count > 0;
                                            $statusBadgeClass = 'secondary';
                                            if ($statusPenilaian === 'finalized') {
                                                $statusBadgeClass = 'success';
                                            } elseif ($statusPenilaian === 'approved') {
                                                $statusBadgeClass = 'primary';
                                            } elseif ($statusPenilaian === 'submitted') {
                                                $statusBadgeClass = 'info';
                                            } elseif ($statusPenilaian === 'draft') {
                                                $statusBadgeClass = 'warning';
                                            }
                                            $statusText = ucwords(str_replace('_', ' ', $statusPenilaian));
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $pegawai->firstItem() + $index }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $item->name ?? '-' }}</div>
                                                <div class="text-muted small">{{ $item->email ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div>{{ optional($item->Jabatan)->nama_jabatan ?? 'Belum diatur' }}</div>
                                                <small class="text-muted">{{ optional($item->Lokasi)->nama_lokasi ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($statusImport)
                                                    <span class="badge bg-light-success text-success">Ter-import</span>
                                                @else
                                                    <span class="badge bg-light-warning text-warning">Belum Import</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold">
                                                    {{ $evaluation && $evaluation->final_score !== null ? number_format($evaluation->final_score, 1) : '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $grade = $evaluation->grade ?? '-';
                                                    $gradeBadgeClass = 'secondary';
                                                    if ($grade === 'A') {
                                                        $gradeBadgeClass = 'success';
                                                    } elseif ($grade === 'B') {
                                                        $gradeBadgeClass = 'primary';
                                                    } elseif ($grade === 'C') {
                                                        $gradeBadgeClass = 'warning';
                                                    } elseif ($grade === 'D') {
                                                        $gradeBadgeClass = 'danger';
                                                    }
                                                @endphp
                                                <span class="badge bg-light-{{ $gradeBadgeClass }} text-{{ $gradeBadgeClass }}">
                                                    {{ $grade }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light-{{ $statusBadgeClass }} text-{{ $statusBadgeClass }}">
                                                    {{ $statusText }}
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
                                            <td colspan="8" class="text-center text-muted py-5">
                                                Tidak ada data karyawan untuk ditampilkan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 mt-4">
                            <div class="text-muted small">
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

    <!-- Import Modal -->
    @if(auth()->check() && auth()->user()->is_admin === 'admin')
        <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('kpi.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Import Target KPI Massal</h5>
                            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tahun Penilaian</label>
                                <input type="number" class="form-control" name="year" value="{{ $year }}" min="2020" max="2100" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">File Excel (XLSX, XLS, CSV)</label>
                                <input type="file" class="form-control" name="file_excel" accept=".xlsx,.xls,.csv" required>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="fw-semibold mb-1">Format Kolom Excel:</h6>
                                <p class="mb-0 small">
                                    <code>email</code> / <code>nip</code> / <code>nama_lengkap</code>,
                                    <code>indicator_name</code>,
                                    <code>target_value</code>,
                                    <code>weight</code> (0-100),
                                    <code>realization_value</code> (opsional)
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit">Import Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Grade Distribution Chart
        document.addEventListener('DOMContentLoaded', function() {
            // Grade Chart
            const gradeCtx = document.getElementById('gradeChart').getContext('2d');
            @php
                $gradeDataDefault = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
                $finalGradeData = $gradeDistribution ?? $gradeDataDefault;
            @endphp
            const gradeData = {!! json_encode($finalGradeData) !!};
            new Chart(gradeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Grade A', 'Grade B', 'Grade C', 'Grade D'],
                    datasets: [{
                        data: [gradeData.A, gradeData.B, gradeData.C, gradeData.D],
                        backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Status Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const totalPegawai = {{ (int) ($totalPegawai ?? 0) }};
            const sudahDinilai = {{ (int) ($sudahDinilai ?? 0) }};
            const belumDinilai = {{ (int) ($belumDinilai ?? 0) }};
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: ['Sudah Dinilai (Finalized)', 'Belum Dinilai'],
                    datasets: [{
                        data: [sudahDinilai, belumDinilai],
                        backgroundColor: ['#10c469', '#3b3e66'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
