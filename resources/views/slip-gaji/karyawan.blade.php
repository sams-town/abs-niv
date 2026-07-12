@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $title }}</h4>
                <small class="text-muted">Cetak dan distribusikan slip gaji karyawan</small>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i data-feather="file" style="width:16px"></i> Slip gaji karyawan diambil dari data payroll yang sudah diproses di menu <a href="{{ url('/payroll') }}">Proses Payroll</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
