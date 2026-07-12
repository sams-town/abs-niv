@extends('templates.app')
@section('container')

<style>
    .pk-user-form {
        padding: 0 0 100px 0;
    }
    .pk-user-header {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c084fc 100%);
        padding: 24px 20px;
        color: white;
        margin-bottom: 20px;
    }
    .pk-user-header h5 {
        font-weight: 700;
        margin: 0;
        font-size: 1.15rem;
    }
    .pk-user-header p {
        margin: 4px 0 0;
        opacity: 0.8;
        font-size: 0.8rem;
    }
    .pk-form-section { padding: 16px 20px; margin-bottom: 12px; }
    .pk-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 2px solid #f0f5fb;
    }
    .pk-field-group { margin-bottom: 16px; }
    .pk-field-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e1e3a;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pk-input-field {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.88rem;
        color: #374151;
        background: #f8fafc;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        transition: all 0.2s;
    }
    .pk-input-field:focus {
        border-color: #7c3aed;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.10);
    }
    .pk-input-field[readonly] { background: #f1f5f9; color: #64748b; cursor: default; }
    .jenis-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .jenis-radio-opt { position: relative; }
    .jenis-radio-opt input[type="radio"] { position: absolute; opacity: 0; width:0; height:0; }
    .jenis-radio-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: #f8fafc;
    }
    .jenis-radio-label:hover { border-color: #c4b5fd; background: #f5f3ff; }
    .jenis-radio-opt input[type="radio"]:checked + .jenis-radio-label {
        border-color: #7c3aed;
        background: #f5f3ff;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }
    .jenis-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .jenis-text strong { display:block; font-size:0.78rem; font-weight:700; color:#1e1e3a; line-height:1.2; }
    .jenis-text small { font-size:0.68rem; color:#94a3b8; }
    .invalid-feedback { font-size:0.75rem; color:#ef4444; margin-top:4px; }
    .pk-file-box {
        border: 2px dashed #dde3ed;
        border-radius: 10px;
        padding: 18px;
        text-align: center;
        background: #f8fafc;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pk-file-box:hover { border-color: #7c3aed; background: #f5f3ff; }
    .pk-file-box input[type="file"] {
        position: absolute;
        top:0; left:0; width:100%; height:100%;
        opacity:0; cursor:pointer;
    }
    .existing-file-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #f0fdf4;
        color: #166534;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #bbf7d0;
        margin-bottom: 10px;
    }
    .pk-sticky-footer {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: white;
        padding: 14px 20px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        display: flex;
        gap: 10px;
        z-index: 99;
    }
    .btn-pk-submit {
        flex: 1;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-pk-submit:hover { box-shadow: 0 4px 16px rgba(124,58,237,0.3); }
    .btn-pk-back {
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 12px;
        padding: 14px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-pk-back:hover { background:#e2e8f0; color:#1e1e3a; }
</style>

<div class="pk-user-form">
    <div class="pk-user-header">
        <h5><i class="fa fa-user-edit me-2"></i>Edit Pengajuan Keluar</h5>
        <p>Perbarui data pengajuan keberhentian Anda</p>
    </div>

    <form method="post" action="{{ url('/exit/update/'.$pegawai_keluar->id) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        <!-- Identitas -->
        <div class="pk-form-section">
            <div class="pk-section-title"><i class="fa fa-user me-1"></i> Identitas</div>
            <div class="pk-field-group">
                <label>Nama Pegawai</label>
                <input type="text" class="pk-input-field" value="{{ $pegawai_keluar->user->name ?? auth()->user()->name }}" readonly>
                <input type="hidden" name="user_id" value="{{ $pegawai_keluar->user_id }}">
            </div>
            <div class="pk-field-group">
                <label>Tanggal Keluar *</label>
                <input type="date" name="tanggal" class="pk-input-field @error('tanggal') is-invalid @enderror"
                    value="{{ old('tanggal', $pegawai_keluar->tanggal) }}">
                @error('tanggal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Jenis Keberhentian -->
        <div class="pk-form-section">
            <div class="pk-section-title"><i class="fa fa-tag me-1"></i> Jenis Keberhentian *</div>
            <div class="jenis-grid">
                <div class="jenis-radio-opt">
                    <input type="radio" id="j_phk" name="jenis" value="PHK"
                        {{ old('jenis', $pegawai_keluar->jenis) == 'PHK' ? 'checked' : '' }}>
                    <label class="jenis-radio-label" for="j_phk">
                        <div class="jenis-icon-box" style="background:#fee2e2;color:#b91c1c;"><i class="fa fa-ban"></i></div>
                        <div class="jenis-text"><strong>PHK</strong><small>Pemutusan Kerja</small></div>
                    </label>
                </div>
                <div class="jenis-radio-opt">
                    <input type="radio" id="j_mundur" name="jenis" value="Mengundurkan Diri"
                        {{ old('jenis', $pegawai_keluar->jenis) == 'Mengundurkan Diri' ? 'checked' : '' }}>
                    <label class="jenis-radio-label" for="j_mundur">
                        <div class="jenis-icon-box" style="background:#fef3c7;color:#92400e;"><i class="fa fa-door-open"></i></div>
                        <div class="jenis-text"><strong>Undur Diri</strong><small>Permintaan sendiri</small></div>
                    </label>
                </div>
                <div class="jenis-radio-opt">
                    <input type="radio" id="j_meninggal" name="jenis" value="Meninggal Dunia"
                        {{ old('jenis', $pegawai_keluar->jenis) == 'Meninggal Dunia' ? 'checked' : '' }}>
                    <label class="jenis-radio-label" for="j_meninggal">
                        <div class="jenis-icon-box" style="background:#ede9fe;color:#5b21b6;"><i class="fa fa-cross"></i></div>
                        <div class="jenis-text"><strong>Meninggal</strong><small>Dalam masa aktif</small></div>
                    </label>
                </div>
                <div class="jenis-radio-opt">
                    <input type="radio" id="j_pensiun" name="jenis" value="Pensiun"
                        {{ old('jenis', $pegawai_keluar->jenis) == 'Pensiun' ? 'checked' : '' }}>
                    <label class="jenis-radio-label" for="j_pensiun">
                        <div class="jenis-icon-box" style="background:#dcfce7;color:#166534;"><i class="fa fa-award"></i></div>
                        <div class="jenis-text"><strong>Pensiun</strong><small>Masa kerja selesai</small></div>
                    </label>
                </div>
            </div>
            @error('jenis')
                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Alasan -->
        <div class="pk-form-section">
            <div class="pk-section-title"><i class="fa fa-align-left me-1"></i> Keterangan</div>
            <div class="pk-field-group">
                <label>Alasan *</label>
                <textarea name="alasan" class="pk-input-field @error('alasan') is-invalid @enderror" rows="4"
                    placeholder="Jelaskan alasan keberhentian...">{{ old('alasan', $pegawai_keluar->alasan) }}</textarea>
                @error('alasan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- File -->
        <div class="pk-form-section">
            <div class="pk-section-title"><i class="fa fa-paperclip me-1"></i> Dokumen Pendukung</div>
            @if ($pegawai_keluar->pegawai_keluar_file_path)
                <a href="{{ url('/storage/'.$pegawai_keluar->pegawai_keluar_file_path) }}" class="existing-file-badge d-inline-flex mb-2" target="_blank">
                    <i class="fa fa-file-download"></i> {{ $pegawai_keluar->pegawai_keluar_file_name }}
                </a><br>
            @endif
            <div class="pk-file-box">
                <i class="fa fa-cloud-upload-alt fa-2x mb-2" style="color:#94a3b8;"></i>
                <p style="color:#64748b; margin:0; font-size:0.82rem;" id="userFileLabel">
                    {{ $pegawai_keluar->pegawai_keluar_file_path ? 'Ganti file (opsional)' : 'Ketuk untuk memilih file' }}
                </p>
                <small style="color:#94a3b8; font-size:0.72rem;">PDF, JPG, PNG (maks. 10MB)</small>
                <input type="file" name="pegawai_keluar_file_path" onchange="updateUserFile(this)">
            </div>
            @error('pegawai_keluar_file_path')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Sticky Actions -->
        <div class="pk-sticky-footer">
            <a href="{{ url('/exit') }}" class="btn-pk-back">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn-pk-submit">
                <i class="fa fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('script')
<script>
    function updateUserFile(input) {
        const label = document.getElementById('userFileLabel');
        if (input.files && input.files[0]) {
            label.textContent = '📎 ' + input.files[0].name;
            label.style.color = '#7c3aed';
            label.style.fontWeight = '600';
        }
    }
</script>
@endpush
@endsection
