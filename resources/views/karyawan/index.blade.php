@extends('templates.dashboard')
@section('isi')
    <style>
        .pegawai-header-card {
            border: none;
            background: transparent;
            box-shadow: none;
            margin-bottom: 20px;
        }
        .pegawai-title {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .pegawai-subtitle {
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
        .btn-outline-resapan {
            border-color: #cbd5e1 !important;
            color: #4f46e5 !important;
            background: #fff !important;
        }
        .btn-outline-resapan:hover {
            background-color: #f5f3ff !important;
            border-color: #c4b5fd !important;
            transform: translateY(-1px);
        }
        .btn-outline-export {
            border-color: #e2e8f0 !important;
            color: #16a34a !important;
        }
        .btn-outline-export:hover {
            background-color: #f0fdf4;
            border-color: #86efac !important;
            transform: translateY(-1px);
        }
        .btn-outline-import {
            border-color: #e2e8f0 !important;
            color: #ea580c !important;
        }
        .btn-outline-import:hover {
            background-color: #fff7ed;
            border-color: #fdba74 !important;
            transform: translateY(-1px);
        }
        .btn-outline-shift {
            border-color: #e2e8f0 !important;
            color: #7c3aed !important;
        }
        .btn-outline-shift:hover {
            background-color: #f5f3ff;
            border-color: #c4b5fd !important;
            transform: translateY(-1px);
        }
        .btn-solid-tambah {
            background: #4f46e5 !important;
            color: #fff !important;
            border: none !important;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }
        .btn-solid-tambah:hover {
            background: #4338ca !important;
            box-shadow: 0 6px 14px rgba(79, 70, 229, 0.3);
            transform: translateY(-1px);
        }
        
        /* Stats Cards */
        .stats-wrapper {
            margin-bottom: 25px;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px 20px;
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
        .icon-orange {
            background-color: #fef3c7;
            color: #d97706;
        }
        .icon-red {
            background-color: #fee2e2;
            color: #dc2626;
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

        /* Drawer Slide-out */
        .resapan-drawer {
            position: fixed;
            top: 0;
            right: -600px;
            width: 580px;
            height: 100vh;
            background: #fff;
            z-index: 1050;
            box-shadow: -4px 0 30px rgba(0, 0, 0, 0.1);
            transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            font-family: 'Rubik', 'Segoe UI', sans-serif;
        }
        .resapan-drawer.show {
            right: 0;
        }
        .resapan-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1040;
            display: none;
        }
        .resapan-backdrop.show {
            display: block;
        }
        .drawer-header {
            padding: 24px;
            border-bottom: 1px solid #f1f5f9;
            position: relative;
        }
        .drawer-close-btn {
            position: absolute;
            top: 24px;
            right: 24px;
            background: none;
            border: none;
            font-size: 20px;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }
        .drawer-close-btn:hover {
            color: #475569;
        }
        .drawer-body {
            padding: 24px;
            overflow-y: auto;
            flex-grow: 1;
        }
        .drawer-footer {
            padding: 20px 24px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: flex-end;
            background: #f8fafc;
        }
        .drawer-nav-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .drawer-tab {
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            background: #f1f5f9;
            color: #64748b;
        }
        .drawer-tab.active-purple {
            background: #4f46e5;
            color: #fff;
        }
        .drawer-tab.active-blue {
            background: #0f3d59;
            color: #fff;
        }
        .drawer-stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }
        .drawer-stat-card {
            border-radius: 20px;
            padding: 18px;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100px;
        }
        .drawer-stat-card.solid-purple {
            background: #4f46e5;
            color: #fff;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        .drawer-stat-card.solid-blue {
            background: #0f3d59;
            color: #fff;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(15, 61, 89, 0.3);
        }
        .drawer-stat-card .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.8;
            margin-bottom: 8px;
        }
        .drawer-stat-card.solid-purple .label,
        .drawer-stat-card.solid-blue .label {
            opacity: 0.9;
        }
        .drawer-stat-card .value {
            font-size: 24px;
            font-weight: 700;
        }
        
        .chart-section {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-top: 10px;
        }
        .chart-container-wrapper {
            position: relative;
            width: 200px;
            height: 200px;
            flex-shrink: 0;
        }
        .chart-inner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }
        .chart-inner-text .number {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            display: block;
            line-height: 1;
        }
        .chart-inner-text .label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .distribution-list {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 260px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .dist-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: #f8fafc;
            border-radius: 12px;
            font-size: 13px;
        }
        .dist-item-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #334155;
        }
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .dist-item-value {
            font-weight: 700;
            color: #0f172a;
        }
        .dist-item-value .percent {
            color: #64748b;
            font-weight: 600;
            margin-left: 8px;
        }
    </style>

    <div class="col-md-12 project-list">
        <div class="card pegawai-header-card">
            <div class="row align-items-center">
                <div class="col-md-5 mt-2 p-0">
                    <h3 class="pegawai-title">{{ $title }}</h3>
                    <p class="pegawai-subtitle">Kelola dan pantau seluruh pegawai</p>
                </div>
                <div class="col-md-7 p-0">
                    <div class="action-buttons-group">
                        <button type="button" class="btn btn-outline-resapan" id="btn-open-resapan">
                            <i class="fa fa-map me-2"></i> Resapan Tenaga Kerja
                        </button>
                        <a href="{{ url('/pegawai/export') }}{{ $_GET ? '?'.$_SERVER['QUERY_STRING'] : '' }}" class="btn btn-outline-export">
                            <i class="fa fa-download me-2"></i> Export
                        </a>
                        <button class="btn btn-outline-import" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fa fa-upload me-2"></i> Import
                        </button>
                        <a href="{{ url('/shift') }}" class="btn btn-outline-shift">
                            <i class="fa fa-clock me-2"></i> Shift
                        </a>
                        <a href="{{ url('/pegawai/tambah-pegawai') }}" class="btn btn-solid-tambah">
                            <i class="fa fa-plus me-2"></i> Tambah Pegawai
                        </a>
                    </div>

                    <!-- Import Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Import Users</h5>
                                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('/pegawai/import') }}" method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        @csrf
                                        <div class="form-group">
                                            <label for="file_excel">File Excel</label>
                                            <input type="file" name="file_excel" id="file_excel" class="form-control @error('file_excel') is-invalid @enderror">
                                            @error('file_excel')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-primary" type="submit">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards Row -->
    <div class="col-md-12">
        <div class="row stats-wrapper">
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-blue">
                        <i class="fa fa-users" style="font-size: 18px;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Total Pegawai</span>
                        <span class="stat-value">{{ $total_pegawai ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-green">
                        <i class="fa fa-user-check" style="font-size: 18px;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Aktif</span>
                        <span class="stat-value">{{ $aktif_pegawai ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-orange">
                        <i class="fa fa-clock" style="font-size: 18px;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Cuti</span>
                        <span class="stat-value">{{ $cuti_pegawai > 0 ? $cuti_pegawai : '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <div class="stat-icon-wrapper icon-red">
                        <i class="fa fa-chart-line" style="font-size: 18px;"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-label">Baru Bulan Ini</span>
                        <span class="stat-value">{{ $baru_bulan_ini > 0 ? $baru_bulan_ini : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <form action="{{ url('/pegawai') }}">
                        <div class="row mb-2">
                            <div class="col-10">
                                <input type="text" placeholder="Search...." class="form-control" value="{{ request('search') }}" name="search">
                            </div>
                            <div class="col">
                                <button type="submit" id="search"class="border-0 mt-3" style="background-color: transparent;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="border-radius: 10px">
                        <table class="table table-bordered" style="vertical-align: middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="position: sticky; left: 0; background-color: rgb(215, 215, 215); z-index: 2;">No.</th>
                                    <th style="position: sticky; left: 40px; background-color: rgb(215, 215, 215); z-index: 2; min-width: 230px;" class="text-center">Nama</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Foto</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Username</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Lokasi</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Divisi</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Role</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Dashboard</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Masa Berlaku</th>
                                    <th style="min-width: 170px; background-color:rgb(243, 243, 243);" class="text-center">Kartu</th>
                                    <th class="text-center" style="position: sticky; right: 0; background-color: rgb(215, 215, 215); z-index: 2;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data_user) <= 0)
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak Ada Data</td>
                                    </tr>
                                @else
                                    @foreach ($data_user as $key => $du)
                                        <tr>
                                            <td class="text-center" style="position: sticky; left: 0; background-color: rgb(235, 235, 235); z-index: 1;">{{ ($data_user->currentpage() - 1) * $data_user->perpage() + $key + 1 }}.</td>
                                            <td style="position: sticky; left: 40px; background-color: rgb(235, 235, 235); z-index: 1;">{{ $du->name }}</td>
                                            <td class="text-center">
                                                @if($du->foto_karyawan == null)
                                                    <img style="width: 80px; border-radius: 50px" src="{{ url('assets/img/foto_default.jpg') }}" alt="{{ $du->name ?? '-' }}">
                                                @else
                                                    <img style="width: 80px; border-radius: 50px" src="{{ url('/storage/'.$du->foto_karyawan) }}" alt="{{ $du->name ?? '-' }}">
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $du->username ?? '-' }}</td>
                                            <td>{{ $du->Lokasi->nama_lokasi ?? '-' }}</td>
                                            <td>{{ $du->Jabatan->nama_jabatan ?? '-' }}</td>
                                            <td class="text-center">
                                                @if (count($du->roles) > 0)
                                                    @foreach ($du->roles as $role)
                                                        <div class="badge" style="color: rgb(21, 47, 118); background-color:rgba(192, 218, 254, 0.889); border-radius:10px;">{{ $role->name ?? '-' }}</div>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $du->is_admin ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($du->masa_berlaku)
                                                    @php
                                                        Carbon\Carbon::setLocale('id');
                                                        $masa_berlaku = Carbon\Carbon::createFromFormat('Y-m-d', $du->masa_berlaku);
                                                        $new_masa_berlaku = $masa_berlaku->translatedFormat('d F Y');
                                                    @endphp
                                                    @if ($du->masa_berlaku <= date('Y-m-d'))
                                                        <span class="btn btn-xs"  style="color: rgba(78, 26, 26, 0.889); background-color:rgb(242, 170, 170); border-radius:10px;">{{ $new_masa_berlaku  }}</span> <br> <span class="btn btn-xs mt-2"  style="color: rgba(78, 26, 26, 0.889); background-color:rgb(242, 170, 170); border-radius:10px;">Non-Aktif</span>
                                                    @else
                                                        <span class="btn btn-xs" style="color: rgba(20, 78, 7, 0.889); background-color:rgb(186, 238, 162); border-radius:10px;">{{ $new_masa_berlaku }}</span> <br> <span class="btn btn-xs mt-2" style="color: rgba(20, 78, 7, 0.889); background-color:rgb(186, 238, 162); border-radius:10px;">Aktif</span>
                                                    @endif
                                                @else
                                                    <span style="font-size: 30px">♾️</span> <br> <span class="btn btn-xs mt-2" style="color: rgba(20, 78, 7, 0.889); background-color:rgb(186, 238, 162); border-radius:10px;">Aktif</span>
                                                @endif
                                            </td>
                                            <td><a href="{{ url('/pegawai/qrcode/'.$du->id) }}" class="btn" style="width: 150px; background-color:rgb(196, 196, 196)"><i class="fas fa-qrcode"></i> Qrcode</a></td>
                                            <td style="position: sticky; right: 0; background-color: rgb(235, 235, 235); z-index: 1;"z>
                                                <ul class="action">
                                                    <li class="edit me-2"><a href="{{ url('/pegawai/detail/'.$du->id) }}" title="Edit Pegawai"><i class="icon-pencil-alt"></i></a></li>

                                                    <li class="me-2"><a href="{{ url('/pegawai/edit-password/'.$du->id) }}" title="Ganti Password"><i class="fa fa-solid fa-key" style="color: rgb(11, 18, 222)"></i></a></li>

                                                    <li class="me-2"> <a href="{{ url('/pegawai/shift/'.$du->id) }}" title="Input Shift Pegawai"><i style="color:coral" class="fa fa-solid fa-clock"></i></a></li>

                                                    <li class="me-2"> <a href="{{ url('/pegawai/dinas-luar/'.$du->id) }}" title="Input Dinas Luar Pegawai"><i style="color:rgb(43, 198, 203)" class="fa fa-solid fa-route"></i></a></li>

                                                    <li class="me-2"> <a href="{{ url('/pegawai/kontrak/'.$du->id) }}" title="Kontrak Kerja"><i data-feather="trending-up"> </i></a></li>

                                                    @if ($du->foto_face_recognition != null && $du->foto_face_recognition != "")
                                                        <li class="me-2"><a href="{{ url('/pegawai/face/'.$du->id) }}" title="Face Recognition Terdaftar (Klik untuk ganti)"><i style="color: green" class="fa fa-solid fa-camera"></i><i class="fa fa-solid fa-check" style="color: green; font-size: 10px; margin-left: 2px;"></i></a></li>
                                                    @else
                                                        <li class="me-2"><a href="{{ url('/pegawai/face/'.$du->id) }}" title="Face Recognition Belum Terdaftar"><i style="color: red" class="fa fa-solid fa-camera"></i><i class="fa fa-solid fa-times" style="color: red; font-size: 10px; margin-left: 2px;"></i></a></li>
                                                    @endif

                                                    <li class="delete">
                                                        <form action="{{ url('/pegawai/delete/'.$du->id) }}" method="post">
                                                            @method('delete')
                                                            @csrf
                                                            <button title="Delete Pegawai" class="border-0" style="background-color: transparent;" onClick="return confirm('Are You Sure')"><i class="icon-trash"></i></button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end me-4 mt-4">
                        {{ $data_user->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide-out Drawer backdrop -->
    <div class="resapan-backdrop" id="resapanBackdrop"></div>

    <!-- Slide-out Drawer Panel -->
    <div class="resapan-drawer" id="resapanDrawer">
        <!-- Header -->
        <div class="drawer-header">
            <div class="d-flex align-items-center">
                <div class="icon-blue me-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: #eff6ff; color: #4f46e5;">
                    <i class="fa fa-map-marker-alt" style="font-size: 18px;"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">Analisis Resapan Pegawai</h4>
                    <p style="margin: 4px 0 0; font-size: 12px; color: #64748b;">Visualisasi distribusi pegawai berdasarkan lokasi dan domisili</p>
                </div>
            </div>
            <button type="button" class="drawer-close-btn" id="btn-close-drawer-x">&times;</button>
        </div>

        <!-- Body -->
        <div class="drawer-body">
            <!-- Navigation Tabs -->
            <div class="drawer-nav-tabs">
                <button type="button" class="drawer-tab active-purple" id="tab-lokasi">Lokasi Kerja</button>
                <button type="button" class="drawer-tab" id="tab-domisili">Domisili KTP</button>
            </div>

            <!-- Tab 1: Lokasi Kerja Content -->
            <div id="content-lokasi">
                <!-- Stat Cards -->
                <div class="drawer-stat-cards">
                    <div class="drawer-stat-card solid-purple">
                        <span class="label">Total Lokasi</span>
                        <span class="value">{{ $total_lokasi }}</span>
                    </div>
                    <div class="drawer-stat-card">
                        <span class="label">Lokasi Terbesar</span>
                        <span class="value" style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $lokasi_terbesar }}</span>
                    </div>
                    <div class="drawer-stat-card">
                        <span class="label">Total Pegawai</span>
                        <span class="value" style="color: #1e293b;">{{ $total_pegawai }}</span>
                    </div>
                </div>

                <!-- Donut Chart & List -->
                <div class="chart-section">
                    <div class="chart-container-wrapper">
                        <canvas id="chartLokasi" style="width: 200px; height: 200px;"></canvas>
                        <div class="chart-inner-text">
                            <span class="number">{{ $total_pegawai }}</span>
                            <span class="label">Pegawai</span>
                        </div>
                    </div>

                    <div class="distribution-list">
                        @php
                            $colors = ['#4f46e5', '#7c3aed', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#64748b'];
                        @endphp
                        @foreach($distribusi_lokasi as $idx => $item)
                            <div class="dist-item">
                                <div class="dist-item-label">
                                    <span class="dot" style="background-color: {{ $colors[$idx % count($colors)] }};"></span>
                                    <span>{{ $item['label'] }}</span>
                                </div>
                                <div class="dist-item-value">
                                    {{ $item['count'] }}
                                    <span class="percent">{{ $item['percentage'] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tab 2: Domisili KTP Content -->
            <div id="content-domisili" style="display: none;">
                <!-- Stat Cards -->
                <div class="drawer-stat-cards">
                    <div class="drawer-stat-card solid-blue">
                        <span class="label">Total Provinsi</span>
                        <span class="value">{{ $total_provinsi }}</span>
                    </div>
                    <div class="drawer-stat-card">
                        <span class="label">Provinsi Terbesar</span>
                        <span class="value" style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $provinsi_terbesar }}</span>
                    </div>
                    <div class="drawer-stat-card">
                        <span class="label">Total Pegawai</span>
                        <span class="value" style="color: #1e293b;">{{ $total_pegawai }}</span>
                    </div>
                </div>

                <!-- Donut Chart & List -->
                <div class="chart-section">
                    <div class="chart-container-wrapper">
                        <canvas id="chartDomisili" style="width: 200px; height: 200px;"></canvas>
                        <div class="chart-inner-text">
                            <span class="number">{{ $total_pegawai }}</span>
                            <span class="label">Pegawai</span>
                        </div>
                    </div>

                    <div class="distribution-list">
                        @php
                            $colorsDomisili = ['#0f3d59', '#3b82f6', '#14b8a6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#64748b'];
                        @endphp
                        @foreach($distribusi_domisili as $idx => $item)
                            <div class="dist-item">
                                <div class="dist-item-label">
                                    <span class="dot" style="background-color: {{ $colorsDomisili[$idx % count($colorsDomisili)] }};"></span>
                                    <span>{{ $item['label'] }}</span>
                                </div>
                                <div class="dist-item-value">
                                    {{ $item['count'] }}
                                    <span class="percent">{{ $item['percentage'] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="drawer-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="btn-close-panel" style="border-radius: 10px; padding: 8px 20px;">Tutup Panel</button>
        </div>
    </div>

    <!-- Drawer Logic and Charts initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btnOpen = document.getElementById('btn-open-resapan');
            var btnCloseX = document.getElementById('btn-close-drawer-x');
            var btnClosePanel = document.getElementById('btn-close-panel');
            var backdrop = document.getElementById('resapanBackdrop');
            var drawer = document.getElementById('resapanDrawer');

            var tabLokasi = document.getElementById('tab-lokasi');
            var tabDomisili = document.getElementById('tab-domisili');
            var contentLokasi = document.getElementById('content-lokasi');
            var contentDomisili = document.getElementById('content-domisili');

            var chartLokasiInstance = null;
            var chartDomisiliInstance = null;

            // Data from backend
            var lokasiData = @json($distribusi_lokasi);
            var domisiliData = @json($distribusi_domisili);

            function openDrawer() {
                drawer.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
                initCharts();
            }

            function closeDrawer() {
                drawer.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (btnOpen) btnOpen.addEventListener('click', openDrawer);
            if (btnCloseX) btnCloseX.addEventListener('click', closeDrawer);
            if (btnClosePanel) btnClosePanel.addEventListener('click', closeDrawer);
            if (backdrop) backdrop.addEventListener('click', closeDrawer);

            // Tab switching
            if (tabLokasi) {
                tabLokasi.addEventListener('click', function() {
                    tabLokasi.classList.add('active-purple');
                    tabLokasi.classList.remove('active-blue');
                    if (tabDomisili) tabDomisili.classList.remove('active-purple', 'active-blue');
                    
                    if (contentLokasi) contentLokasi.style.display = 'block';
                    if (contentDomisili) contentDomisili.style.display = 'none';
                    initCharts();
                });
            }

            if (tabDomisili) {
                tabDomisili.addEventListener('click', function() {
                    tabDomisili.classList.add('active-blue');
                    tabDomisili.classList.remove('active-purple');
                    if (tabLokasi) tabLokasi.classList.remove('active-purple', 'active-blue');
                    
                    if (contentLokasi) contentLokasi.style.display = 'none';
                    if (contentDomisili) contentDomisili.style.display = 'block';
                    initCharts();
                });
            }

            function initCharts() {
                // Lokasi Chart
                if (contentLokasi && contentLokasi.style.display !== 'none' && !chartLokasiInstance) {
                    var canvasL = document.getElementById('chartLokasi');
                    if (canvasL) {
                        var ctxL = canvasL.getContext('2d');
                        var labelsL = lokasiData.map(function(item) { return item.label; });
                        var countsL = lokasiData.map(function(item) { return item.count; });
                        var colorsL = ['#4f46e5', '#7c3aed', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#64748b'];

                        chartLokasiInstance = new Chart(ctxL, {
                            type: 'doughnut',
                            data: {
                                labels: labelsL,
                                datasets: [{
                                    data: countsL,
                                    backgroundColor: colorsL.slice(0, countsL.length),
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                cutoutPercentage: 75,
                                cutout: '75%',
                                legend: { display: false },
                                plugins: { legend: { display: false } },
                                maintainAspectRatio: false
                            }
                        });
                    }
                }

                // Domisili Chart
                if (contentDomisili && contentDomisili.style.display !== 'none' && !chartDomisiliInstance) {
                    var canvasD = document.getElementById('chartDomisili');
                    if (canvasD) {
                        var ctxD = canvasD.getContext('2d');
                        var labelsD = domisiliData.map(function(item) { return item.label; });
                        var countsD = domisiliData.map(function(item) { return item.count; });
                        var colorsD = ['#0f3d59', '#3b82f6', '#14b8a6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#64748b'];

                        chartDomisiliInstance = new Chart(ctxD, {
                            type: 'doughnut',
                            data: {
                                labels: labelsD,
                                datasets: [{
                                    data: countsD,
                                    backgroundColor: colorsD.slice(0, countsD.length),
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: {
                                cutoutPercentage: 75,
                                cutout: '75%',
                                legend: { display: false },
                                plugins: { legend: { display: false } },
                                maintainAspectRatio: false
                            }
                        });
                    }
                }
            }
        });
    </script>
@endsection




