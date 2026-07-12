@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ $title }}</h4>
                    <small class="text-muted">Verifikasi token daring yang sudah diinput dosen</small>
                </div>
            </div>
            <div class="card-body">

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i data-feather="check-circle" class="me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i data-feather="x-circle" class="me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Filter --}}
                <form action="{{ url('/admin/token-verifikasi') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama dosen..." value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="semua" {{ $status=='semua'?'selected':'' }}>Semua Status</option>
                                <option value="pending" {{ $status=='pending'?'selected':'' }}>Pending (belum input token)</option>
                                <option value="valid" {{ $status=='valid'?'selected':'' }}>Valid (sudah input, tunggu review)</option>
                                <option value="approved" {{ $status=='approved'?'selected':'' }}>Approved</option>
                                <option value="invalid" {{ $status=='invalid'?'selected':'' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle" style="font-size:13px">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Dosen</th>
                                <th>Mata Kuliah / Kelas</th>
                                <th>Tanggal Sesi</th>
                                <th>Durasi</th>
                                <th>Token Sistem</th>
                                <th>Token Diinput</th>
                                <th>Status</th>
                                <th>Total Gaji</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporan as $i => $lp)
                            @php
                                $sesi  = $lp->sesiDaring;
                                $jadwal = optional($sesi)->jadwal;
                            @endphp
                            <tr>
                                <td>{{ ($laporan->currentPage()-1)*$laporan->perPage()+$i+1 }}</td>
                                <td class="text-start">
                                    <strong>{{ optional($lp->dosen)->name ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ optional($lp->dosen)->nidn ?? '' }}</small>
                                </td>
                                <td class="text-start">
                                    {{ optional($jadwal)->nama_kelas ?? '-' }}<br>
                                    <small class="text-muted">{{ optional(optional($jadwal)->mataKuliah)->nama_mk ?? optional($jadwal)->mata_kuliah ?? '' }}</small>
                                </td>
                                <td>
                                    {{ optional(optional($sesi)->end_time)->format('d/m/Y') ?? '-' }}<br>
                                    <small>{{ optional(optional($sesi)->start_time)->format('H:i') ?? '' }} - {{ optional(optional($sesi)->end_time)->format('H:i') ?? '' }}</small>
                                </td>
                                <td>{{ $lp->durasi_menit }} mnt</td>
                                <td>
                                    @if($sesi && $sesi->token_daring)
                                        <span class="badge bg-secondary font-monospace">{{ $sesi->token_daring }}</span>
                                        <form action="{{ url('/admin/token-verifikasi/regenerate-token/'.optional($sesi)->id) }}" method="POST" class="d-inline mt-1">
                                            @csrf
                                            <button type="button" class="btn btn-xs btn-outline-warning mt-1 btn-regenerate" title="Generate ulang token" data-id="{{ optional($sesi)->id }}">
                                                <i data-feather="refresh-cw" style="width:12px"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lp->token_input)
                                        <span class="badge font-monospace {{ strtoupper($lp->token_input) == strtoupper(optional($sesi)->token_daring ?? '') ? 'bg-success' : 'bg-danger' }}">
                                            {{ $lp->token_input }}
                                        </span>
                                        @if(strtoupper($lp->token_input) == strtoupper(optional($sesi)->token_daring ?? ''))
                                            <br><small class="text-success">✓ Cocok</small>
                                        @else
                                            <br><small class="text-danger">✗ Tidak cocok</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Belum diinput</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeMap = [
                                            'pending'  => 'warning',
                                            'valid'    => 'info',
                                            'approved' => 'success',
                                            'invalid'  => 'danger',
                                        ];
                                        $badge = $badgeMap[$lp->status_pembayaran] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($lp->status_pembayaran) }}</span>
                                </td>
                                <td>
                                    @if($lp->total_gaji > 0)
                                        <strong>Rp {{ number_format($lp->total_gaji, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!in_array($lp->status_pembayaran, ['approved']))
                                    <form action="{{ url('/admin/token-verifikasi/approve/'.$lp->id) }}" method="POST" class="d-inline btn-approve-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-xs" title="Setujui">
                                            <i data-feather="check" style="width:14px"></i> Approve
                                        </button>
                                    </form>
                                    @endif
                                    @if(!in_array($lp->status_pembayaran, ['invalid']))
                                    <form action="{{ url('/admin/token-verifikasi/reject/'.$lp->id) }}" method="POST" class="d-inline btn-reject-form">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs" title="Tolak">
                                            <i data-feather="x" style="width:14px"></i> Tolak
                                        </button>
                                    </form>
                                    @endif
                                    @if(in_array($lp->status_pembayaran, ['approved','invalid']))
                                        <span class="badge bg-secondary">Final</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-2">
                    {{ $laporan->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

@push('script')
<script>
// SweetAlert2 konfirmasi untuk Approve
document.querySelectorAll('.btn-approve-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Setujui Token?',
            text: 'Laporan ini akan ditandai sebagai Approved.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});

// SweetAlert2 konfirmasi untuk Reject
document.querySelectorAll('.btn-reject-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Tolak Token?',
            text: 'Laporan ini akan ditandai sebagai Ditolak.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});

// SweetAlert2 konfirmasi untuk Regenerate Token
document.querySelectorAll('.btn-regenerate').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = btn.closest('form');
        Swal.fire({
            title: 'Generate Ulang Token?',
            text: 'Token lama akan diganti. Dosen harus menggunakan token baru.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Generate Ulang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
@endsection
