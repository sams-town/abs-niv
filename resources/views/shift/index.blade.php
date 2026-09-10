@extends('templates.dashboard')
@section('isi')
@push('style')
<style>
    .shift-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; border-left:4px solid #f59e0b; padding:18px; margin-bottom:16px; }
    .shift-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.06); }
    .shift-name { font-size:15px; font-weight:700; color:#0f172a; }
    .shift-time { font-size:12px; color:#64748b; margin-top:3px; }
    .assigned-item { display:flex; align-items:center; justify-content:space-between; padding:8px 10px; background:#f8fafc; border-radius:10px; margin-top:8px; }
    .assigned-name { font-size:13px; font-weight:600; color:#1e293b; }
    .assigned-sub  { font-size:11px; color:#64748b; }
    .badge-lock   { font-size:10px; font-weight:700; padding:2px 7px; border-radius:6px; background:#e0e7ff; color:#4338ca; }
    .badge-unlock { font-size:10px; font-weight:700; padding:2px 7px; border-radius:6px; background:#f1f5f9; color:#64748b; }
    .stat-card { background:#fff; border-radius:12px; padding:16px 20px; border:1px solid #f1f5f9; display:flex; align-items:center; gap:14px; }
    .stat-icon { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-val  { font-size:22px; font-weight:700; color:#1e293b; }
    .stat-lbl  { font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase; }
</style>
@endpush

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Manajemen Shift</h4>
            <small class="text-muted">Kelola jadwal shift &amp; penugasan pegawai</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ url('/shift/create') }}" class="btn btn-primary btn-sm">
                <i data-feather="plus" style="width:14px"></i> Tambah Shift
            </a>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i data-feather="upload" style="width:14px"></i> Import
            </button>
            <a href="{{ url('/shift-management/template') }}" class="btn btn-outline-secondary btn-sm">
                <i data-feather="download" style="width:14px"></i> Template
            </a>
            <a href="{{ url('/pegawai') }}" class="btn btn-dark btn-sm">
                <i data-feather="arrow-left" style="width:14px"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e0e7ff;color:#4f46e5"><i data-feather="clock" style="width:20px"></i></div>
                <div><div class="stat-val">{{ $total_shift }}</div><div class="stat-lbl">Total Shift</div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#dcfce7;color:#16a34a"><i data-feather="users" style="width:20px"></i></div>
                <div><div class="stat-val">{{ $karyawan_aktif }}</div><div class="stat-lbl">Karyawan Aktif</div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon" style="background:#f3e8ff;color:#9333ea"><i data-feather="calendar" style="width:20px"></i></div>
                <div><div class="stat-val">{{ $jadwal_terjadwal }}</div><div class="stat-lbl">Jadwal Terjadwal</div></div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-3" style="max-width:360px">
        <form method="GET" action="{{ url('/shift') }}" class="input-group input-group-sm">
            <input type="text" name="search" class="form-control" placeholder="Cari shift..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i data-feather="search" style="width:14px"></i></button>
            @if(request('search'))
                <a href="{{ url('/shift') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Shift Grid --}}
    <div class="row g-3">
        @forelse ($shifts as $shift)
        <div class="col-md-4">
            <div class="shift-card">
                {{-- Shift Header --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="shift-name">{{ $shift->nama_shift }}</div>
                        <div class="shift-time">
                            <i data-feather="clock" style="width:12px"></i>
                            {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($shift->jam_keluar)->format('H:i') }}
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary py-1 px-2 btn-assign"
                                data-shift-id="{{ $shift->id }}"
                                data-shift-name="{{ $shift->nama_shift }}"
                                data-bs-toggle="modal" data-bs-target="#assignModal"
                                title="Tugaskan Pegawai">
                            <i data-feather="user-plus" style="width:13px"></i>
                        </button>
                        @if($shift->nama_shift !== 'Libur')
                        <a href="{{ url('/shift/'.$shift->id.'/edit') }}" class="btn btn-sm btn-outline-warning py-1 px-2" title="Edit">
                            <i data-feather="edit-2" style="width:13px"></i>
                        </a>
                        <form action="{{ url('/shift/'.$shift->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus shift ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus">
                                <i data-feather="trash-2" style="width:13px"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Assigned List --}}
                <div class="border-top pt-2 mt-1">
                    <small class="text-muted fw-semibold">
                        <i data-feather="users" style="width:12px"></i>
                        {{ count($shift->assigned_employees ?? []) }} Pegawai Ditugaskan
                    </small>
                    <div style="max-height:200px;overflow-y:auto">
                        @forelse ($shift->assigned_employees ?? [] as $emp)
                        <div class="assigned-item">
                            <div>
                                <div class="assigned-name">{{ $emp['user']->name }}</div>
                                <div class="assigned-sub">
                                    {{ $emp['user']->Jabatan->nama_jabatan ?? '-' }} &bull; {{ $emp['range'] }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                @if($emp['lock_location'])
                                    <span class="badge-lock">Lock</span>
                                @else
                                    <span class="badge-unlock">Unlock</span>
                                @endif
                                <form action="{{ url('/shift-management/delete-assignment/'.$emp['mapping_ids']) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus penugasan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link btn-sm text-danger p-0 ms-1">
                                        <i data-feather="x" style="width:14px"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-2" style="font-size:12px">Belum ada penugasan</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i data-feather="inbox" style="width:48px;opacity:.3"></i>
            <div class="mt-2">Tidak ada shift ditemukan</div>
        </div>
        @endforelse
    </div>

</div>

{{-- Modal Assign --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="fw-bold mb-0">Tugaskan ke Shift</h5>
                    <small id="assignShiftName" class="text-primary fw-semibold"></small>
                </div>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/shift-management/assign') }}" method="POST">
                @csrf
                <input type="hidden" name="shift_id" id="assignShiftId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Pegawai / Dosen</label>
                        <select name="user_ids[]" class="form-select form-select-sm" multiple size="6" required>
                            @foreach($all_users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->tipe_user ?? 'pegawai') }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Tahan Ctrl untuk pilih lebih dari satu</small>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="lock_location" value="1" id="lockCheck">
                        <label class="form-check-label fw-semibold small" for="lockCheck">
                            Kunci Lokasi Absensi (Lock Location)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px">
            <div class="modal-header border-0">
                <h5 class="fw-bold mb-0">Import Jadwal Shift</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/shift-management/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center gap-2 py-2 mb-3" style="font-size:13px">
                        <i data-feather="info" style="width:16px;flex-shrink:0"></i>
                        <div>Download template terlebih dahulu, isi data, lalu upload.
                            <a href="{{ url('/shift-management/template') }}" class="fw-semibold">Unduh Template</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">File Excel / CSV</label>
                        <input type="file" name="file_excel" class="form-control form-control-sm" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    feather.replace();

    // Set shift id dan nama saat modal assign dibuka
    document.querySelectorAll('.btn-assign').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('assignShiftId').value   = this.dataset.shiftId;
            document.getElementById('assignShiftName').textContent = this.dataset.shiftName;
        });
    });
</script>
@endpush
@endsection
