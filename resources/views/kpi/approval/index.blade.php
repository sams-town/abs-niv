@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="text-white mb-0">Menunggu Approval</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable-kpi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Karyawan</th>
                                <th>Periode</th>
                                <th>KPI</th>
                                <th>Target</th>
                                <th>Actual</th>
                                <th>Skor</th>
                                <th>Bukti</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $sub)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sub->user->name }}</td>
                                <td>{{ $sub->period->name }}</td>
                                <td>{{ $sub->assignment->kpi->name }}</td>
                                <td>{{ $sub->assignment->kpi->target_value }} {{ $sub->assignment->kpi->unit }}</td>
                                <td>{{ $sub->actual_value }} {{ $sub->assignment->kpi->unit }}</td>
                                <td><strong>{{ number_format($sub->score, 2) }}</strong></td>
                                <td>
                                    @if($sub->attachment)
                                        <a href="{{ asset('storage/'.$sub->attachment) }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-download"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ url('/kpi/approval/'.$sub->id.'/approve') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve pencapaian ini?')"><i class="fa fa-check"></i></button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $sub->id }}"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                            
                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ url('/kpi/approval/'.$sub->id.'/reject') }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pencapaian KPI</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Alasan Penolakan / Revisi</label>
                                                    <textarea name="feedback" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak & Minta Revisi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($submissions->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada antrean approval KPI.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success">
                <h5 class="text-white mb-0">Riwayat Approval KPI</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable-kpi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Karyawan</th>
                                <th>Periode</th>
                                <th>KPI</th>
                                <th>Target</th>
                                <th>Actual</th>
                                <th>Skor</th>
                                <th>Status</th>
                                <th>Tgl Proses</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $hist)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $hist->user->name }}</td>
                                <td>{{ $hist->period->name }}</td>
                                <td>{{ $hist->assignment->kpi->name }}</td>
                                <td>{{ $hist->assignment->kpi->target_value }} {{ $hist->assignment->kpi->unit }}</td>
                                <td>{{ $hist->actual_value }} {{ $hist->assignment->kpi->unit }}</td>
                                <td><strong>{{ number_format($hist->score, 2) }}</strong></td>
                                <td>
                                    @if($hist->status == 'Approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($hist->approved_at)->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(function () {
        $('.datatable-kpi').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
@endpush
