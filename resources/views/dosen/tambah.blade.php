@extends('templates.dashboard')
@section('isi')
    <style>
        .tambah-pegawai-header {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .btn-back-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            transition: all 0.2s;
        }
        .btn-back-circle:hover {
            background: #eff6ff;
            transform: scale(1.05);
        }
        .header-title-wrapper h3 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .header-title-wrapper p {
            font-size: 13px;
            color: #64748b;
            margin: 4px 0 0;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-cancel {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ef4444;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: #fef2f2;
            border-color: #fee2e2;
        }
        .btn-save-submit {
            background: #4f46e5 !important;
            color: #fff !important;
            border-radius: 12px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-save-submit:hover {
            background: #4338ca !important;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
            transform: translateY(-1px);
        }

        /* Tab Navigation */
        .tabs-container {
            background: #ffffff;
            border-radius: 50px;
            padding: 6px;
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow-x: auto;
            white-space: nowrap;
        }
        .tab-btn {
            background: transparent;
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .tab-btn:hover {
            color: #4f46e5;
            background: #f8fafc;
        }
        .tab-btn.active {
            background: #4f46e5;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }

        /* Form Card */
        .form-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            padding: 28px;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .section-title i {
            color: #4f46e5;
        }
        
        /* Form Styling */
        label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border-radius: 12px !important;
            padding: 12px 16px !important;
            font-size: 14px !important;
            border: 1px solid #cbd5e1 !important;
            transition: all 0.2s !important;
            background-color: #f8fafc !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
            outline: none !important;
        }
        .required-star {
            color: #ef4444;
            font-weight: bold;
        }

        /* Image Upload Dropzone */
        .upload-dropzone {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .upload-dropzone:hover {
            border-color: #4f46e5;
            background: #f0fdf4;
        }
        .upload-preview {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            display: none;
        }
        .upload-icon {
            font-size: 32px;
            color: #94a3b8;
        }

        /* Bottom Nav bar */
        .bottom-nav-container {
            background: #ffffff;
            border-radius: 50px;
            padding: 10px 24px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-arrow-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }
        .nav-arrow-btn:hover {
            background: #eff6ff;
            color: #4f46e5;
        }
        .dots-indicator {
            display: flex;
            gap: 8px;
        }
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.3s;
        }
        .dot.active {
            background: #4f46e5;
            width: 24px;
            border-radius: 4px;
        }

        /* Custom Dropdown Styling */
        .custom-dropdown {
            position: relative;
            width: 100%;
        }
        .custom-dropdown-select {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }
        .custom-dropdown-select:hover {
            border-color: #94a3b8;
        }
        .custom-dropdown-select.active {
            border-color: #4f46e5;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .custom-dropdown-selected-text {
            font-weight: 600;
            color: #94a3b8;
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }
        .dropdown-icon {
            color: #94a3b8;
            margin-right: 12px;
            font-size: 16px;
        }
        .arrow-icon {
            color: #64748b;
            font-size: 12px;
            transition: transform 0.2s;
        }
        .custom-dropdown-select.active .arrow-icon {
            transform: rotate(180deg);
        }
        
        .custom-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            overflow: hidden;
            animation: slideDown 0.2s ease-out;
        }
        .custom-dropdown-menu.open {
            display: block;
        }
        
        .custom-dropdown-search {
            position: relative;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .custom-dropdown-search i {
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .custom-dropdown-search input {
            width: 100%;
            border-radius: 30px;
            padding: 8px 16px 8px 36px;
            font-size: 13px;
            border: 1px solid #cbd5e1;
            outline: none;
        }
        .custom-dropdown-search input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }
        
        .custom-dropdown-options {
            max-height: 240px;
            overflow-y: auto;
            padding: 8px 0;
        }
        .custom-dropdown-option {
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
        }
        .custom-dropdown-option:hover {
            background-color: #f1f5f9;
            color: #4f46e5;
        }
        .custom-dropdown-option.selected {
            background-color: #eff6ff;
            color: #4f46e5;
            font-weight: 700;
        }

        /* Checkbox indicator for multiple searchable select */
        .custom-dropdown.multiple .custom-dropdown-option {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            padding-left: 40px !important;
        }
        .custom-dropdown.multiple .custom-dropdown-option::before {
            content: '';
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: #ffffff;
            transition: all 0.2s;
        }
        .custom-dropdown.multiple .custom-dropdown-option.selected::before {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .custom-dropdown.multiple .custom-dropdown-option.selected::after {
            content: '';
            position: absolute;
            left: 21px;
            top: 42%;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <form id="tambahDosenForm" method="POST" action="{{ url('/dosen/store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Top Header Card -->
        <div class="tambah-pegawai-header">
            <div class="header-left">
                <a href="{{ url('/dosen') }}" class="btn-back-circle">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="header-title-wrapper">
                    <h3>Tambah Dosen</h3>
                    <p>Data lengkap dosen baru & sistem honorarium</p>
                </div>
            </div>
            <div class="header-right">
                <a href="{{ url('/dosen') }}" class="btn-cancel">
                    <i class="fa fa-times"></i>
                </a>
                <button type="submit" class="btn-save-submit">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </div>

        <!-- Tab Bar -->
        <div class="tabs-container">
            <button type="button" class="tab-btn active" data-target="data-pribadi" data-index="0">
                <i class="fa fa-user"></i> Data Pribadi
            </button>
            <button type="button" class="tab-btn" data-target="informasi-kontak" data-index="1">
                <i class="fa fa-phone"></i> Informasi Kontak
            </button>
            <button type="button" class="tab-btn" data-target="dokumen" data-index="2">
                <i class="fa fa-file-alt"></i> Dokumen
            </button>
            <button type="button" class="tab-btn" data-target="kontrak-rekening" data-index="3">
                <i class="fa fa-file-contract"></i> Kontrak & Rekening
            </button>
            <button type="button" class="tab-btn" data-target="cuti-izin" data-index="4">
                <i class="fa fa-calendar-check"></i> Cuti & Izin
            </button>
            <button type="button" class="tab-btn" data-target="penjumlahan-gaji" data-index="5">
                <i class="fa fa-chart-line"></i> Penjumlahan Gaji
            </button>
            <button type="button" class="tab-btn" data-target="pengurangan-gaji" data-index="6">
                <i class="fa fa-minus-circle"></i> Pengurangan Gaji
            </button>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <!-- 1. DATA PRIBADI TAB -->
            <div class="tab-content-section" id="section-data-pribadi">
                <div class="section-title">
                    <i class="fa fa-image"></i> Foto Dosen
                </div>
                <div class="row mb-4 justify-content-center">
                    <div class="col-md-6">
                        <div class="upload-dropzone" onclick="document.getElementById('foto_karyawan').click()">
                            <i class="fa fa-image upload-icon" id="dropzone-icon"></i>
                            <img src="" id="avatar-preview" class="upload-preview">
                            <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                <i class="fa fa-camera me-1"></i> Pilih Foto / Kamera
                            </button>
                            <span style="font-size: 11px; color: #94a3b8;">Format file: JPG, PNG (Max 10MB)</span>
                            <input type="file" name="foto_karyawan" id="foto_karyawan" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                    </div>
                </div>

                <div class="section-title mt-4">
                    <i class="fa fa-user"></i> Data Pribadi
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name">Nama Lengkap <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required placeholder="Nama lengkap">
                    </div>
                    <div class="col-md-3">
                        <label for="gelar_depan">Gelar Depan</label>
                        <input type="text" class="form-control" name="gelar_depan" id="gelar_depan" placeholder="Dr., Prof.">
                    </div>
                    <div class="col-md-3">
                        <label for="gelar_belakang">Gelar Belakang</label>
                        <input type="text" class="form-control" name="gelar_belakang" id="gelar_belakang" placeholder="M.Kom, Ph.D">
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email <span class="required-star">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" required placeholder="name@domain.com">
                    </div>
                    <div class="col-md-6">
                        <label for="telepon">HP <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="telepon" id="telepon" required placeholder="Nomor handphone aktif">
                    </div>
                    <div class="col-md-6">
                        <label for="username">Username <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="username" id="username" required placeholder="Username login">
                    </div>
                    <div class="col-md-6">
                        <label for="password">Password <span class="required-star">*</span></label>
                        <input type="password" class="form-control" name="password" id="password" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="col-md-6">
                        <label for="lokasi_id">Lokasi <span class="required-star">*</span></label>
                        <select name="lokasi_id" id="lokasi_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Lokasi Kantor</option>
                            @foreach ($lokasi as $dl)
                                <option value="{{ $dl->id }}">{{ $dl->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_lahir">Tgl Lahir <span class="required-star">*</span></label>
                        <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
                    </div>
                    <div class="col-md-6">
                        <label for="gender">Jenis Kelamin <span class="required-star">*</span></label>
                        <select name="gender" id="gender" class="form-select" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_join">Tgl Masuk <span class="required-star">*</span></label>
                        <input type="date" class="form-control" name="tgl_join" id="tgl_join" required onchange="calculateMasaKerja()">
                    </div>
                    <div class="col-md-6">
                        <label for="masa_kerja">Masa Kerja</label>
                        <input type="text" class="form-control" id="masa_kerja" readonly placeholder="Akan terhitung otomatis...">
                    </div>
                    <div class="col-md-6">
                        <label for="role">Role <span class="required-star">*</span></label>
                        <select name="role[]" id="role" class="form-select" multiple required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ $role->name == 'dosen' ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jabatan_id">Divisi <span class="required-star">*</span></label>
                        <select name="jabatan_id" id="jabatan_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Divisi / Jabatan</option>
                            @foreach($jabatan as $dj)
                                <option value="{{ $dj->id }}">{{ $dj->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="is_admin">Is Admin <span class="required-star">*</span></label>
                        <select name="is_admin" id="is_admin" class="form-select" required>
                            <option value="user" selected>user</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nama_ibu_kandung">Nama Ibu Kandung <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="nama_ibu_kandung" id="nama_ibu_kandung" required placeholder="Nama Ibu Kandung">
                    </div>
                    <div class="col-md-6">
                        <label for="status_pajak_id">Status Pajak <span class="required-star">*</span></label>
                        <select name="status_pajak_id" id="status_pajak_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Status Pajak</option>
                            @foreach ($status_pajak as $pajak)
                                <option value="{{ $pajak->id }}">{{ $pajak->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Dosen Specific Fields inside Tab 1 -->
                    <div class="col-md-6">
                        <label for="nidn">NIDN <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="nidn" id="nidn" required placeholder="Nomor Induk Dosen Nasional">
                    </div>
                    <div class="col-md-6">
                        <label for="nip">NIP / NUP</label>
                        <input type="text" class="form-control" name="nip" id="nip" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="col-md-6">
                        <label for="program_studi">Program Studi</label>
                        <input type="text" class="form-control" name="program_studi" id="program_studi" placeholder="Teknik Informatika">
                    </div>
                    <div class="col-md-6">
                        <label for="jabatan_akademik">Jabatan Akademik</label>
                        <input type="text" class="form-control" name="jabatan_akademik" id="jabatan_akademik" placeholder="Asisten Ahli / Lektor">
                    </div>
                    <div class="col-md-6">
                        <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select">
                            <option value="" disabled selected>Pilih Pendidikan</option>
                            <option value="S1">S1 - Sarjana</option>
                            <option value="S2">S2 - Magister</option>
                            <option value="S3">S3 - Doktor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="status_kepegawaian">Status Kepegawaian</label>
                        <select name="status_kepegawaian" id="status_kepegawaian" class="form-select">
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="Tetap">Dosen Tetap</option>
                            <option value="Praktisi">Dosen Praktisi / LB</option>
                        </select>
                    </div>
                </div>

                <!-- Alamat KTP -->
                <div class="section-title mt-4">
                    <i class="fa fa-map-marker-alt"></i> Alamat KTP
                </div>
                <div class="row g-3 mb-4 address-group" data-prefix="ktp">
                    <div class="col-md-6">
                        <label>Provinsi <span class="required-star">*</span></label>
                        <select class="form-select addr-provinsi" id="ktp_prov" required>
                            <option value="" disabled selected>PILIH PROVINSI</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kota / Kabupaten <span class="required-star">*</span></label>
                        <select class="form-select addr-kota" id="ktp_kota" required>
                            <option value="" disabled selected>PILIH KOTA/KAB</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kecamatan <span class="required-star">*</span></label>
                        <select class="form-select addr-kecamatan" id="ktp_kec" required>
                            <option value="" disabled selected>PILIH KECAMATAN</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kelurahan <span class="required-star">*</span></label>
                        <select class="form-select addr-kelurahan" id="ktp_kel" required>
                            <option value="" disabled selected>PILIH KELURAHAN</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label>Detail Jalan / No Rumah</label>
                        <input type="text" class="form-control addr-detail" placeholder="Jl. Raya No. 123...">
                    </div>
                    <div class="col-md-4">
                        <label>Kode Pos</label>
                        <input type="text" class="form-control addr-kodepos" placeholder="12345">
                    </div>
                    <div class="col-md-12">
                        <label>Kesimpulan Alamat (Otomatis)</label>
                        <textarea class="form-control addr-summary" name="alamat" id="alamat" rows="3" readonly placeholder="Alamat akan terisi otomatis..."></textarea>
                    </div>
                </div>

                <!-- Alamat Domisili -->
                <div class="section-title mt-4 d-flex align-items-center justify-content-between">
                    <span><i class="fa fa-home"></i> Alamat Domisili</span>
                    <div style="font-size: 13px; font-weight: normal; color: #475569;">
                        <input type="checkbox" id="same_as_ktp" onchange="copyKtpAddress(this)" style="accent-color:#4f46e5; width:16px; height:16px; vertical-align: middle;"> Sama dengan Alamat KTP
                    </div>
                </div>
                <div class="row g-3 mb-2 address-group" data-prefix="dom">
                    <div class="col-md-6">
                        <label>Provinsi <span class="required-star">*</span></label>
                        <select class="form-select addr-provinsi" id="dom_prov" required>
                            <option value="" disabled selected>PILIH PROVINSI</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kota / Kabupaten <span class="required-star">*</span></label>
                        <select class="form-select addr-kota" id="dom_kota" required>
                            <option value="" disabled selected>PILIH KOTA/KAB</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kecamatan <span class="required-star">*</span></label>
                        <select class="form-select addr-kecamatan" id="dom_kec" required>
                            <option value="" disabled selected>PILIH KECAMATAN</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Kelurahan <span class="required-star">*</span></label>
                        <select class="form-select addr-kelurahan" id="dom_kel" required>
                            <option value="" disabled selected>PILIH KELURAHAN</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label>Detail Jalan / No Rumah</label>
                        <input type="text" class="form-control addr-detail" id="dom_det" placeholder="Jl. Raya No. 123...">
                    </div>
                    <div class="col-md-4">
                        <label>Kode Pos</label>
                        <input type="text" class="form-control addr-kodepos" id="dom_pos" placeholder="12345">
                    </div>
                    <div class="col-md-12">
                        <label>Kesimpulan Alamat Domisili (Otomatis)</label>
                        <textarea class="form-control addr-summary" name="alamat_domisili" id="alamat_domisili" rows="3" readonly placeholder="Alamat akan terisi otomatis..."></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. INFORMASI KONTAK TAB -->
            <div class="tab-content-section d-none" id="section-informasi-kontak">
                <div class="section-title">
                    <i class="fa fa-phone-alt"></i> Kontak Darurat
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="kontak_darurat_nama">Nama <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="kontak_darurat_nama" id="kontak_darurat_nama" placeholder="Nama kontak darurat">
                    </div>
                    <div class="col-md-6">
                        <label for="kontak_darurat_hp">HP <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="kontak_darurat_hp" id="kontak_darurat_hp" placeholder="Nomor handphone kontak darurat">
                    </div>
                    <div class="col-md-6">
                        <label for="kontak_darurat_hubungan">Hubungan <span class="required-star">*</span></label>
                        <select name="kontak_darurat_hubungan" id="kontak_darurat_hubungan" class="form-select">
                            <option value="" disabled selected>Pilih Hubungan</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Pasangan">Pasangan (Suami/Istri)</option>
                            <option value="Saudara">Saudara</option>
                            <option value="Teman / Lainnya">Teman / Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 3. DOKUMEN TAB -->
            <div class="tab-content-section d-none" id="section-dokumen">
                <div class="section-title">
                    <i class="fa fa-folder-open"></i> Dokumen Identitas
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="ktp">KTP <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="ktp" id="ktp" placeholder="Masukkan nomor KTP">
                    </div>
                    <div class="col-md-6">
                        <label for="kartu_keluarga">Kartu Keluarga <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="kartu_keluarga" id="kartu_keluarga" placeholder="Masukkan nomor KK">
                    </div>
                    <div class="col-md-6">
                        <label for="bpjs_kesehatan">BPJS Kesehatan <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="bpjs_kesehatan" id="bpjs_kesehatan" placeholder="Masukkan nomor BPJS Kesehatan">
                    </div>
                    <div class="col-md-6">
                        <label for="bpjs_ketenagakerjaan">BPJS Ketenagakerjaan <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="bpjs_ketenagakerjaan" id="bpjs_ketenagakerjaan" placeholder="Masukkan nomor BPJS Ketenagakerjaan">
                    </div>
                    <div class="col-md-6">
                        <label for="npwp">NPWP <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="npwp" id="npwp" placeholder="Masukkan nomor NPWP">
                    </div>
                    <div class="col-md-6">
                        <label for="sim">SIM <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="sim" id="sim" placeholder="Masukkan nomor SIM">
                    </div>
                </div>
                <button type="button" id="btn-add-document" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
                    <i class="fa fa-plus me-1"></i> Tambah Baru
                </button>
                <div id="dynamic-documents-container" class="mt-4" style="display: flex; flex-direction: column; gap: 16px;"></div>
            </div>

            <!-- 4. KONTRAK & REKENING TAB -->
            <div class="tab-content-section d-none" id="section-kontrak-rekening">
                <div class="section-title">
                    <i class="fa fa-file-signature"></i> Kontrak Kerja & Mata Kuliah
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="no_pkwt">No PKWT <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="no_pkwt" id="no_pkwt" placeholder="Nomor PKWT">
                    </div>
                    <div class="col-md-6">
                        <label for="no_kontrak">No Kontrak <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="no_kontrak" id="no_kontrak" placeholder="Nomor Kontrak Kerja">
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_mulai_pkwt">Tgl Mulai Kontrak <span class="required-star">*</span></label>
                        <input type="date" class="form-control" name="tanggal_mulai_pkwt" id="tanggal_mulai_pkwt">
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_berakhir_pkwt">Tgl Berakhir Kontrak <span class="required-star">*</span></label>
                        <input type="date" class="form-control" name="tanggal_berakhir_pkwt" id="tanggal_berakhir_pkwt">
                    </div>
                    
                    <!-- Mata Kuliah Multi-Select -->
                    <div class="col-md-12">
                        <label for="mata_kuliah">Mata Kuliah Yang Diampu <span class="required-star">*</span></label>
                        <select name="mata_kuliah[]" id="mata_kuliah" class="form-select" multiple required>
                            <option value="Algoritma & Pemrograman">Algoritma & Pemrograman</option>
                            <option value="Basis Data">Basis Data</option>
                            <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                            <option value="Struktur Data">Struktur Data</option>
                            <option value="Sistem Operasi">Sistem Operasi</option>
                            <option value="Jaringan Komputer">Jaringan Komputer</option>
                            <option value="Kecerdasan Buatan (AI)">Kecerdasan Buatan (AI)</option>
                            <option value="Pemrograman Web">Pemrograman Web</option>
                            <option value="Kalkulus & Aljabar Linear">Kalkulus & Aljabar Linear</option>
                            <option value="Statistika & Probabilitas">Statistika & Probabilitas</option>
                        </select>
                    </div>
                </div>

                <div class="section-title mt-4">
                    <i class="fa fa-university"></i> Informasi Rekening
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="rekening">No Rekening <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="rekening" id="rekening" placeholder="Nomor rekening bank">
                    </div>
                    <div class="col-md-6">
                        <label for="nama_rekening">Nama Rekening <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="nama_rekening" id="nama_rekening" placeholder="Nama pemegang rekening">
                    </div>
                </div>
            </div>

            <!-- 5. CUTI & IZIN TAB -->
            <div class="tab-content-section d-none" id="section-cuti-izin">
                <div class="section-title">
                    <i class="fa fa-calendar-check"></i> Cuti & Izin
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="izin_cuti">Cuti <span class="required-star">*</span></label>
                        <input type="number" class="form-control" name="izin_cuti" id="izin_cuti" value="12">
                    </div>
                    <div class="col-md-6">
                        <label for="izin_lainnya">Izin Masuk <span class="required-star">*</span></label>
                        <input type="number" class="form-control" name="izin_lainnya" id="izin_lainnya" value="3">
                    </div>
                    <div class="col-md-6">
                        <label for="izin_telat">Izin Telat <span class="required-star">*</span></label>
                        <input type="number" class="form-control" name="izin_telat" id="izin_telat" value="3">
                    </div>
                    <div class="col-md-6">
                        <label for="izin_pulang_cepat">Izin Pulang Cepat <span class="required-star">*</span></label>
                        <input type="number" class="form-control" name="izin_pulang_cepat" id="izin_pulang_cepat" value="3">
                    </div>
                    <div class="col-md-6">
                        <label for="cuti_melahirkan">Cuti Melahirkan <span class="required-star">*</span></label>
                        <input type="number" class="form-control" name="cuti_melahirkan" id="cuti_melahirkan" value="90">
                    </div>
                    <div class="col-md-6">
                        <label for="cuti_kematian">Cuti Kematian <span class="required-star">*</span></label>
                        <input type="number" class="form-control" name="cuti_kematian" id="cuti_kematian" value="3">
                    </div>
                </div>
            </div>

            <!-- 6. PENJUMLAHAN GAJI TAB -->
            <div class="tab-content-section d-none" id="section-penjumlahan-gaji">
                <div class="section-title">
                    <i class="fa fa-money-bill-wave"></i> Skema Gaji & Honorarium Dosen
                </div>
                <div class="row g-3 mb-4" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 16px; padding: 20px; margin-bottom: 20px;">
                    <div class="col-md-12 mb-2">
                        <label for="master_skema_honorarium_id">Hubungkan Ke Master Skema Honorarium (Opsional)</label>
                        <select name="master_skema_honorarium_id" id="master_skema_honorarium_id" class="form-select">
                            <option value="">-- PILIH MASTER SKEMA (MENGGUNAKAN TARIF DARI SKEMA) --</option>
                            @foreach ($skemas as $skema)
                                <option value="{{ $skema->id }}">{{ $skema->nama_skema }} (Rp {{ number_format($skema->nominal_per_unit, 0, ',', '.') }} / unit)</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="display: block; margin-top: 4px;">Jika dipilih, sistem penggajian akan menggunakan skema ini sebagai acuan utama daripada nominal manual di bawah.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="tipe_honorarium">Tipe Honorarium <span class="required-star">*</span></label>
                        <select name="tipe_honorarium" id="tipe_honorarium" class="form-select" required>
                            <option value="" disabled selected>Pilih Skema Honorarium</option>
                            <option value="Per Sesi">Per Sesi (Mengajar per Tatap Muka)</option>
                            <option value="Per Token">Per Token (Mengajar per Bobot Token)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="nominal_honor">Nominal Honor per Unit <span class="required-star">*</span></label>
                        <input type="text" class="form-control format-rupiah" name="nominal_honor" id="nominal_honor" value="0" required>
                    </div>
                </div>

                <div class="section-title mt-4">
                    <i class="fa fa-chart-line"></i> Tunjangan Bulanan Dosen (Optional)
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label for="gaji_pokok">Gaji Pokok</label>
                        <input type="text" class="form-control format-rupiah" name="gaji_pokok" id="gaji_pokok" value="0">
                    </div>
                    <div class="col-md-6">
                        <label for="tunjangan_makan">Tunjangan Makan</label>
                        <input type="text" class="form-control format-rupiah" name="tunjangan_makan" id="tunjangan_makan" value="0">
                    </div>
                    <div class="col-md-6">
                        <label for="tunjangan_transport">Tunjangan Transport</label>
                        <input type="text" class="form-control format-rupiah" name="tunjangan_transport" id="tunjangan_transport" value="0">
                    </div>
                    <div class="col-md-6">
                        <label for="tunjangan_bpjs_kesehatan">Tunjangan BPJS Kesehatan</label>
                        <input type="text" class="form-control format-rupiah" name="tunjangan_bpjs_kesehatan" id="tunjangan_bpjs_kesehatan" value="0">
                    </div>
                    <div class="col-md-6">
                        <label for="tunjangan_bpjs_ketenagakerjaan">Tunjangan BPJS Ketenagakerjaan</label>
                        <input type="text" class="form-control format-rupiah" name="tunjangan_bpjs_ketenagakerjaan" id="tunjangan_bpjs_ketenagakerjaan" value="0">
                    </div>
                    <div class="col-md-6">
                        <label for="lembur">Lembur</label>
                        <input type="text" class="form-control format-rupiah" name="lembur" id="lembur" value="0">
                    </div>
                </div>
            </div>

            <!-- 7. PENGURANGAN GAJI TAB -->
            <div class="tab-content-section d-none" id="section-pengurangan-gaji">
                <style>
                    .deduction-card-wrapper {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 16px;
                        margin-top: 16px;
                    }
                    .deduction-card {
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 16px;
                        padding: 16px 20px;
                        position: relative;
                        transition: all 0.2s;
                    }
                    .deduction-card:hover {
                        border-color: #cbd5e1;
                        background: #f1f5f9;
                    }
                    .deduction-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 8px;
                    }
                    .deduction-label {
                        font-size: 12px;
                        font-weight: 700;
                        color: #475569;
                        text-transform: lowercase;
                        margin: 0;
                    }
                    .deduction-label::first-letter {
                        text-transform: uppercase;
                    }
                    .deduction-badge {
                        font-size: 10px;
                        font-weight: 700;
                        color: #ef4444;
                        background: #fef2f2;
                        padding: 2px 6px;
                        border-radius: 4px;
                        text-transform: uppercase;
                    }
                    .deduction-input-group {
                        position: relative;
                        display: flex;
                        align-items: center;
                    }
                    .deduction-prefix {
                        position: absolute;
                        left: 12px;
                        font-size: 12px;
                        font-weight: 600;
                        color: #94a3b8;
                    }
                    .deduction-input {
                        width: 100%;
                        border-radius: 10px !important;
                        border: 1px solid #cbd5e1 !important;
                        padding: 10px 12px 10px 36px !important;
                        font-size: 15px !important;
                        font-weight: 700 !important;
                        color: #0f172a !important;
                        text-align: right !important;
                        background: #ffffff !important;
                    }
                </style>
                <div class="section-title">
                    <i class="fa fa-minus-circle" style="color: #ef4444;"></i> Potongan
                </div>
                
                <div class="deduction-card-wrapper">
                    <!-- Terlambat -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">potongan terlambat <span class="required-star">*</span></span>
                            <span class="deduction-badge">/ Menit</span>
                        </div>
                        <div class="deduction-input-group">
                            <span class="deduction-prefix">Rp</span>
                            <input type="text" class="deduction-input format-rupiah" name="terlambat" id="terlambat" value="0">
                        </div>
                    </div>

                    <!-- Batas Terlambat -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">batas terlambat <span class="required-star">*</span></span>
                            <span class="deduction-badge">Menit</span>
                        </div>
                        <div class="deduction-input-group">
                            <input type="number" class="deduction-input no-prefix" name="batas_terlambat" id="batas_terlambat" value="0" style="padding-left: 12px !important; text-align: left !important;">
                        </div>
                    </div>

                    <!-- Mangkir -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">potongan mangkir <span class="required-star">*</span></span>
                            <span class="deduction-badge">/ Hari</span>
                        </div>
                        <div class="deduction-input-group">
                            <span class="deduction-prefix">Rp</span>
                            <input type="text" class="deduction-input format-rupiah" name="mangkir" id="mangkir" value="0">
                        </div>
                    </div>

                    <!-- Saldo Kasbon -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">potongan kasbon <span class="required-star">*</span></span>
                            <span class="deduction-badge">/ Bulan</span>
                        </div>
                        <div class="deduction-input-group">
                            <span class="deduction-prefix">Rp</span>
                            <input type="text" class="deduction-input format-rupiah" name="saldo_kasbon" id="saldo_kasbon" value="0">
                        </div>
                    </div>


                    <!-- Potongan BPJS Kesehatan -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">potongan bpjs kesehatan <span class="required-star">*</span></span>
                            <span class="deduction-badge">/ Bulan</span>
                        </div>
                        <div class="deduction-input-group">
                            <span class="deduction-prefix">Rp</span>
                            <input type="text" class="deduction-input format-rupiah" name="potongan_bpjs_kesehatan" id="potongan_bpjs_kesehatan" value="0">
                        </div>
                    </div>

                    <!-- Potongan BPJS Ketenagakerjaan -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">potongan bpjs ketenagakerjaan <span class="required-star">*</span></span>
                            <span class="deduction-badge">/ Bulan</span>
                        </div>
                        <div class="deduction-input-group">
                            <span class="deduction-prefix">Rp</span>
                            <input type="text" class="deduction-input format-rupiah" name="potongan_bpjs_ketenagakerjaan" id="potongan_bpjs_ketenagakerjaan" value="0">
                        </div>
                    </div>

                    <!-- Potongan Koperasi -->
                    <div class="deduction-card">
                        <div class="deduction-header">
                            <span class="deduction-label">potongan koperasi <span class="required-star">*</span></span>
                            <span class="deduction-badge">/ Unit</span>
                        </div>
                        <div class="deduction-input-group">
                            <span class="deduction-prefix">Rp</span>
                            <input type="text" class="deduction-input format-rupiah" name="potongan_koperasi" id="potongan_koperasi" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation Bar -->
        <div class="bottom-nav-container">
            <button type="button" class="nav-arrow-btn" id="prev-tab-btn" onclick="navigateTab(-1)">
                <i class="fa fa-chevron-left"></i>
            </button>
            <div class="dots-indicator">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            <button type="button" class="nav-arrow-btn" id="next-tab-btn" onclick="navigateTab(1)">
                <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </form>

    <script>
        var currentTabIndex = 0;
        var tabSections = [
            'data-pribadi',
            'informasi-kontak',
            'dokumen',
            'kontrak-rekening',
            'cuti-izin',
            'penjumlahan-gaji',
            'pengurangan-gaji'
        ];

        function showTab(index) {
            if (index < 0 || index >= tabSections.length) return;
            currentTabIndex = index;

            // Update Tab Buttons UI
            var tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(function(btn, idx) {
                if (idx === currentTabIndex) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Update Form Sections Visibility
            var sections = document.querySelectorAll('.tab-content-section');
            sections.forEach(function(sec, idx) {
                if (idx === currentTabIndex) {
                    sec.classList.remove('d-none');
                } else {
                    sec.classList.add('d-none');
                }
            });

            // Update Dots Indicator
            var dots = document.querySelectorAll('.dot');
            dots.forEach(function(dot, idx) {
                if (idx === currentTabIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            // Disable/Enable Nav buttons at bounds
            document.getElementById('prev-tab-btn').style.opacity = currentTabIndex === 0 ? '0.3' : '1';
            document.getElementById('next-tab-btn').style.opacity = currentTabIndex === tabSections.length - 1 ? '0.3' : '1';
        }

        function navigateTab(direction) {
            var newIndex = currentTabIndex + direction;
            if (newIndex >= 0 && newIndex < tabSections.length) {
                showTab(newIndex);
            }
        }

        // Click Tab Button directly
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var index = parseInt(this.getAttribute('data-index'));
                showTab(index);
            });
        });

        // Custom Searchable Dropdown Helper
        function makeSelectSearchable(selectElement, placeholderText, iconClass) {
            if (!selectElement) return;
            
            // If already searchable, remove previous wrapper first
            var existingWrapper = selectElement.parentNode.querySelector('.custom-dropdown');
            if (existingWrapper) {
                existingWrapper.remove();
            }

            selectElement.style.display = 'none';

            var wrapper = document.createElement('div');
            wrapper.className = 'custom-dropdown';
            if (selectElement.multiple) {
                wrapper.classList.add('multiple');
            }

            var selectBox = document.createElement('div');
            selectBox.className = 'custom-dropdown-select';
            
            var iconHtml = iconClass ? `<i class="${iconClass} dropdown-icon"></i>` : '';
            selectBox.innerHTML = `
                ${iconHtml}
                <span class="custom-dropdown-selected-text">${placeholderText}</span>
                <i class="fa fa-chevron-down arrow-icon"></i>
            `;
            wrapper.appendChild(selectBox);

            var menu = document.createElement('div');
            menu.className = 'custom-dropdown-menu';
            
            var searchDiv = document.createElement('div');
            searchDiv.className = 'custom-dropdown-search';
            searchDiv.innerHTML = `
                <i class="fa fa-search"></i>
                <input type="text" placeholder="Ketik untuk mencari...">
            `;
            menu.appendChild(searchDiv);

            var optionsDiv = document.createElement('div');
            optionsDiv.className = 'custom-dropdown-options';
            
            function populateOptions() {
                optionsDiv.innerHTML = '';
                Array.from(selectElement.options).forEach(function(opt) {
                    if (opt.value === "" && !opt.selected) return; // skip default select option
                    var optDiv = document.createElement('div');
                    optDiv.className = 'custom-dropdown-option';
                    if (opt.selected) {
                        optDiv.classList.add('selected');
                    }
                    optDiv.innerText = opt.text;
                    optDiv.setAttribute('data-value', opt.value);
                    
                    optDiv.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (selectElement.multiple) {
                            opt.selected = !opt.selected;
                            this.classList.toggle('selected');
                            updateSelectedText();
                        } else {
                            Array.from(selectElement.options).forEach(o => o.selected = false);
                            opt.selected = true;
                            wrapper.querySelectorAll('.custom-dropdown-option').forEach(o => o.classList.remove('selected'));
                            this.classList.add('selected');
                            updateSelectedText();
                            menu.classList.remove('open');
                            selectBox.classList.remove('active');
                        }
                        var event = new Event('change', { bubbles: true });
                        selectElement.dispatchEvent(event);
                    });
                    optionsDiv.appendChild(optDiv);
                });
            }

            function updateSelectedText() {
                var selectedOptions = Array.from(selectElement.options).filter(o => o.selected && o.value !== "");
                if (selectedOptions.length === 0) {
                    selectBox.querySelector('.custom-dropdown-selected-text').innerText = placeholderText;
                    selectBox.querySelector('.custom-dropdown-selected-text').style.color = '#94a3b8';
                } else {
                    var text = selectedOptions.map(o => o.text).join(', ');
                    selectBox.querySelector('.custom-dropdown-selected-text').innerText = text;
                    selectBox.querySelector('.custom-dropdown-selected-text').style.color = '#0f172a';
                }
            }

            menu.appendChild(optionsDiv);
            wrapper.appendChild(menu);
            selectElement.parentNode.insertBefore(wrapper, selectElement);

            selectBox.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('.custom-dropdown-menu').forEach(function(m) {
                    if (m !== menu) m.classList.remove('open');
                });
                document.querySelectorAll('.custom-dropdown-select').forEach(function(s) {
                    if (s !== selectBox) s.classList.remove('active');
                });

                menu.classList.toggle('open');
                this.classList.toggle('active');
                if (menu.classList.contains('open')) {
                    searchDiv.querySelector('input').focus();
                }
            });

            searchDiv.querySelector('input').addEventListener('click', function(e) {
                e.stopPropagation();
            });

            searchDiv.querySelector('input').addEventListener('input', function() {
                var val = this.value.toLowerCase();
                optionsDiv.querySelectorAll('.custom-dropdown-option').forEach(function(opt) {
                    var text = opt.innerText.toLowerCase();
                    if (text.indexOf(val) !== -1) {
                        opt.style.display = 'block';
                    } else {
                        opt.style.display = 'none';
                    }
                });
            });

            document.addEventListener('click', function() {
                menu.classList.remove('open');
                selectBox.classList.remove('active');
            });

            populateOptions();
            updateSelectedText();

            selectElement.addEventListener('change', function() {
                optionsDiv.querySelectorAll('.custom-dropdown-option').forEach(function(optDiv) {
                    var val = optDiv.getAttribute('data-value');
                    var opt = Array.from(selectElement.options).find(o => o.value == val);
                    if (opt && opt.selected) {
                        optDiv.classList.add('selected');
                    } else {
                        optDiv.classList.remove('selected');
                    }
                });
                updateSelectedText();
            });

            // Expose a function to refresh the dropdown UI if options list is modified
            selectElement.refreshDropdown = function() {
                populateOptions();
                updateSelectedText();
            };
        }

        // Initialize first tab & dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            showTab(0);

            // Make main form select fields searchable
            makeSelectSearchable(document.getElementById('role'), 'PILIH ROLE', 'fa fa-shield-alt');
            makeSelectSearchable(document.getElementById('jabatan_id'), 'PILIH DIVISI', 'fa fa-briefcase');
            makeSelectSearchable(document.getElementById('lokasi_id'), 'PILIH LOKASI', 'fa fa-map-marker-alt');
            makeSelectSearchable(document.getElementById('gender'), 'PILIH JENIS KELAMIN', 'fa fa-venus-mars');
            makeSelectSearchable(document.getElementById('is_admin'), 'PILIH TIPE USER', 'fa fa-user-cog');
            makeSelectSearchable(document.getElementById('status_pajak_id'), 'PILIH STATUS PAJAK', 'fa fa-percent');
            makeSelectSearchable(document.getElementById('kontak_darurat_hubungan'), 'PILIH HUBUNGAN', 'fa fa-users');
            makeSelectSearchable(document.getElementById('pendidikan_terakhir'), 'PILIH PENDIDIKAN', 'fa fa-graduation-cap');
            makeSelectSearchable(document.getElementById('status_kepegawaian'), 'PILIH STATUS KEPEGAWAIAN', 'fa fa-id-card');
            makeSelectSearchable(document.getElementById('mata_kuliah'), 'PILIH MATA KULIAH', 'fa fa-book');
            makeSelectSearchable(document.getElementById('tipe_honorarium'), 'PILIH SKEMA HONORARIUM', 'fa fa-file-invoice-dollar');
            makeSelectSearchable(document.getElementById('master_skema_honorarium_id'), 'PILIH MASTER SKEMA', 'fa fa-credit-card');

            // Format Rupiah Input bindings
            document.querySelectorAll('.format-rupiah').forEach(function(input) {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });

            // Load Indonesian Regional API cascading selects
            setupAddressGroup('ktp');
            setupAddressGroup('dom');

            // Handle invalid form fields hidden in tabs
            var form = document.getElementById('tambahDosenForm');
            form.addEventListener('invalid', function(e) {
                e.preventDefault();
                var section = e.target.closest('.tab-content-section');
                if (section) {
                    var tabId = section.id.replace('section-', '');
                    var btn = document.querySelector(`.tab-btn[data-target="${tabId}"]`);
                    if (btn) {
                        var index = parseInt(btn.getAttribute('data-index'));
                        showTab(index);
                    }
                }
                setTimeout(function() {
                    e.target.focus();
                }, 50);
            }, true);
        });

        // setup Indonesian region API for a specific address prefix (ktp / domisili)
        function setupAddressGroup(prefix) {
            var provSel = document.getElementById(prefix + '_prov');
            var kotaSel = document.getElementById(prefix + '_kota');
            var kecSel = document.getElementById(prefix + '_kec');
            var kelSel = document.getElementById(prefix + '_kel');

            // Initialize custom searchable dropdown wrappers
            makeSelectSearchable(provSel, 'PILIH PROVINSI', 'fa fa-map-marker-alt');
            makeSelectSearchable(kotaSel, 'PILIH KOTA/KAB', 'fa fa-map');
            makeSelectSearchable(kecSel, 'PILIH KECAMATAN', 'fa fa-globe');
            makeSelectSearchable(kelSel, 'PILIH KELURAHAN', 'fa fa-home');

            // Fetch Provinces
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(provinces => {
                    provSel.innerHTML = '<option value="" disabled selected>PILIH PROVINSI</option>';
                    provinces.forEach(p => {
                        var opt = new Option(p.name, p.id);
                        provSel.add(opt);
                    });
                    provSel.refreshDropdown();
                });

            // On Province Change
            provSel.addEventListener('change', function() {
                var provId = this.value;
                if (!provId) return;

                // Clear downstream selections
                kotaSel.innerHTML = '<option value="" disabled selected>PILIH KOTA/KAB</option>';
                kecSel.innerHTML = '<option value="" disabled selected>PILIH KECAMATAN</option>';
                kelSel.innerHTML = '<option value="" disabled selected>PILIH KELURAHAN</option>';
                kotaSel.refreshDropdown();
                kecSel.refreshDropdown();
                kelSel.refreshDropdown();

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`)
                    .then(response => response.json())
                    .then(regencies => {
                        regencies.forEach(r => {
                            var opt = new Option(r.name, r.id);
                            kotaSel.add(opt);
                        });
                        kotaSel.refreshDropdown();
                        updateAddressSummary(prefix);
                    });
            });

            // On City Change
            kotaSel.addEventListener('change', function() {
                var regencyId = this.value;
                if (!regencyId) return;

                kecSel.innerHTML = '<option value="" disabled selected>PILIH KECAMATAN</option>';
                kelSel.innerHTML = '<option value="" disabled selected>PILIH KELURAHAN</option>';
                kecSel.refreshDropdown();
                kelSel.refreshDropdown();

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`)
                    .then(response => response.json())
                    .then(districts => {
                        districts.forEach(d => {
                            var opt = new Option(d.name, d.id);
                            kecSel.add(opt);
                        });
                        kecSel.refreshDropdown();
                        updateAddressSummary(prefix);
                    });
            });

            // On District Change
            kecSel.addEventListener('change', function() {
                var districtId = this.value;
                if (!districtId) return;

                kelSel.innerHTML = '<option value="" disabled selected>PILIH KELURAHAN</option>';
                kelSel.refreshDropdown();

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
                    .then(response => response.json())
                    .then(villages => {
                        villages.forEach(v => {
                            var opt = new Option(v.name, v.id);
                            kelSel.add(opt);
                        });
                        kelSel.refreshDropdown();
                        updateAddressSummary(prefix);
                    });
            });

            // On Sub-district Change
            kelSel.addEventListener('change', function() {
                updateAddressSummary(prefix);
            });

            // Other fields change listeners
            var group = provSel.closest('.address-group');
            group.querySelector('.addr-detail').addEventListener('input', () => updateAddressSummary(prefix));
            group.querySelector('.addr-kodepos').addEventListener('input', () => updateAddressSummary(prefix));
        }

        function updateAddressSummary(prefix) {
            var group = document.querySelector(`.address-group[data-prefix="${prefix}"]`);
            var provSel = document.getElementById(prefix + '_prov');
            var kotaSel = document.getElementById(prefix + '_kota');
            var kecSel = document.getElementById(prefix + '_kec');
            var kelSel = document.getElementById(prefix + '_kel');

            var prov = provSel.selectedIndex >= 0 && provSel.value ? provSel.options[provSel.selectedIndex].text : '';
            var kota = kotaSel.selectedIndex >= 0 && kotaSel.value ? kotaSel.options[kotaSel.selectedIndex].text : '';
            var kec = kecSel.selectedIndex >= 0 && kecSel.value ? kecSel.options[kecSel.selectedIndex].text : '';
            var kel = kelSel.selectedIndex >= 0 && kelSel.value ? kelSel.options[kelSel.selectedIndex].text : '';
            
            var detail = group.querySelector('.addr-detail').value.trim();
            var pos = group.querySelector('.addr-kodepos').value.trim();

            var fullAddress = "";
            if (detail) fullAddress += detail;
            if (kel) fullAddress += (fullAddress ? ", Kel. " : "Kel. ") + kel;
            if (kec) fullAddress += (fullAddress ? ", Kec. " : "Kec. ") + kec;
            if (kota) fullAddress += (fullAddress ? ", " : "") + kota;
            if (prov) fullAddress += (fullAddress ? ", Prov. " : "Prov. ") + prov;
            if (pos) fullAddress += (fullAddress ? " " : "") + pos;

            group.querySelector('.addr-summary').value = fullAddress;
        }

        // Helper to format currency
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        // Preview profile image
        function previewImage(input) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                    document.getElementById('avatar-preview').style.display = 'block';
                    document.getElementById('dropzone-icon').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        // Copy KTP address to Domisili
        function copyKtpAddress(checkbox) {
            if (checkbox.checked) {
                var ktpGroup = document.querySelector('.address-group[data-prefix="ktp"]');
                var domGroup = document.querySelector('.address-group[data-prefix="dom"]');

                // Copy values of select elements
                document.getElementById('dom_prov').innerHTML = document.getElementById('ktp_prov').innerHTML;
                document.getElementById('dom_prov').value = document.getElementById('ktp_prov').value;
                document.getElementById('dom_prov').refreshDropdown();

                document.getElementById('dom_kota').innerHTML = document.getElementById('ktp_kota').innerHTML;
                document.getElementById('dom_kota').value = document.getElementById('ktp_kota').value;
                document.getElementById('dom_kota').refreshDropdown();

                document.getElementById('dom_kec').innerHTML = document.getElementById('ktp_kec').innerHTML;
                document.getElementById('dom_kec').value = document.getElementById('ktp_kec').value;
                document.getElementById('dom_kec').refreshDropdown();

                document.getElementById('dom_kel').innerHTML = document.getElementById('ktp_kel').innerHTML;
                document.getElementById('dom_kel').value = document.getElementById('ktp_kel').value;
                document.getElementById('dom_kel').refreshDropdown();

                // Copy text inputs
                domGroup.querySelector('.addr-detail').value = ktpGroup.querySelector('.addr-detail').value;
                domGroup.querySelector('.addr-kodepos').value = ktpGroup.querySelector('.addr-kodepos').value;
                domGroup.querySelector('.addr-summary').value = ktpGroup.querySelector('.addr-summary').value;
            }
        }

        // Calculate Masa Kerja
        function calculateMasaKerja() {
            var joinDateVal = document.getElementById('tgl_join').value;
            if (joinDateVal) {
                var joinDate = new Date(joinDateVal);
                var today = new Date();
                
                var diffTime = Math.abs(today - joinDate);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                
                var years = Math.floor(diffDays / 365);
                var months = Math.floor((diffDays % 365) / 30);
                
                var result = "";
                if (years > 0) result += years + " Tahun ";
                if (months > 0) result += months + " Bulan";
                if (result === "") result = "0 Bulan";

                document.getElementById('masa_kerja').value = result;
            }
        }

        // Dynamic documents logic
        document.getElementById('btn-add-document').addEventListener('click', function() {
            var container = document.getElementById('dynamic-documents-container');
            var card = document.createElement('div');
            card.className = 'dynamic-document-card';
            card.style.cssText = 'border: 1px solid #cbd5e1; border-radius: 16px; padding: 18px 24px; position: relative; margin-bottom: 12px;';
            card.innerHTML = `
                <button type="button" class="btn-remove-doc" style="position: absolute; right: 24px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px;">
                    <i class="fa fa-trash-alt"></i>
                </button>
                <div class="row g-3" style="margin-right: 40px;">
                    <div class="col-12 mb-2">
                        <input type="text" name="document_names[]" class="form-control" placeholder="Nama File (KTP, SIM, dll)" required style="background: #ffffff !important;">
                    </div>
                    <div class="col-12">
                        <input type="file" name="document_files[]" class="form-control" required style="background: #ffffff !important;">
                    </div>
                </div>
            `;
            
            card.querySelector('.btn-remove-doc').addEventListener('click', function() {
                card.remove();
            });
            
            container.appendChild(card);
        });
    </script>
@endsection
