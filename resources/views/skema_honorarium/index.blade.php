@extends('templates.dashboard')
@section('isi')
<div style="padding:24px">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 style="font-weight:800;color:#0f172a;margin:0">Master Tarif Mengajar</h3>
            <p class="text-muted mb-0" style="font-size:13px">Atur nominal, tunjangan, dan konfigurasi mengajar SKS dosen</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTarif"
            style="border-radius:10px;font-weight:600;background:#1a9e5c;border:none;padding:10px 20px">
            <i data-feather="plus" style="width:16px"></i> + Tambah Konfigurasi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
    @endif

    {{-- Konfigurasi Per-Dosen --}}
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        <div class="card-body p-0">
            <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2">
                <i data-feather="settings" style="width:16px;color:#1a9e5c"></i>
                <span style="font-weight:700;font-size:14px">Konfigurasi Tarif Aktif</span>
                <small class="text-muted">Daftar konfigurasi aktif yang digunakan untuk perhitungan payroll</small>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th class="px-4 py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Dosen</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Status Ikatan Kerja</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Nominal Per Unit</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Deskripsi</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase;width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dosens->filter(fn($d) => $d->master_skema_honorarium_id || $d->nominal_honor > 0) as $d)
                        <tr style="border-bottom:1px solid #f1f5f9">
                            <td class="px-4 py-3">
                                <div style="font-weight:600;color:#0f172a">{{ $d->name }}</div>
                                <small class="text-muted">{{ $d->nidn ?? '-' }}</small>
                            </td>
                            <td class="py-3">
                                @if($d->status_kepegawaian)
                                    <span class="badge rounded-pill px-3 py-1" style="background:#e0f2fe;color:#0369a1;font-size:12px">
                                        {{ $d->status_kepegawaian }}
                                    </span>
                                @else <span class="text-muted">-</span> @endif
                            </td>
                            <td class="py-3" style="font-weight:800;color:#4f46e5">
                                Rp {{ number_format($d->nominal_honor ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-muted" style="font-size:12px">
                                {{ optional($d->masterSkemaHonorarium)->deskripsi ?? '-' }}
                            </td>
                            <td class="py-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-warning btn-edit-tarif" style="border-radius:8px"
                                        data-id="{{ $d->id }}"
                                        data-nama="{{ $d->name }}"
                                        data-status="{{ $d->status_kepegawaian }}"
                                        data-nominal="{{ $d->nominal_honor ?? 0 }}"
                                        data-bs-toggle="modal" data-bs-target="#modalEditTarif">
                                        <i data-feather="edit-2" style="width:12px"></i> Edit
                                    </button>
                                    <form action="{{ url('/skema-honorarium/'.$d->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius:8px">
                                            <i data-feather="trash-2" style="width:12px"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada konfigurasi tarif. Klik "+ Tambah Konfigurasi" untuk memulai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Master Skema Global --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2">
                <i data-feather="layers" style="width:16px;color:#6366f1"></i>
                <span style="font-weight:700;font-size:14px">Master Skema Global</span>
                <small class="text-muted">Template skema honorarium yang tersedia</small>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th class="px-4 py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">No</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Nama Skema</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Nominal Per Unit</th>
                            <th class="py-3" style="font-weight:700;color:#475569;font-size:11px;text-transform:uppercase">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($skemas as $i => $s)
                        <tr style="border-bottom:1px solid #f1f5f9">
                            <td class="px-4 py-3 text-muted">{{ $i+1 }}</td>
                            <td class="py-3" style="font-weight:600">{{ $s->nama_skema }}</td>
                            <td class="py-3" style="font-weight:800;color:#4f46e5">Rp {{ number_format($s->nominal_per_unit,0,',','.') }}</td>
                            <td class="py-3 text-muted">{{ $s->deskripsi ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada skema global.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ===== MODAL TAMBAH TARIF ===== --}}
<div class="modal fade" id="modalTambahTarif" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15)">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 style="font-weight:800;color:#0f172a;margin:0">
                        <i data-feather="settings" style="width:18px;color:#1a9e5c;margin-right:8px"></i>
                        Tambah Konfigurasi Tarif
                    </h5>
                    <p class="text-muted mb-0" style="font-size:13px;margin-top:4px">Lengkapi rincian tarif dosen di bawah ini. Pastikan satu dosen hanya memiliki satu konfigurasi tarif.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/skema-honorarium') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="fw-semibold mb-1" style="font-size:13px">Dosen</label>
                        <select name="dosen_id" class="form-select" required style="border-radius:10px;border:2px solid #e2e8f0">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosens as $d)
                            <option value="{{ $d->id }}">{{ $d->username ?? $d->name }} - {{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold mb-1" style="font-size:13px">Status Ikatan Kerja</label>
                        <select name="status_kepegawaian" class="form-select" required style="border-radius:10px;border:2px solid #e2e8f0">
                            <option value="">-- Pilih Status --</option>
                            <option value="Dosen Tetap">Dosen Tetap</option>
                            <option value="Dosen Kontrak">Dosen Kontrak</option>
                            <option value="Dosen Luar Biasa (LB)">Dosen Luar Biasa (LB)</option>
                            <option value="Dosen Honorer">Dosen Honorer</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:13px">Gaji Pokok</label>
                            <div class="input-group" style="border-radius:10px;overflow:hidden">
                                <span class="input-group-text" style="background:#f8fafc;border:2px solid #e2e8f0;border-right:0;font-weight:600;color:#64748b">Rp</span>
                                <input type="number" name="gaji_pokok" class="form-control" value="0" min="0" style="border:2px solid #e2e8f0;border-left:0">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:13px">Tunjangan</label>
                            <div class="input-group" style="border-radius:10px;overflow:hidden">
                                <span class="input-group-text" style="background:#f8fafc;border:2px solid #e2e8f0;border-right:0;font-weight:600;color:#64748b">Rp</span>
                                <input type="number" name="tunjangan" class="form-control" value="0" min="0" style="border:2px solid #e2e8f0;border-left:0">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:13px">Tarif Daring / SKS</label>
                            <div class="input-group" style="border-radius:10px;overflow:hidden">
                                <span class="input-group-text" style="background:#f8fafc;border:2px solid #e2e8f0;border-right:0;font-weight:600;color:#64748b">Rp</span>
                                <input type="number" name="tarif_daring" class="form-control" value="75000" min="0" style="border:2px solid #e2e8f0;border-left:0">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:13px">Tarif Luring / SKS</label>
                            <div class="input-group" style="border-radius:10px;overflow:hidden">
                                <span class="input-group-text" style="background:#f8fafc;border:2px solid #e2e8f0;border-right:0;font-weight:600;color:#64748b">Rp</span>
                                <input type="number" name="tarif_luring" class="form-control" value="100000" min="0" style="border:2px solid #e2e8f0;border-left:0">
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_aktif" id="isAktif" class="form-check-input" checked style="border-radius:4px">
                        <label class="form-check-label" for="isAktif" style="font-size:13px">Aktifkan konfigurasi tarif ini untuk perhitungan payroll mendatang</label>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px;font-weight:600">Batal</button>
                    <button type="submit" class="btn" style="background:#1a9e5c;color:white;border-radius:10px;font-weight:700;padding:10px 24px">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT TARIF ===== --}}
<div class="modal fade" id="modalEditTarif" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.15)">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 style="font-weight:800;color:#0f172a;margin:0">
                    <i data-feather="edit" style="width:18px;color:#f59e0b;margin-right:8px"></i>
                    Edit Konfigurasi Tarif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditTarif" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="fw-semibold mb-1" style="font-size:13px">Dosen</label>
                        <input type="text" id="editNamaDosen" class="form-control" disabled style="border-radius:10px;border:2px solid #e2e8f0;background:#f8fafc">
                    </div>
                    <div class="mb-3">
                        <label class="fw-semibold mb-1" style="font-size:13px">Status Ikatan Kerja</label>
                        <select name="status_kepegawaian" id="editStatus" class="form-select" required style="border-radius:10px;border:2px solid #e2e8f0">
                            <option value="Dosen Tetap">Dosen Tetap</option>
                            <option value="Dosen Kontrak">Dosen Kontrak</option>
                            <option value="Dosen Luar Biasa (LB)">Dosen Luar Biasa (LB)</option>
                            <option value="Dosen Honorer">Dosen Honorer</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:13px">Tarif Daring / SKS</label>
                            <div class="input-group" style="border-radius:10px;overflow:hidden">
                                <span class="input-group-text" style="background:#f8fafc;border:2px solid #e2e8f0;border-right:0;font-weight:600;color:#64748b">Rp</span>
                                <input type="number" name="tarif_daring" id="editNominal" class="form-control" min="0" style="border:2px solid #e2e8f0;border-left:0">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:13px">Tarif Luring / SKS</label>
                            <div class="input-group" style="border-radius:10px;overflow:hidden">
                                <span class="input-group-text" style="background:#f8fafc;border:2px solid #e2e8f0;border-right:0;font-weight:600;color:#64748b">Rp</span>
                                <input type="number" name="tarif_luring" class="form-control" value="0" min="0" style="border:2px solid #e2e8f0;border-left:0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:10px;font-weight:600">Batal</button>
                    <button type="submit" class="btn" style="background:#1a9e5c;color:white;border-radius:10px;font-weight:700;padding:10px 24px">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
// Isi modal edit saat klik tombol edit
document.querySelectorAll('.btn-edit-tarif').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('editNamaDosen').value = this.dataset.nama;
        document.getElementById('editStatus').value = this.dataset.status || '';
        document.getElementById('editNominal').value = this.dataset.nominal || 0;
        document.getElementById('formEditTarif').action = '/skema-honorarium/' + id;
    });
});

// Konfirmasi hapus
document.querySelectorAll('.delete-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({ title:'Hapus konfigurasi tarif?', text:'Data tidak dapat dikembalikan.', icon:'warning',
            showCancelButton:true, confirmButtonColor:'#e74c3c', cancelButtonColor:'#6c757d',
            confirmButtonText:'Ya, Hapus', cancelButtonText:'Batal'
        }).then(r => { if(r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
@endsection
