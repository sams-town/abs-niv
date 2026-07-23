@extends('templates.dashboard')
@section('isi')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ url('/kpi/dashboard') }}" method="GET" class="row align-items-center">
                    <div class="col-md-3">
                        <label class="fw-bold">Pilih Periode KPI</label>
                    </div>
                    <div class="col-md-6">
                        <select name="period_id" class="form-control selectpicker" onchange="this.form.submit()">
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periods as $p)
                                <option value="{{ $p->id }}" {{ $selectedPeriodId == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} {{ $p->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($selectedPeriodId)
<div class="row">
    <!-- Rata-rata Skor Perusahaan -->
    <div class="col-sm-6 col-xl-6 col-lg-6">
        <div class="card o-hidden border-0">
            <div class="bg-primary b-r-4 card-body">
                <div class="media static-top-widget">
                    <div class="align-self-center text-center"><i data-feather="activity"></i></div>
                    <div class="media-body"><span class="m-0">Rata-rata Skor Perusahaan</span>
                        <h4 class="mb-0 counter">{{ number_format($averageScore, 2) }}</h4><i class="icon-bg" data-feather="activity"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total KPI Disubmit -->
    <div class="col-sm-6 col-xl-6 col-lg-6">
        <div class="card o-hidden border-0">
            <div class="bg-secondary b-r-4 card-body">
                <div class="media static-top-widget">
                    <div class="align-self-center text-center"><i data-feather="file-text"></i></div>
                    <div class="media-body"><span class="m-0">Total Submissions (Approval)</span>
                        <h4 class="mb-0 counter">{{ $totalSubmissions }}</h4><i class="icon-bg" data-feather="file-text"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fa fa-trophy text-warning"></i> Top 5 Karyawan (Leaderboard)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Total Skor KPI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaderboard as $index => $board)
                            <tr>
                                <td>
                                    @if($index == 0) <span class="badge bg-warning"><i class="fa fa-star"></i> 1</span>
                                    @elseif($index == 1) <span class="badge bg-secondary">2</span>
                                    @elseif($index == 2) <span class="badge" style="background-color: #cd7f32;">3</span>
                                    @else {{ $index + 1 }} @endif
                                </td>
                                <td>{{ $board->user->name }}</td>
                                <td>{{ $board->user->Jabatan ? $board->user->Jabatan->nama_jabatan : '-' }}</td>
                                <td><strong>{{ number_format($board->total_score, 2) }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data pencapaian untuk periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    Silakan pilih periode KPI untuk melihat dashboard.
</div>
@endif

@endsection
