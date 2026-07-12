@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $title }}</h4>
                <small class="text-muted">Kelola penggajian dosen berdasarkan sesi mengajar yang telah diverifikasi</small>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center gap-2">
                    <i data-feather="info" style="width:18px"></i>
                    <span>Halaman ini akan menampilkan rekap penggajian dosen dari laporan sesi mengajar yang sudah <strong>Approved</strong>. Silakan proses di menu <a href="{{ url('/admin/token-verifikasi') }}">Verifikasi Token</a> terlebih dahulu.</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th><th>Nama Dosen</th><th>NIDN</th><th>Bulan</th><th>Jumlah Sesi</th><th>Total Honorarium</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="7" class="text-muted py-4">Belum ada data payroll dosen.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
