@extends('templates.dashboard')
@section('isi')
    <style>
        .shift-header-card {
            border: none;
            background: transparent;
            box-shadow: none;
            margin-bottom: 20px;
        }
        .shift-title {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .shift-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
            margin-bottom: 0;
        }
        .action-buttons-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }
        .action-buttons-group .btn {
            border-radius: 12px;
            padding: 8px 16px;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
            background: #fff;
            border: 1px solid transparent;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-outline-action {
            border-color: #cbd5e1 !important;
            color: #475569 !important;
            background: #fff !important;
        }
        .btn-outline-action:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            transform: translateY(-1px);
        }
        .btn-solid-kembali {
            background: #0f172a !important;
            color: #fff !important;
            border: none !important;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
        }
        .btn-solid-kembali:hover {
            background: #1e293b !important;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.25);
            transform: translateY(-1px);
        }
        
        /* Stats Cards */
        .stats-wrapper {
            margin-bottom: 25px;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
        }
        .icon-blue {
            background-color: #e0e7ff;
            color: #4f46e5;
        }
        .icon-green {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .icon-purple {
            background-color: #f3e8ff;
            color: #9333ea;
        }
        .stat-content {
            display: flex;
            flex-direction: column;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        /* Search Section */
        .search-container {
            margin-bottom: 24px;
        }
        .search-input-wrapper {
            position: relative;
            max-width: 380px;
        }
        .search-input-wrapper input {
            border-radius: 30px;
            padding: 10px 20px 10px 45px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .search-input-wrapper input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
            outline: none;
        }
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* Shift Grid Card */
        .shift-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .shift-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-left: 5px solid #f59e0b; /* yellow accent */
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .shift-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.05);
        }
        .shift-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .shift-info {
            display: flex;
            flex-direction: column;
        }
        .shift-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .shift-id-badge {
            font-size: 10px;
            background: #f1f5f9;
            color: #64748b;
            padding: 2px 6px;
            border-radius: 6px;
            font-weight: 600;
        }
        .shift-time {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .shift-tag {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #d97706;
            background: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .btn-assign-karyawan {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-assign-karyawan:hover {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #bfdbfe;
        }
        
        .assigned-summary {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .assigned-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 250px;
            overflow-y: auto;
        }
        .assigned-user-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }
        .assigned-user-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .avatar-initials {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4f46e5;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .assigned-user-info {
            display: flex;
            flex-direction: column;
        }
        .assigned-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }
        .assigned-user-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
        }
        .assigned-user-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .lock-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .lock-badge.locked {
            background: #e0e7ff;
            color: #4f46e5;
        }
        .lock-badge.unlocked {
            background: #f1f5f9;
            color: #64748b;
        }
        .btn-delete-assignment {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-delete-assignment:hover {
            color: #b91c1c;
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

    <div class="row">
        <!-- Header -->
        <div class="col-md-12 project-list">
            <div class="card shift-header-card">
                <div class="row align-items-center">
                    <div class="col-md-5 mt-2 p-0">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="shift-title" style="margin: 0;">Manajemen Shift</h3>
                            <a href="{{ url('/shift/create') }}" class="btn btn-sm btn-primary" style="border-radius: 10px; background-color: #4f46e5; border: none; font-weight: 600; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);">
                                <i class="fa fa-plus"></i> Tambah Shift
                            </a>
                        </div>
                        <p class="shift-subtitle">Kelola jadwal shift & penugasan karyawan</p>
                    </div>
                    <div class="col-md-7 p-0">
                        <div class="action-buttons-group">
                            <a href="{{ url('/shift-management/template') }}" class="btn btn-outline-action">
                                <i class="fa fa-download me-2"></i> Template
                            </a>
                            <button type="button" class="btn btn-outline-action" data-bs-toggle="modal" data-bs-target="#importShiftModal">
                                <i class="fa fa-upload me-2"></i> Import
                            </button>
                            <a href="{{ url('/shift') }}" class="btn btn-outline-action">
                                <i class="fa fa-sync me-2"></i> Refresh
                            </a>
                            <a href="{{ url('/pegawai') }}" class="btn btn-solid-kembali">
                                <i class="fa fa-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-md-12">
            <div class="row stats-wrapper">
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon-wrapper icon-blue">
                            <i class="fa fa-clock" style="font-size: 18px;"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Shift</span>
                            <span class="stat-value">{{ $total_shift }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon-wrapper icon-green">
                            <i class="fa fa-user-check" style="font-size: 18px;"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Karyawan Aktif</span>
                            <span class="stat-value">{{ $karyawan_aktif }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon-wrapper icon-purple">
                            <i class="fa fa-calendar-alt" style="font-size: 18px;"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Jadwal Terjadwal</span>
                            <span class="stat-value">{{ $jadwal_terjadwal }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="col-md-12 search-container">
            <div class="search-input-wrapper">
                <i class="fa fa-search search-icon"></i>
                <input type="text" id="search-shift" class="form-control" placeholder="Cari shift berdasarkan nama...">
            </div>
        </div>

        <!-- Shifts Grid -->
        <div class="col-md-12">
            <div class="shift-grid">
                @foreach ($shifts as $s)
                    <div class="shift-card shift-card-item" data-name="{{ $s->nama_shift }}">
                        <div class="shift-card-header">
                            <div class="shift-info">
                                <span class="shift-name">
                                    {{ $s->nama_shift }}
                                    <span class="shift-id-badge">ID: {{ $s->id }}</span>
                                    @if ($s->nama_shift !== 'Libur')
                                        <a href="{{ url('/shift/'.$s->id.'/edit') }}" class="text-warning ms-1" title="Edit Shift" style="font-size: 13px;">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ url('/shift/'.$s->id) }}" method="post" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus master shift ini?')">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" style="background: none; border: none; padding: 0; color: #ef4444; font-size: 13px;" title="Hapus Shift">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </span>
                                <span class="shift-time">
                                    <span class="shift-tag">PAGI</span>
                                    <i class="fa fa-clock"></i> {{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_keluar)->format('H:i') }}
                                </span>
                            </div>
                            <button type="button" class="btn-assign-karyawan btn-open-assign-modal" data-shift-id="{{ $s->id }}" data-shift-name="{{ $s->nama_shift }}">
                                <i class="fa fa-user-plus"></i>
                            </button>
                        </div>

                        <span class="assigned-summary">
                            <i class="fa fa-users"></i> {{ count($s->assigned_employees ?? []) }} Karyawan
                        </span>

                        <div class="assigned-list">
                            @forelse ($s->assigned_employees ?? [] as $assigned)
                                @php
                                    $initials = collect(explode(' ', $assigned['user']->name))->map(function($word) {
                                        return strtoupper(substr($word, 0, 1));
                                    })->take(2)->implode('');
                                @endphp
                                <div class="assigned-user-item">
                                    <div class="assigned-user-left">
                                        <div class="avatar-initials">{{ $initials }}</div>
                                        <div class="assigned-user-info">
                                            <span class="assigned-user-name">{{ $assigned['user']->name }}</span>
                                            <span class="assigned-user-sub">{{ $assigned['user']->Jabatan->nama_jabatan ?? 'Pegawai' }} &bull; {{ $assigned['range'] }}</span>
                                        </div>
                                    </div>
                                    <div class="assigned-user-right">
                                        @if ($assigned['lock_location'])
                                            <span class="lock-badge locked">Lock</span>
                                        @else
                                            <span class="lock-badge unlocked">Unlock</span>
                                        @endif
                                        <form action="{{ url('/shift-management/delete-assignment/'.$assigned['mapping_ids']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-assignment" onclick="return confirm('Hapus penugasan shift ini?')">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3 text-muted" style="font-size: 13px;">
                                    Belum ada karyawan ditugaskan
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="importShiftModal" tabindex="-1" role="dialog" aria-labelledby="importShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none;">
                <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 24px;">
                    <div>
                        <h5 class="modal-title" id="importShiftModalLabel" style="font-weight: 700; color: #0f172a;">Import Jadwal Shift</h5>
                        <p style="margin: 4px 0 0; font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Bulk Upload Karyawan ke Shift</p>
                    </div>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/shift-management/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body" style="padding: 24px;">
                        <div class="alert alert-info d-flex align-items-center mb-4" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16a34a; border-radius: 12px; font-size: 13px;">
                            <i class="fa fa-info-circle me-3" style="font-size: 18px;"></i>
                            <div>
                                <strong>Download Template Import</strong><br>
                                Isi data jadwal shift sesuai format agar tidak terjadi kesalahan.
                            </div>
                            <a href="{{ url('/shift-management/template') }}" class="btn btn-sm btn-success ms-auto" style="border-radius: 8px;">Unduh Template</a>
                        </div>
                        <div class="form-group text-center py-4" style="border: 2px dashed #cbd5e1; border-radius: 16px; background-color: #f8fafc;">
                            <i class="fa fa-cloud-upload-alt" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px;"></i>
                            <h6 style="font-weight: 700; color: #334155; margin-bottom: 6px;">Upload File Excel Anda</h6>
                            <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">Drag & drop file template yang sudah diisi di sini</p>
                            <input type="file" name="file_excel" id="file_excel" class="form-control-file" style="margin: 0 auto;" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 20px 24px;">
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal" style="border-radius: 10px;">BATAL</button>
                        <button class="btn btn-primary btn-sm" type="submit" style="border-radius: 10px; background-color: #4f46e5; border: none;">IMPORT DATA</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manual Assignment Modal -->
    <div class="modal fade" id="assignShiftModal" tabindex="-1" role="dialog" aria-labelledby="assignShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none;">
                <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 24px;">
                    <div>
                        <h5 class="modal-title" id="assignShiftModalLabel" style="font-weight: 700; color: #0f172a;">Tugaskan Pegawai ke Shift</h5>
                        <p id="target-shift-name" style="margin: 4px 0 0; font-size: 13px; color: #4f46e5; font-weight: 600;"></p>
                    </div>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('/shift-management/assign') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shift_id" id="modal-shift-id">
                    <div class="modal-body" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                        <div class="form-group">
                            <label for="user_ids" style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 6px;">Pilih Pegawai atau Dosen</label>
                            <select name="user_ids[]" id="user_ids" class="form-control" style="border-radius: 10px;" multiple required>
                                @foreach($all_users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->tipe_user ?? 'pegawai' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="tanggal_mulai" style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 6px;">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" style="border-radius: 10px;" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="tanggal_akhir" style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 6px;">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" style="border-radius: 10px;" required>
                            </div>
                        </div>
                        <div class="form-group d-flex align-items-center" style="gap: 10px;">
                            <input type="checkbox" name="lock_location" id="lock_location" value="1" style="width: 18px; height: 18px; accent-color: #4f46e5;">
                            <label for="lock_location" style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 0; cursor: pointer;">Kunci Lokasi Absensi (Lock Location)</label>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 20px 24px;">
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal" style="border-radius: 10px;">BATAL</button>
                        <button class="btn btn-primary btn-sm" type="submit" style="border-radius: 10px; background-color: #4f46e5; border: none;">SIMPAN PENUGASAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripting for live search and modal bindings -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Search
            var searchInput = document.getElementById('search-shift');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    var query = this.value.toLowerCase();
                    var cards = document.querySelectorAll('.shift-card-item');
                    cards.forEach(function(card) {
                        var name = card.getAttribute('data-name').toLowerCase();
                        if (name.indexOf(query) !== -1) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Assign Modal triggers
            var assignButtons = document.querySelectorAll('.btn-open-assign-modal');
            assignButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var shiftId = this.getAttribute('data-shift-id');
                    var shiftName = this.getAttribute('data-shift-name');
                    
                    document.getElementById('modal-shift-id').value = shiftId;
                    document.getElementById('target-shift-name').innerText = shiftName;
                    
                    // Clear previous select values
                    var userSelect = document.getElementById('user_ids');
                    Array.from(userSelect.options).forEach(opt => opt.selected = false);
                    if (userSelect.dispatchEvent) {
                        userSelect.dispatchEvent(new Event('change'));
                    }
                    
                    var modal = new bootstrap.Modal(document.getElementById('assignShiftModal'));
                    modal.show();
                });
            });

            // Initialize searchable multiple checklist dropdown
            makeSelectSearchable(document.getElementById('user_ids'), 'PILIH PEGAWAI / DOSEN', 'fa fa-user-friends');
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
    </script>
@endsection
