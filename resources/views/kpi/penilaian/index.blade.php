@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="alert alert-info">
            <h5><i class="fa fa-info-circle"></i> Penilaian KPI Periode Berjalan</h5>
            <p class="mb-0">Periode Aktif saat ini: <strong>{{ $activePeriod ? $activePeriod->name . ' (' . \Carbon\Carbon::parse($activePeriod->start_date)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($activePeriod->end_date)->format('d M Y') . ')' : 'Tidak Ada Periode Aktif' }}</strong></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ $title }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="mytable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-white">Kategori</th>
                                <th class="text-white">Nama KPI</th>
                                <th class="text-white">Target</th>
                                <th class="text-white">Bobot</th>
                                <th class="text-white">Capaian Anda</th>
                                <th class="text-white">Skor</th>
                                <th class="text-white">Status</th>
                                <th class="text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalScore = 0; $totalWeight = 0; @endphp
                            @foreach ($assignments as $assign)
                                @php
                                    $submission = $assign->submissions->first();
                                    if ($submission && $submission->status == 'Approved') {
                                        $totalScore += $submission->score;
                                    }
                                    $totalWeight += $assign->weight;
                                @endphp
                            <tr>
                                <td>{{ $assign->kpi->category->name }}</td>
                                <td>
                                    <strong>{{ $assign->kpi->name }}</strong><br>
                                    <small class="text-muted">{{ $assign->kpi->type }}</small>
                                </td>
                                <td class="text-center">{{ $assign->kpi->target_value }} {{ $assign->kpi->unit }}</td>
                                <td class="text-center">{{ number_format($assign->weight, 2) }}%</td>
                                <td class="text-center">
                                    @if($submission)
                                        {{ $submission->actual_value }} {{ $assign->kpi->unit }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($submission)
                                        <strong>{{ number_format($submission->score, 2) }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($submission)
                                        @if($submission->status == 'Submitted')
                                            <span class="badge bg-warning">Menunggu Approval</span>
                                        @elseif($submission->status == 'Approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($submission->status == 'Rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                            <br><small class="text-danger">{{ $submission->feedback }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Belum Diisi</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activePeriod)
                                        @if(!$submission || $submission->status == 'Rejected')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inputModal{{ $assign->id }}">Input Pencapaian</button>
                                        @else
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#inputModal{{ $assign->id }}">Lihat/Edit</button>
                                        @endif
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Periode Tutup</button>
                                    @endif
                                </td>
                            </tr>

                            @if($activePeriod)
                            <!-- Input Modal -->
                            <div class="modal fade" id="inputModal{{ $assign->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ url('/kpi/penilaian/submit') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="kpi_assignment_id" value="{{ $assign->id }}">
                                            <input type="hidden" name="period_id" value="{{ $activePeriod->id }}">
                                            
                                            <div class="modal-header">
                                                <h5 class="modal-title">Input Pencapaian KPI</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-secondary">
                                                    <strong>KPI:</strong> {{ $assign->kpi->name }}<br>
                                                    <strong>Target:</strong> {{ $assign->kpi->target_value }} {{ $assign->kpi->unit }}
                                                </div>
                                                
                                                <div class="form-group mb-3">
                                                    <label>Nilai Pencapaian (Actual Value)</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control" name="actual_value" value="{{ $submission ? $submission->actual_value : '' }}" required>
                                                        <span class="input-group-text">{{ $assign->kpi->unit }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group mb-3">
                                                    <label>Catatan Pendukung (Opsional)</label>
                                                    <textarea class="form-control" name="notes" rows="3">{{ $submission ? $submission->notes : '' }}</textarea>
                                                </div>
                                                
                                                <div class="form-group mb-3">
                                                    <label>Upload Bukti (Opsional)</label>
                                                    <input type="file" class="form-control" name="attachment">
                                                    @if($submission && $submission->attachment)
                                                        <small><a href="{{ asset('storage/'.$submission->attachment) }}" target="_blank">Lihat file saat ini</a></small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                @if(!$submission || $submission->status != 'Approved')
                                                <button type="submit" class="btn btn-primary">Submit Penilaian</button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Bobot & Skor Akhir:</th>
                                <th class="text-center">{{ number_format($totalWeight, 2) }}%</th>
                                <th></th>
                                <th class="text-center bg-light">
                                    <h4 class="mb-0 text-primary">{{ number_format($totalScore, 2) }}</h4>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
