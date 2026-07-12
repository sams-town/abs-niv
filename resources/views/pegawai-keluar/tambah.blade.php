@extends('templates.dashboard')
@section('isi')

<style>
    .pk-form-wrapper {
        max-width: 760px;
        margin: 0 auto;
    }
    .pk-form-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 50%, #1a8cff 100%);
        border-radius: 20px 20px 0 0;
        padding: 28px 32px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .pk-form-header::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .pk-form-header h4 {
        font-weight: 700;
        font-size: 1.4rem;
        margin: 0;
        letter-spacing: 0.3px;
    }
    .pk-form-header p {
        margin: 4px 0 0;
        opacity: 0.82;
        font-size: 0.85rem;
    }
    .pk-form-body {
        background: #fff;
        border-radius: 0 0 20px 20px;
        padding: 32px;
        box-shadow: 0 8px 32px rgba(30, 58, 95, 0.12);
    }
    .pk-section-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f5fb;
    }
    .pk-form-group {
        margin-bottom: 20px;
    }
    .pk-form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pk-form-group label .required-star {
        color: #ef4444;
        margin-left: 2px;
    }
    .pk-input {
        width: 100%;
        padding: 11px 15px;
        border: 1.5px solid #dde3ed;
        border-radius: 10px;
        font-size: 0.9rem;
        color: #374151;
        background: #f8fafc;
        transition: all 0.2s ease;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
    }
    .pk-input:focus {
        border-color: #2d6a9f;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(45, 106, 159, 0.12);
    }
    .pk-input.is-invalid {
        border-color: #ef4444;
        background: #fff5f5;
    }
    .pk-select-wrapper {
        position: relative;
    }
    .pk-select-wrapper::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
        font-size: 0.9rem;
    }
    .pk-select-wrapper .pk-input {
        padding-right: 40px;
        cursor: pointer;
    }
    /* Custom Jenis Keberhentian Cards */
    .jenis-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .jenis-card-option {
        position: relative;
    }
    .jenis-card-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .jenis-card-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .jenis-card-label:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }
    .jenis-card-option input[type="radio"]:checked + .jenis-card-label {
        border-color: #2d6a9f;
        background: #eff6ff;
        box-shadow: 0 0 0 3px rgba(45, 106, 159, 0.1);
    }
    .jenis-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .jenis-card-text strong {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e3a5f;
    }
    .jenis-card-text small {
        font-size: 0.75rem;
        color: #94a3b8;
    }
    .pk-file-area {
        border: 2px dashed #dde3ed;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.2s;
        cursor: pointer;
    }
    .pk-file-area:hover {
        border-color: #2d6a9f;
        background: #eff6ff;
    }
    .pk-file-area input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }
    .pk-submit-btn {
        background: linear-gradient(135deg, #1e3a5f, #2d6a9f, #1a8cff);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 13px 36px;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .pk-submit-btn:hover {
        box-shadow: 0 6px 20px rgba(45, 106, 159, 0.4);
        transform: translateY(-2px);
        color: white;
    }
    .pk-back-btn {
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 12px;
        padding: 13px 28px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .pk-back-btn:hover {
        background: #e2e8f0;
        color: #1e3a5f;
    }
    .invalid-feedback {
        font-size: 0.78rem;
        color: #ef4444;
        margin-top: 5px;
    }
</style>

<div class="row">
    <div class="col-12 pk-form-wrapper">
        <div class="pk-form-header">
            <h4><i class="fa fa-user-minus me-2"></i>Tambah Pegawai Keluar</h4>
            <p>Isi formulir di bawah untuk mendaftarkan pegawai yang keluar dari instansi</p>
        </div>
        <div class="pk-form-body">
            <form method="post" action="{{ url('/exit/store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Informasi Pegawai -->
                <div class="pk-section-title"><i class="fa fa-user me-2"></i>Informasi Pegawai</div>

                <div class="pk-form-group">
                    <label>Nama Pegawai <span class="required-star">*</span></label>
                    <div class="pk-select-wrapper">
                        <select class="pk-input @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('user_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="pk-form-group">
                    <label>Tanggal Keluar <span class="required-star">*</span></label>
                    <input type="date" class="pk-input @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal') }}">
                    @error('tanggal')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jenis Keberhentian -->
                <div class="pk-section-title mt-4"><i class="fa fa-tag me-2"></i>Jenis Keberhentian</div>
                <div class="pk-form-group">
                    <div class="jenis-cards">
                        <div class="jenis-card-option">
                            <input type="radio" id="jenis_phk" name="jenis" value="PHK" {{ old('jenis') == 'PHK' ? 'checked' : '' }}>
                            <label class="jenis-card-label" for="jenis_phk">
                                <div class="jenis-card-icon" style="background:#fee2e2; color:#b91c1c;">
                                    <i class="fa fa-ban"></i>
                                </div>
                                <div class="jenis-card-text">
                                    <strong>PHK</strong>
                                    <small>Pemutusan Hubungan Kerja</small>
                                </div>
                            </label>
                        </div>
                        <div class="jenis-card-option">
                            <input type="radio" id="jenis_mundur" name="jenis" value="Mengundurkan Diri" {{ old('jenis') == 'Mengundurkan Diri' ? 'checked' : '' }}>
                            <label class="jenis-card-label" for="jenis_mundur">
                                <div class="jenis-card-icon" style="background:#fef3c7; color:#92400e;">
                                    <i class="fa fa-door-open"></i>
                                </div>
                                <div class="jenis-card-text">
                                    <strong>Mengundurkan Diri</strong>
                                    <small>Atas permintaan sendiri</small>
                                </div>
                            </label>
                        </div>
                        <div class="jenis-card-option">
                            <input type="radio" id="jenis_meninggal" name="jenis" value="Meninggal Dunia" {{ old('jenis') == 'Meninggal Dunia' ? 'checked' : '' }}>
                            <label class="jenis-card-label" for="jenis_meninggal">
                                <div class="jenis-card-icon" style="background:#ede9fe; color:#5b21b6;">
                                    <i class="fa fa-cross"></i>
                                </div>
                                <div class="jenis-card-text">
                                    <strong>Meninggal Dunia</strong>
                                    <small>Wafat dalam masa aktif</small>
                                </div>
                            </label>
                        </div>
                        <div class="jenis-card-option">
                            <input type="radio" id="jenis_pensiun" name="jenis" value="Pensiun" {{ old('jenis') == 'Pensiun' ? 'checked' : '' }}>
                            <label class="jenis-card-label" for="jenis_pensiun">
                                <div class="jenis-card-icon" style="background:#dcfce7; color:#166534;">
                                    <i class="fa fa-award"></i>
                                </div>
                                <div class="jenis-card-text">
                                    <strong>Pensiun</strong>
                                    <small>Masa kerja telah selesai</small>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('jenis')
                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alasan -->
                <div class="pk-section-title mt-4"><i class="fa fa-align-left me-2"></i>Keterangan</div>

                <div class="pk-form-group">
                    <label>Alasan Keberhentian <span class="required-star">*</span></label>
                    <textarea name="alasan" id="alasan" class="pk-input @error('alasan') is-invalid @enderror" rows="4" placeholder="Jelaskan alasan keberhentian pegawai...">{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- File Upload -->
                <div class="pk-form-group">
                    <label>Dokumen Pendukung (Opsional)</label>
                    <div style="position: relative;">
                        <div class="pk-file-area" id="fileArea">
                            <i class="fa fa-cloud-upload-alt fa-2x mb-2" style="color:#94a3b8;"></i>
                            <p style="color:#64748b; margin: 0; font-size:0.88rem;" id="fileLabel">Klik atau seret file ke sini</p>
                            <small style="color:#94a3b8;">Mendukung PDF, JPG, PNG (maks. 10MB)</small>
                            <input type="file" id="pegawai_keluar_file_path" name="pegawai_keluar_file_path"
                                style="position:absolute; width:100%; height:100%; top:0; left:0; opacity:0; cursor:pointer;"
                                onchange="updateFileName(this)">
                        </div>
                    </div>
                    @error('pegawai_keluar_file_path')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 2px solid #f0f5fb;">
                    <a href="{{ url('/exit') }}" class="pk-back-btn">
                        <i class="fa fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="pk-submit-btn">
                        <i class="fa fa-save me-2"></i>Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    function updateFileName(input) {
        const label = document.getElementById('fileLabel');
        if (input.files && input.files[0]) {
            label.textContent = '📎 ' + input.files[0].name;
            label.style.color = '#2d6a9f';
            label.style.fontWeight = '600';
        }
    }
</script>
@endpush
@endsection
