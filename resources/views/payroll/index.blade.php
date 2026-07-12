@extends('templates.dashboard')
@section('isi')
@php
    $bulanList = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $bulanAktif = request('bulan', date('n'));
    $tahunAktif = request('tahun', date('Y'));
    $totalGaji  = $data->sum('grand_total');
    $statusPayroll = $data->count() > 0 ? ($data->first()->status ?? 'Draft') : 'Draft';
@endphp

<div style="padding: 24px;">

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="mb-1" style="font-weight:700">Pengelolaan Payroll</h3>
        <p class="text-muted mb-0" style="font-size:14px">Proses gaji bulanan karyawan {{ optional(App\Models\settings::first())->nama_perusahaan ?? '' }}</p>
    </div>

    {{-- Filter + Actions --}}
    <form action="{{ url('/payroll') }}" method="GET" id="filterForm">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <label class="mb-0 fw-semibold" style="font-size:14px">Bulan</label>
                <select name="bulan" class="form-select form-select-sm" style="width:130px" onchange="document.getElementById('filterForm').submit()">
                    @foreach($bulanList as $id => $nama)
                        @if($id == 0) @continue @endif
                        <option value="{{ $id }}" {{ $bulanAktif == $id ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="mb-0 fw-semibold" style="font-size:14px">Tahun</label>
                <select name="tahun" class="form-select form-select-sm" style="width:90px" onchange="document.getElementById('filterForm').submit()">
                    @for($y = date('Y'); $y >= date('Y')-10; $y--)
                        <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/payroll?bulan='.$bulanAktif.'&tahun='.$tahunAktif.'&export=excel') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i data-feather="file-text" style="width:15px"></i> Export Excel
            </a>
            <a href="{{ url('/payroll?bulan='.$bulanAktif.'&tahun='.$tahunAktif.'&export=pdf') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i data-feather="file" style="width:15px"></i> Export PDF
            </a>
            @can('admin')
            <a href="{{ url('/rekap-data') }}" class="btn btn-sm d-flex align-items-center gap-1" style="background:#1a7a4a;color:white;border-radius:8px">
                <i data-feather="lock" style="width:15px"></i> Generate &amp; Lock Payroll
            </a>
            @endcan
        </div>
    </div>
    </form>

    {{-- Summary Cards --}}
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="p-4 rounded-3 text-white d-flex flex-column justify-content-between" style="background:linear-gradient(135deg,#1a9e5c,#0d7a44);min-height:120px">
                <div class="d-flex align-items-center gap-2 mb-2" style="font-size:14px;opacity:.9">
                    <i data-feather="dollar-sign" style="width:16px"></i>
                    <span>Total Pengeluaran Gaji Bulan Ini</span>
                </div>
                <div style="font-size:32px;font-weight:800;letter-spacing:-1px">
                    Rp {{ number_format($totalGaji, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 rounded-3 border bg-white d-flex flex-column justify-content-between" style="min-height:120px">
                <div class="fw-semibold mb-3" style="font-size:15px">Status Payroll</div>
                @if($data->count() > 0)
                    <span class="badge rounded-pill px-3 py-2" style="font-size:13px;background:#ffc107;color:#333">
                        {{ ucfirst($statusPayroll) }}
                    </span>
                @else
                    <span class="badge rounded-pill px-3 py-2" style="font-size:13px;background:#e9ecef;color:#666">
                        Belum Ada Data
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead>
                        <tr style="border-bottom:2px solid #f0f0f0">
                            <th class="px-4 py-3 text-muted fw-normal" style="white-space:nowrap">NIP</th>
                            <th class="py-3 text-muted fw-normal">Nama Karyawan</th>
                            <th class="py-3 text-muted fw-normal">Divisi</th>
                            <th class="py-3 text-muted fw-normal text-center" colspan="3">Kehadiran</th>
                            <th class="py-3 text-muted fw-normal">Gaji Pokok</th>
                            <th class="py-3 text-muted fw-normal">Tunjangan</th>
                            <th class="py-3 text-muted fw-normal">Potongan</th>
                            <th class="py-3 text-muted fw-normal fw-semibold">Gaji Bersih</th>
                            @can('admin')<th class="py-3 text-muted fw-normal text-center">Aksi</th>@endcan
                        </tr>
                        <tr style="background:#f8f9fa;border-bottom:1px solid #eee">
                            <th colspan="3" class="px-4"></th>
                            <th class="py-2 text-center" style="font-size:11px;color:#1a9e5c;font-weight:600">Hadir</th>
                            <th class="py-2 text-center" style="font-size:11px;color:#e67e22;font-weight:600">Lambat</th>
                            <th class="py-2 text-center" style="font-size:11px;color:#e74c3c;font-weight:600">Alpa</th>
                            <th colspan="5" @can('admin')colspan="5"@endcan></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $d)
                        @php
                            $hadir  = $d->jumlahHadir($d->user_id, $d->bulan, $d->tahun, 'Masuk');
                            $lambat = $d->jumlahTelat($d->user_id, $d->bulan, $d->tahun);
                            $alpa   = $d->jumlahHadir($d->user_id, $d->bulan, $d->tahun, 'Alfa');
                            $tunjangan  = ($d->total_tunjangan_transport ?? 0) + ($d->total_tunjangan_makan ?? 0)
                                        + ($d->total_tunjangan_bpjs_kesehatan ?? 0) + ($d->total_tunjangan_bpjs_ketenagakerjaan ?? 0)
                                        + ($d->bonus_pribadi ?? 0) + ($d->bonus_team ?? 0) + ($d->total_kehadiran ?? 0) + ($d->total_lembur ?? 0);
                            $potongan   = ($d->total_potongan_bpjs_kesehatan ?? 0) + ($d->total_potongan_bpjs_ketenagakerjaan ?? 0)
                                        + ($d->total_mangkir ?? 0) + ($d->total_terlambat ?? 0) + ($d->bayar_kasbon ?? 0) + ($d->loss ?? 0);
                        @endphp
                        <tr style="border-bottom:1px solid #f5f5f5">
                            <td class="px-4 py-3" style="color:#888;font-size:12px;white-space:nowrap">{{ $d->user->nip ?? '-' }}</td>
                            <td class="py-3" style="font-weight:500">{{ $d->user->name ?? '-' }}</td>
                            <td class="py-3" style="color:#666">{{ optional(optional($d->user)->Jabatan)->nama_jabatan ?? '-' }}</td>
                            <td class="py-3 text-center" style="color:#1a9e5c;font-weight:500">{{ $hadir }}</td>
                            <td class="py-3 text-center" style="color:#e67e22">{{ $lambat }}</td>
                            <td class="py-3 text-center" style="color:#e74c3c">{{ $alpa }}</td>
                            <td class="py-3">Rp {{ number_format($d->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="py-3">Rp {{ number_format($tunjangan, 0, ',', '.') }}</td>
                            <td class="py-3" style="color:#e74c3c">Rp {{ number_format($potongan, 0, ',', '.') }}</td>
                            <td class="py-3" style="font-weight:700">Rp {{ number_format($d->grand_total, 0, ',', '.') }}</td>
                            @can('admin')
                            <td class="py-3 text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ url('/payroll/'.$d->id.'/download') }}" target="_blank" class="btn btn-xs btn-outline-primary" title="Cetak Slip">
                                        <i data-feather="printer" style="width:13px"></i>
                                    </a>
                                    <a href="{{ url('/payroll/'.$d->id.'/edit') }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                        <i data-feather="edit-2" style="width:13px"></i>
                                    </a>
                                    <form action="{{ url('/payroll/'.$d->id.'/delete') }}" method="post" class="d-inline delete-form">
                                        @method('delete') @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus">
                                            <i data-feather="trash-2" style="width:13px"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endcan
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i data-feather="inbox" style="width:32px;height:32px;opacity:.3" class="d-block mx-auto mb-2"></i>
                                Belum ada data payroll untuk {{ $bulanList[$bulanAktif] ?? '' }} {{ $tahunAktif }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($data->count() > 0)
                    <tfoot>
                        <tr style="background:#f8f9fa;border-top:2px solid #eee">
                            <td colspan="9" class="px-4 py-3 fw-semibold text-end">Total Seluruh Gaji Bersih:</td>
                            <td class="py-3" style="font-weight:800;font-size:15px;color:#1a7a44">Rp {{ number_format($totalGaji, 0, ',', '.') }}</td>
                            @can('admin')<td></td>@endcan
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($data->hasPages())
            <div class="d-flex justify-content-end px-4 py-3">
                {{ $data->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

@push('script')
<script>
document.querySelectorAll('.delete-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus data payroll ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
@endsection
