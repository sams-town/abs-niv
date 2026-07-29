@extends('templates.app')
@section('container')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card shadow-sm border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        
                        @if($status == 'success')
                            <div class="mb-4">
                                <i class="fa fa-check-circle text-success" style="font-size: 80px;"></i>
                            </div>
                            <h2 class="text-success fw-bold mb-3">Scan Berhasil!</h2>
                            <p class="text-muted fs-5 mb-4">{{ $message }}</p>
                        @elseif($status == 'duplicate')
                            <div class="mb-4">
                                <i class="fa fa-exclamation-triangle text-warning" style="font-size: 80px;"></i>
                            </div>
                            <h2 class="text-warning fw-bold mb-3">Sudah Scan</h2>
                            <p class="text-muted fs-5 mb-4">{{ $message }}</p>
                        @endif

                        <div class="card bg-light border-0 mb-4 text-start">
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <th width="35%" class="text-muted">Nama Petugas</th>
                                        <td class="fw-bold">{{ auth()->user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Lokasi Patroli</th>
                                        <td class="fw-bold">{{ $lokasi->nama_lokasi }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Tanggal</th>
                                        <td class="fw-bold">{{ $patroli->tanggal ?? $lastScan->tanggal }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Waktu</th>
                                        <td class="fw-bold">{{ $patroli->jam ?? $lastScan->jam }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <a href="{{ url('/patroli') }}" class="btn btn-primary btn-lg w-100 rounded-pill">
                            Kembali ke Riwayat Patroli
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
