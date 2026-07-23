@extends('templates.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Penilaian KPI {{ $user->name }} - Tahun {{ $year }}</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                        <li class="breadcrumb-item active">KPI</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Target KPI Kuantitatif</h5>
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addTargetModal">
                            <i class="fa fa-plus"></i> Tambah Target
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Indikator KPI</th>
                                        <th>Target</th>
                                        <th>Realisasi</th>
                                        <th>Bobot (%)</th>
                                        <th>Skor Hitungan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kpiTargets as $target)
                                        <tr>
                                            <td>{{ $target->indicator_name }}</td>
                                            <td>{{ number_format($target->target_value, 2) }}</td>
                                            <td>
                                                <form action="{{ url('/kpi/target/' . $target->id . '/update') }}" method="POST">
                                                    @csrf
                                                    <input type="number" name="realization_value" class="form-control" value="{{ $target->realization_value }}" step="0.01" required>
                                                </td>
                                            <td>{{ number_format($target->weight, 2) }}</td>
                                            <td>{{ number_format($target->calculated_score, 2) }}</td>
                                            <td>
                                                    <button class="btn btn-sm btn-primary" type="submit">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <td colspan="4" class="text-end"><strong>Total Skor Kuantitatif:</strong></td>
                                        <td><strong>{{ number_format($kpiTargets->sum('calculated_score'), 2) }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Penilaian Kualitatif (HR)</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/kpi/evaluation/' . $evaluation->id . '/save') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Skor Kedisiplinan (0-100)</label>
                                    <input type="number" class="form-control" name="discipline_score" value="{{ $evaluation->discipline_score }}" min="0" max="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Skor Inisiatif (0-100)</label>
                                    <input type="number" class="form-control" name="initiative_score" value="{{ $evaluation->initiative_score }}" min="0" max="100" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan HR</label>
                                <textarea class="form-control" name="hr_notes" rows="4">{{ $evaluation->hr_notes }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="draft" {{ $evaluation->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ $evaluation->status == 'submitted' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="approved" {{ $evaluation->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="finalized" {{ $evaluation->status == 'finalized' ? 'selected' : '' }}>Finalized</option>
                                </select>
                            </div>

                            <div class="alert alert-info">
                                <h6>Hasil Perhitungan Akhir:</h6>
                                <p><strong>Skor Akhir:</strong> {{ number_format($evaluation->final_score, 2) }}</p>
                                <p><strong>Grade:</strong> {{ $evaluation->grade }}</p>
                            </div>

                            <button class="btn btn-success" type="submit">
                                <i class="fa fa-save"></i> Simpan Penilaian
                            </button>
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
                            <label class="form-label">Nama Indikator</label>
                            <input type="text" class="form-control" name="indicator_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Target Nilai</label>
                            <input type="number" class="form-control" name="target_value" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bobot (%)</label>
                            <input type="number" class="form-control" name="weight" step="0.01" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
