@extends('templates.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Penilaian KPI</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/kpi') }}">Manajemen KPI</a></li>
                        <li class="breadcrumb-item active">{{ $user->name ?? 'Pegawai' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Employee Info -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Informasi Pegawai</h5>
                        <div class="mb-3">
                            <div class="fw-semibold">{{ $user->name ?? '-' }}</div>
                            <div class="text-muted small">{{ $user->email ?? '-' }}</div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small">Jabatan:</span>
                            <div class="fw-semibold">{{ optional($user->Jabatan)->nama_jabatan ?? 'Belum diatur' }}</div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small">Lokasi:</span>
                            <div class="fw-semibold">{{ optional($user->Lokasi)->nama_lokasi ?? 'Belum diatur' }}</div>
                        </div>
                        <div class="mb-0">
                            <span class="text-muted small">Tahun Penilaian:</span>
                            <div class="fw-semibold">{{ $year }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target KPI -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Target KPI Kuantitatif</h5>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addTargetModal">
                            <i class="fa fa-plus"></i> Tambah Target
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Indikator KPI</th>
                                        <th class="text-center">Target</th>
                                        <th class="text-center">Realisasi</th>
                                        <th class="text-center">Bobot (%)</th>
                                        <th class="text-center">Skor Hitung</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kpiTargets ?? [] as $target)
                                        <tr>
                                            <td>{{ $target->indicator_name }}</td>
                                            <td class="text-center">{{ number_format($target->target_value, 2) }}</td>
                                            <td class="text-center">
                                                <form action="{{ url('/kpi/target/' . $target->id . '/update') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <div class="input-group input-group-sm" style="max-width: 150px; margin: 0 auto;">
                                                        <input type="number" class="form-control" name="realization_value" value="{{ $target->realization_value }}" step="0.01" min="0" required>
                                                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-save"></i></button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-center">{{ number_format($target->weight, 2) }}</td>
                                            <td class="text-center">
                                                <span class="fw-semibold">
                                                    {{ number_format($target->calculated_score ?? 0, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-muted small">-</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada target KPI untuk tahun ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($kpiTargets->count() > 0)
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end fw-semibold">Total Skor Kuantitatif (Bobot 70%):</td>
                                            <td class="text-center fw-bold">
                                                {{ number_format(min($kpiTargets->sum('calculated_score'), 70), 2) }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Penilaian Kualitatif -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h5 class="mb-0">Penilaian Kualitatif (HR)</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/kpi/evaluation/' . $evaluation->id . '/save') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Skor Kedisiplinan (0-100)</label>
                                    <input type="number" class="form-control" name="discipline_score" value="{{ $evaluation->discipline_score ?? 0 }}" min="0" max="100" required>
                                    <small class="text-muted">Bobot: 15%</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Skor Inisiatif (0-100)</label>
                                    <input type="number" class="form-control" name="initiative_score" value="{{ $evaluation->initiative_score ?? 0 }}" min="0" max="100" required>
                                    <small class="text-muted">Bobot: 15%</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan HR</label>
                                <textarea class="form-control" name="hr_notes" rows="4">{{ $evaluation->hr_notes }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status Penilaian</label>
                                <select class="form-select" name="status" required>
                                    <option value="draft" {{ $evaluation->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ $evaluation->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="approved" {{ $evaluation->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="finalized" {{ $evaluation->status === 'finalized' ? 'selected' : '' }}>Finalized</option>
                                </select>
                            </div>

                            <!-- Hasil Perhitungan -->
                            <div class="alert alert-info mb-4">
                                <h6 class="fw-semibold mb-2">Hasil Perhitungan Akhir</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Total Kuantitatif (Normalisasi 0-70)</small>
                                        <h5 class="mb-0">{{ number_format(min($kpiTargets->sum('calculated_score'), 70), 2) }}</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Total Kualitatif (15% + 15%)</small>
                                        <h5 class="mb-0">{{ number_format(($evaluation->discipline_score * 0.15) + ($evaluation->initiative_score * 0.15), 2) }}</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Skor Akhir & Grade</small>
                                        <h5 class="mb-0">
                                            {{ number_format($evaluation->final_score ?? 0, 2) }}
                                            <span class="badge bg-light-primary text-primary ms-1">
                                                {{ $evaluation->grade ?? '-' }}
                                            </span>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ url('/kpi') }}" class="btn btn-light">Kembali</a>
                                <button class="btn btn-primary ms-auto" type="submit">
                                    <i class="fa fa-save me-2"></i>Simpan Penilaian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Target Modal -->
    <div class="modal fade" id="addTargetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/kpi/target/add') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Target KPI</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <div class="mb-3">
                            <label class="form-label">Nama Indikator KPI</label>
                            <input type="text" class="form-control" name="indicator_name" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Target Nilai</label>
                                <input type="number" class="form-control" name="target_value" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bobot (%)</label>
                                <input type="number" class="form-control" name="weight" step="0.01" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Tambah Target</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
