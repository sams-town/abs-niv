@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $title }}</h4>
                <small class="text-muted">Cetak dan distribusikan slip gaji dosen</small>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i data-feather="file-text" style="width:16px"></i> Slip gaji dosen akan tersedia setelah proses payroll dosen diselesaikan.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
