@extends('templates.dashboard')
@section('isi')
    <style>
        .tambah-dosen-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 30px;
            border-radius: 24px;
            color: #ffffff;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px -5px rgba(217, 119, 6 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .btn-back-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .btn-back-circle:hover {
            background: #ffffff;
            color: #d97706;
            transform: translateX(-3px);
        }
        .header-title-wrapper h3 {
            font-weight: 800;
            margin: 0;
            font-size: 24px;
            letter-spacing: -0.5px;
        }
        .header-title-wrapper p {
            margin: 4px 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
        }
        .btn-cancel {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* Stepper Form Layout */
        .form-card-container {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            padding: 32px;
            margin-bottom: 24px;
        }

        /* Tab Buttons Stepper */
        .stepper-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 16px;
            margin-bottom: 32px;
            overflow-x: auto;
            gap: 16px;
        }
        .tab-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-weight: 700;
            font-size: 14px;
            padding: 8px 16px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tab-btn i {
            font-size: 16px;
        }
        .tab-btn.active {
            color: #d97706;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -18px;
            left: 0;
            right: 0;
            height: 4px;
            background: #d97706;
            border-radius: 2px;
        }

        .tab-content-section {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Input Controls styling */
        label {
            font-weight: 700;
            color: #334155;
            font-size: 13px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
            outline: none;
        }
        .required-star {
            color: #ef4444;
            margin-left: 2px;
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
            background: #fffbeb;
            color: #d97706;
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
            background: #d97706;
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
            border-color: #d97706;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
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
            border-color: #d97706;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1);
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
            color: #d97706;
        }
        .custom-dropdown-option.selected {
            background-color: #eff6ff;
            color: #d97706;
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
            background-color: #d97706;
            border-color: #d97706;
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

        /* Gaji/Honor cards */
        .honorarium-card {
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            background-color: #f8fafc;
            padding: 24px;
            transition: all 0.2s;
        }
        .honorarium-card:hover {
            border-color: #d97706;
            background-color: #ffffff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        }
        .honor-prefix {
            font-size: 16px;
            font-weight: 700;
            color: #475569;
            background: #e2e8f0;
            padding: 12px 16px;
            border-radius: 12px 0 0 12px;
            border: 1px solid #cbd5e1;
            border-right: none;
            display: flex;
            align-items: center;
        }
        .honor-input {
            border-radius: 0 12px 12px 0 !important;
            flex-grow: 1;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <form id="editDosenForm" method="POST" action="{{ url('/dosen/update/'.$dosen->id) }}">
        @csrf
        @method('PUT')

        <!-- Top Header Card -->
        <div class="tambah-dosen-header">
            <div class="header-left">
                <a href="{{ url('/dosen') }}" class="btn-back-circle">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="header-title-wrapper">
                    <h3>Edit Dosen: {{ $dosen->name }}</h3>
                    <p>Ubah data dosen & sistem honorarium</p>
                </div>
            </div>
            <div class="header-right">
                <a href="{{ url('/dosen') }}" class="btn-cancel">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 16px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card-container">
            <!-- Stepper Navigation -->
            <div class="stepper-nav">
                <button type="button" class="tab-btn active" data-index="0">
                    <i class="fa fa-user-tie"></i> 1. Identitas Akademik
                </button>
                <button type="button" class="tab-btn" data-index="1">
                    <i class="fa fa-graduation-cap"></i> 2. Kualifikasi & Tugas
                </button>
                <button type="button" class="tab-btn" data-index="2">
                    <i class="fa fa-address-book"></i> 3. Kontak & Akun
                </button>
                <button type="button" class="tab-btn" data-index="3">
                    <i class="fa fa-money-bill-wave"></i> 4. Skema Honorarium
                </button>
            </div>

            <!-- Tab 1: DATA AKADEMIK -->
            <div class="tab-content-section" id="section-data-akademik">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name">Nama Lengkap <span class="required-star">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $dosen->name) }}" placeholder="Nama Lengkap beserta gelar (opsional)" required>
                    </div>
                    <div class="col-md-3">
                        <label for="gelar_depan">Gelar Depan</label>
                        <input type="text" class="form-control" name="gelar_depan" id="gelar_depan" value="{{ old('gelar_depan', $dosen->gelar_depan) }}" placeholder="Contoh: Dr., Prof.">
                    </div>
                    <div class="col-md-3">
                        <label for="gelar_belakang">Gelar Belakang</label>
                        <input type="text" class="form-control" name="gelar_belakang" id="gelar_belakang" value="{{ old('gelar_belakang', $dosen->gelar_belakang) }}" placeholder="Contoh: M.Kom, Ph.D">
                    </div>
                    <div class="col-md-6">
                        <label for="nidn">NIDN</label>
                        <input type="text" class="form-control" name="nidn" id="nidn" value="{{ old('nidn', $dosen->nidn) }}" placeholder="Nomor Induk Dosen Nasional">
                    </div>
                    <div class="col-md-6">
                        <label for="nip">NIP / NUP</label>
                        <input type="text" class="form-control" name="nip" id="nip" value="{{ old('nip', $dosen->nip) }}" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="col-md-6">
                        <label for="program_studi">Program Studi</label>
                        <input type="text" class="form-control" name="program_studi" id="program_studi" value="{{ old('program_studi', $dosen->program_studi) }}" placeholder="Contoh: Teknik Informatika">
                    </div>
                    <div class="col-md-6">
                        <label for="jabatan_akademik">Jabatan Akademik</label>
                        <input type="text" class="form-control" name="jabatan_akademik" id="jabatan_akademik" value="{{ old('jabatan_akademik', $dosen->jabatan_akademik) }}" placeholder="Lektor, Asisten Ahli, dll">
                    </div>
                </div>
            </div>

            <!-- Tab 2: KUALIFIKASI & TUGAS -->
            <div class="tab-content-section d-none" id="section-kualifikasi">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select">
                            <option value="" disabled selected>Pilih Pendidikan</option>
                            <option value="S1" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir)=='S1'?'selected':'' }}>S1 - Sarjana</option>
                            <option value="S2" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir)=='S2'?'selected':'' }}>S2 - Magister</option>
                            <option value="S3" {{ old('pendidikan_terakhir', $dosen->pendidikan_terakhir)=='S3'?'selected':'' }}>S3 - Doktor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="status_kepegawaian">Status Kepegawaian</label>
                        <select name="status_kepegawaian" id="status_kepegawaian" class="form-select">
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="Tetap" {{ old('status_kepegawaian', $dosen->status_kepegawaian)=='Tetap'?'selected':'' }}>Dosen Tetap</option>
                            <option value="Praktisi" {{ old('status_kepegawaian', $dosen->status_kepegawaian)=='Praktisi'?'selected':'' }}>Dosen Praktisi / LB</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="mata_kuliah">Mata Kuliah Yang Diampu <span class="required-star">*</span></label>
                        <select name="mata_kuliah[]" id="mata_kuliah" class="form-select" multiple required>
                            @foreach($mata_kuliah as $mk)
                                <option value="{{ $mk->nama_mk }}" {{ in_array($mk->nama_mk, $current_subjects) ? 'selected' : '' }}>{{ $mk->nama_mk }} ({{ $mk->prodi }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="lokasi_id">Lokasi Kantor / Kampus Utama</label>
                        <select name="lokasi_id" id="lokasi_id" class="form-select">
                            <option value="" disabled selected>PILIH LOKASI</option>
                            @foreach($lokasi as $l)
                                <option value="{{ $l->id }}" {{ old('lokasi_id', $dosen->lokasi_id)==$l->id?'selected':'' }}>{{ $l->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab 3: KONTAK & AKUN -->
            <div class="tab-content-section d-none" id="section-kontak">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="email">Email Kampus <span class="required-star">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $dosen->email) }}" placeholder="email@kampus.ac.id" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telepon">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="telepon" id="telepon" value="{{ old('telepon', $dosen->telepon) }}" placeholder="081234xxxx">
                    </div>
                    <div class="col-md-6">
                        <label for="password">Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Minimal 6 Karakter">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Ulangi Password Baru">
                    </div>
                </div>
            </div>

            <!-- Tab 4: SKEMA HONORARIUM -->
            <div class="tab-content-section d-none" id="section-honorarium">
                <div class="honorarium-card">
                    <h5 class="mb-4" style="font-weight: 800; color: #1e293b;"><i class="fa fa-wallet text-warning me-2"></i> Pengaturan Gaji Dosen</h5>
                    <div class="row g-3">
                        <div class="col-md-12 mb-2">
                            <label for="master_skema_honorarium_id">Hubungkan Ke Master Skema Honorarium (Opsional)</label>
                            <select name="master_skema_honorarium_id" id="master_skema_honorarium_id" class="form-select">
                                <option value="">-- PILIH MASTER SKEMA (MENGGUNAKAN TARIF DARI SKEMA) --</option>
                                @foreach ($skemas as $skema)
                                    <option value="{{ $skema->id }}" {{ old('master_skema_honorarium_id', $dosen->master_skema_honorarium_id) == $skema->id ? 'selected' : '' }}>{{ $skema->nama_skema }} (Rp {{ number_format($skema->nominal_per_unit, 0, ',', '.') }} / unit)</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="display: block; margin-top: 4px;">Jika dipilih, sistem penggajian akan menggunakan skema ini sebagai acuan utama daripada nominal manual di bawah.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="tipe_honorarium">Tipe Honorarium <span class="required-star">*</span></label>
                            <select name="tipe_honorarium" id="tipe_honorarium" class="form-select" required>
                                <option value="" disabled selected>Pilih Skema Perhitungan</option>
                                <option value="Per Sesi" {{ old('tipe_honorarium', $dosen->tipe_honorarium)=='Per Sesi'?'selected':'' }}>Per Sesi (Mengajar per Tatap Muka)</option>
                                <option value="Per Token" {{ old('tipe_honorarium', $dosen->tipe_honorarium)=='Per Token'?'selected':'' }}>Per Token (Mengajar per Bobot Token Kuliah)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nominal_honor">Nominal Honor per Unit <span class="required-star">*</span></label>
                            <div class="d-flex align-items-stretch">
                                <span class="honor-prefix">Rp</span>
                                <input type="text" class="form-control honor-input format-rupiah" name="nominal_honor" id="nominal_honor" value="{{ old('nominal_honor', number_format($dosen->nominal_honor ?? 0, 0, ',', '.')) }}" required>
                            </div>
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
            </div>
            <button type="button" class="nav-arrow-btn" id="next-tab-btn" onclick="navigateTab(1)">
                <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </form>

    <script>
        var currentTabIndex = 0;
        var tabSections = [
            'section-data-akademik',
            'section-kualifikasi',
            'section-kontak',
            'section-honorarium'
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
            tabSections.forEach(function(secId, idx) {
                var sec = document.getElementById(secId);
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
        }

        document.addEventListener('DOMContentLoaded', function() {
            showTab(0);

            // Initialize Searchable Dropdowns
            makeSelectSearchable(document.getElementById('pendidikan_terakhir'), 'PILIH PENDIDIKAN', 'fa fa-graduation-cap');
            makeSelectSearchable(document.getElementById('status_kepegawaian'), 'PILIH STATUS KEPEGAWAIAN', 'fa fa-id-card');
            makeSelectSearchable(document.getElementById('mata_kuliah'), 'PILIH MATA KULIAH', 'fa fa-book');
            makeSelectSearchable(document.getElementById('lokasi_id'), 'PILIH LOKASI', 'fa fa-map-marker-alt');
            makeSelectSearchable(document.getElementById('tipe_honorarium'), 'PILIH SKEMA HONORARIUM', 'fa fa-file-invoice-dollar');
            makeSelectSearchable(document.getElementById('master_skema_honorarium_id'), 'PILIH MASTER SKEMA', 'fa fa-credit-card');

            // Format Rupiah Input bindings
            document.querySelectorAll('.format-rupiah').forEach(function(input) {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });
        });

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
    </script>
@endsection
