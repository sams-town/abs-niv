@extends('templates.dashboard')
@section('isi')

<style>
    /* =========================================
       PEGAWAI KELUAR — TURNOVER DASHBOARD
    ========================================= */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    .pk-page { font-family: 'Inter', sans-serif; }

    /* --- Hero Header --- */
    .pk-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 60%, #1a56db 100%);
        border-radius: 24px;
        padding: 32px 36px;
        color: white;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .pk-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 260px; height: 260px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .pk-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; right: 120px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }
    .pk-hero-title {
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0;
    }
    .pk-hero-sub {
        font-size: 0.88rem;
        opacity: 0.7;
        margin: 6px 0 0;
    }
    .btn-hero-tambah {
        background: white;
        color: #1e3a5f;
        border: none;
        border-radius: 14px;
        padding: 12px 28px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
        z-index: 1;
    }
    .btn-hero-tambah:hover {
        background: #eff6ff;
        color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(0,0,0,0.25);
    }

    /* --- Stat Cards Row --- */
    .stat-cards { margin-bottom: 28px; }
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #f0f4f8;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(0,0,0,0.1);
    }
    .stat-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #94a3b8;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .stat-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }
    .stat-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 6px;
    }
    .stat-desc {
        font-size: 0.75rem;
        color: #94a3b8;
    }
    .stat-desc b { color: #475569; }

    /* Turnover card special */
    .turnover-card {
        background: linear-gradient(135deg, #1e3a5f, #1d4ed8);
        color: white;
        border: none;
    }
    .turnover-card .stat-label { color: rgba(255,255,255,0.65); }
    .turnover-card .stat-value { color: white; font-size: 2.6rem; }
    .turnover-card .stat-desc { color: rgba(255,255,255,0.65); }
    .turnover-card .stat-desc b { color: rgba(255,255,255,0.9); }

    /* Health badge */
    .health-badge {
        position: absolute;
        top: 18px; right: 20px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.68rem;
        font-weight: 700;
    }
    .health-sehat { background: rgba(255,255,255,0.2); color: white; }
    .health-warning { background: rgba(251,191,36,0.15); color: #b45309; }
    .health-critical { background: rgba(239,68,68,0.15); color: #dc2626; }

    /* --- Breakdown Jenis --- */
    .breakdown-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #f0f4f8;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 28px;
    }
    .breakdown-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        margin-bottom: 16px;
    }
    .jenis-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .jenis-bar-label {
        width: 140px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        flex-shrink: 0;
    }
    .jenis-bar-track {
        flex: 1;
        height: 10px;
        background: #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
    }
    .jenis-bar-fill {
        height: 100%;
        border-radius: 20px;
        transition: width 0.8s ease;
    }
    .jenis-bar-count {
        width: 30px;
        text-align: right;
        font-size: 0.82rem;
        font-weight: 700;
        color: #374151;
    }

    /* --- Turnover Gauge --- */
    .gauge-section {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #f0f4f8;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 28px;
        text-align: center;
    }
    .gauge-wrapper { position: relative; display: inline-block; }
    .gauge-label-center {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        white-space: nowrap;
    }

    /* --- Trend Chart --- */
    .trend-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #f0f4f8;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 28px;
    }

    /* --- Filter & Table --- */
    .pk-filter-card {
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f0f4f8;
    }
    .pk-filter-card .form-control, .pk-filter-card .form-select {
        border-radius: 10px;
        border: 1.5px solid #dde3ed;
        padding: 10px 14px;
        font-size: 0.875rem;
        background: #f8fafc;
    }
    .pk-filter-card .form-control:focus {
        border-color: #1d4ed8;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }
    .btn-search {
        background: linear-gradient(135deg, #1e3a5f, #1d4ed8);
        color: white; border: none; border-radius: 10px;
        padding: 10px 18px; font-weight: 600;
        transition: all 0.2s;
    }
    .btn-search:hover { opacity: 0.9; transform: translateY(-1px); }

    .pk-table-card {
        background: white; border-radius: 20px;
        overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        border: 1px solid #f0f4f8;
    }
    .pk-table-card table thead th {
        background: #f0f5fb; color: #1e3a5f;
        font-weight: 700; font-size: 0.72rem;
        text-transform: uppercase; letter-spacing: 0.5px;
        border: none; border-bottom: 2px solid #dde3ed;
        padding: 14px 14px;
    }
    .pk-table-card table tbody tr { transition: background 0.15s; }
    .pk-table-card table tbody tr:hover { background: #f8fbff; }
    .pk-table-card table tbody td {
        border: none; border-bottom: 1px solid #f0f3f8;
        padding: 13px 14px; color: #374151; font-size: 0.875rem;
        vertical-align: middle;
    }
    .badge-status {
        padding: 5px 14px; border-radius: 20px;
        font-weight: 700; font-size: 0.72rem; letter-spacing: 0.5px;
    }
    .badge-approved { background: #dcfce7; color: #15803d; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-pending  { background: #fef9c3; color: #854d0e; }
    .jenis-badge {
        padding: 4px 12px; border-radius: 8px;
        font-size: 0.75rem; font-weight: 600;
    }
    .jenis-phk      { background: #fee2e2; color: #b91c1c; }
    .jenis-mundur   { background: #fef3c7; color: #92400e; }
    .jenis-meninggal{ background: #ede9fe; color: #5b21b6; }
    .jenis-pensiun  { background: #dcfce7; color: #166534; }
    .jenis-default  { background: #f1f5f9; color: #475569; }
    .avatar-circle {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, #1e3a5f, #1d4ed8);
        color: white; display: inline-flex; align-items: center;
        justify-content: center; font-weight: 700; font-size: 0.8rem;
        margin-right: 8px; flex-shrink: 0;
    }
    .btn-action {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.8rem; transition: all 0.15s; cursor: pointer;
    }
    .btn-action-edit    { background: #fff8e1; color: #f59e0b; }
    .btn-action-edit:hover { background: #f59e0b; color: white; }
    .btn-action-delete  { background: #fee2e2; color: #ef4444; }
    .btn-action-delete:hover { background: #ef4444; color: white; }
    .btn-action-approve { background: #e0f2fe; color: #0284c7; }
    .btn-action-approve:hover { background: #0284c7; color: white; }
    .modal-hdr {
        background: linear-gradient(135deg, #1e3a5f, #1d4ed8);
        color: white; border-bottom: none; padding: 20px 24px;
    }
    .modal-content { border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
    .file-link {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; background: #eff6ff; color: #1d4ed8;
        border-radius: 8px; font-size: 0.75rem; font-weight: 600;
        text-decoration: none;
    }
    .file-link:hover { background: #dbeafe; }

    /* Rumus info box */
    .formula-box {
        background: #f0f7ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 0.78rem;
        color: #1e40af;
        margin-bottom: 20px;
    }
    .formula-box strong { font-weight: 700; }

    /* Masa kerja badge */
    .masakerja-card {
        background: linear-gradient(135deg, #059669, #10b981);
        border-radius: 20px;
        padding: 24px;
        color: white;
        height: 100%;
    }
    .masakerja-card .stat-label { color: rgba(255,255,255,0.7); }
    .masakerja-card .stat-value { color: white; font-size: 2.4rem; }
    .masakerja-card .stat-desc { color: rgba(255,255,255,0.7); }

    /* Section header */
    .section-hdr {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-hdr::after {
        content: '';
        flex: 1;
        height: 2px;
        background: #f0f5fb;
        border-radius: 2px;
    }
</style>

<div class="pk-page">
    <!-- ===== HERO HEADER ===== -->
    <div class="pk-hero mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="pk-hero-title"><i class="fa fa-sign-out-alt me-3"></i>Pegawai Keluar</h2>
                <p class="pk-hero-sub">Kelola data keberhentian & analisis turnover karyawan secara real-time</p>
            </div>
            <a href="{{ url('/exit/tambah') }}" class="btn-hero-tambah">
                <i class="fa fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    <!-- ===== STAT CARDS ===== -->
    <div class="row g-3 stat-cards">
        <!-- Total Resignasi -->
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon" style="background:#eff6ff; color:#1d4ed8;"><i class="fa fa-users"></i></div>
                    Total Resignasi
                </div>
                <div class="stat-value">{{ $totalResignasi }}</div>
                <div class="stat-desc">
                    <b>{{ $totalApproved }}</b> disetujui &bull;
                    <b>{{ $totalRejected }}</b> ditolak
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="fa fa-clock"></i></div>
                    Pending Approval
                </div>
                <div class="stat-value" style="color: {{ $pendingApproval > 0 ? '#d97706' : '#0f172a' }}">
                    {{ $pendingApproval }}
                </div>
                <div class="stat-desc">
                    @if($pendingApproval > 0)
                        <span style="color:#d97706; font-weight:600;">⚠ Menunggu tindakan</span>
                    @else
                        Semua sudah diproses
                    @endif
                </div>
            </div>
        </div>

        <!-- Keluar Bulan Ini -->
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fa fa-calendar-check"></i></div>
                    Keluar Bulan Ini
                </div>
                <div class="stat-value">{{ $keluarBulanIni }}</div>
                <div class="stat-desc">
                    Tahun ini: <b>{{ $keluarTahunIni }}</b> karyawan
                </div>
            </div>
        </div>

        <!-- Rata-rata Masa Kerja -->
        <div class="col-md-3 col-6">
            <div class="masakerja-card">
                <div class="stat-label">
                    <div class="stat-icon" style="background:rgba(255,255,255,0.2); color:white;"><i class="fa fa-hourglass-half"></i></div>
                    Rata-rata Masa Kerja
                </div>
                <div class="stat-value">{{ $rataRataMasaKerjaBulan }} <small style="font-size:1rem; font-weight:500;">bln</small></div>
                <div class="stat-desc">Sebelum keluar (APPROVED)</div>
            </div>
        </div>
    </div>

    <!-- ===== TURNOVER ROW ===== -->
    <div class="row g-3 mb-4">
        <!-- Turnover Rate Bulan Ini -->
        <div class="col-md-4">
            <div class="turnover-card stat-card">
                @php
                    $statusTO = $turnoverRateBulan <= 1 ? 'Ideal' : ($turnoverRateBulan <= 2 ? 'Perlu Perhatian' : 'Kritis');
                    $statusClass = $turnoverRateBulan <= 1 ? 'health-sehat' : ($turnoverRateBulan <= 2 ? 'health-warning' : 'health-critical');
                @endphp
                <span class="health-badge {{ $statusClass }}">{{ $statusTO }}</span>
                <div class="stat-label"><i class="fa fa-chart-line me-2"></i>Turnover Rate — Bulan Ini</div>
                <div class="stat-value">{{ number_format($turnoverRateBulan, 2) }}%</div>
                <div class="stat-desc">
                    {{ $keluarBulanIni }} keluar / {{ number_format($rataRataKaryawanBulan, 0) }} rata-rata
                </div>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.15); font-size:0.72rem; opacity:0.7;">
                    <i class="fa fa-info-circle me-1"></i> Batas wajar Indonesia: ≤ 1%/bulan
                </div>
            </div>
        </div>

        <!-- Turnover Rate Tahunan -->
        <div class="col-md-4">
            <div class="stat-card">
                @php
                    $statusTA = $turnoverRateTahunan <= 10 ? 'Ideal' : ($turnoverRateTahunan <= 15 ? 'Perlu Perhatian' : 'Kritis');
                    $classTA  = $turnoverRateTahunan <= 10 ? 'text-success' : ($turnoverRateTahunan <= 15 ? 'text-warning' : 'text-danger');
                @endphp
                <div class="stat-label">
                    <div class="stat-icon" style="background:#eff6ff; color:#7c3aed;"><i class="fa fa-calendar-alt"></i></div>
                    Turnover Rate — {{ date('Y') }}
                </div>
                <div class="stat-value {{ $classTA }}">{{ number_format($turnoverRateTahunan, 2) }}%</div>
                <div class="stat-desc">
                    {{ $keluarTahunIni }} keluar tahun ini
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted" style="font-size:0.7rem;">0%</small>
                        <small class="{{ $classTA }}" style="font-size:0.7rem; font-weight:700;">{{ number_format($turnoverRateTahunan, 1) }}%</small>
                        <small class="text-muted" style="font-size:0.7rem;">20%</small>
                    </div>
                    <div style="height:8px; background:#f1f5f9; border-radius:20px; overflow:hidden;">
                        <div style="height:100%; width:{{ min($turnoverRateTahunan * 5, 100) }}%; background: linear-gradient(90deg, #16a34a, {{ $turnoverRateTahunan > 10 ? '#f59e0b' : '#16a34a' }}, {{ $turnoverRateTahunan > 15 ? '#ef4444' : 'transparent' }}); border-radius:20px; transition: width 1s ease;"></div>
                    </div>
                    <small style="font-size:0.68rem; color:#94a3b8; display:block; margin-top:4px;">Batas wajar: ≤ 10%/tahun (Depnakertrans RI)</small>
                </div>
            </div>
        </div>

        <!-- Karyawan Aktif -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">
                    <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fa fa-user-check"></i></div>
                    Karyawan Aktif Saat Ini
                </div>
                <div class="stat-value">{{ $totalKaryawanAktif }}</div>
                <div class="stat-desc">
                    Rata-rata bulan ini: <b>{{ number_format($rataRataKaryawanBulan, 0) }}</b>
                </div>
                <div class="mt-3 pt-3" style="border-top: 1px solid #f0f5fb;">
                    <div class="formula-box mb-0">
                        <strong>Rumus Turnover (Indonesia):</strong><br>
                        Turnover = (Σ Keluar ÷ Rata-rata Karyawan) × 100%<br>
                        <span style="opacity:0.7;">Ref: BPS & Depnakertrans RI</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== BREAKDOWN JENIS + TREND ===== -->
    <div class="row g-3 mb-4">
        <!-- Breakdown per Jenis -->
        <div class="col-md-5">
            <div class="breakdown-card h-100">
                <div class="section-hdr"><i class="fa fa-chart-pie me-2"></i>Breakdown per Jenis Keberhentian</div>
                @php
                    $jenisConfig = [
                        'PHK'               => ['color' => '#ef4444', 'icon' => 'fa-ban',      'label' => 'PHK'],
                        'Mengundurkan Diri' => ['color' => '#f59e0b', 'icon' => 'fa-door-open','label' => 'Mengundurkan Diri'],
                        'Meninggal Dunia'   => ['color' => '#7c3aed', 'icon' => 'fa-cross',    'label' => 'Meninggal Dunia'],
                        'Pensiun'           => ['color' => '#16a34a', 'icon' => 'fa-award',    'label' => 'Pensiun'],
                    ];
                    $maxJenis = count($breakdownJenis) > 0 ? max($breakdownJenis) : 1;
                @endphp
                @if(count($breakdownJenis) == 0)
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-chart-pie fa-2x mb-2" style="color:#cbd5e1;"></i>
                        <p class="mb-0">Belum ada data</p>
                    </div>
                @else
                    @foreach($jenisConfig as $key => $cfg)
                        @php $count = $breakdownJenis[$key] ?? 0; @endphp
                        <div class="jenis-bar">
                            <div class="jenis-bar-label">
                                <i class="fa {{ $cfg['icon'] }} me-2" style="color:{{ $cfg['color'] }};"></i>{{ $cfg['label'] }}
                            </div>
                            <div class="jenis-bar-track">
                                <div class="jenis-bar-fill" style="width:{{ $maxJenis > 0 ? ($count/$maxJenis)*100 : 0 }}%; background:{{ $cfg['color'] }};"></div>
                            </div>
                            <div class="jenis-bar-count">{{ $count }}</div>
                        </div>
                    @endforeach

                    <!-- Pie chart visual sederhana -->
                    <div class="mt-3 pt-3" style="border-top:2px solid #f0f5fb;">
                        <canvas id="jenisChart" height="160"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <!-- Trend 12 Bulan -->
        <div class="col-md-7">
            <div class="trend-card h-100">
                <div class="section-hdr"><i class="fa fa-chart-area me-2"></i>Trend Pegawai Keluar — 12 Bulan Terakhir</div>
                <canvas id="trendChart" height="200"></canvas>
                <div class="mt-3 text-center">
                    @php
                        $maxTrend = max(array_column($trendBulanan, 'jumlah'));
                        $bulanTertinggi = collect($trendBulanan)->sortByDesc('jumlah')->first();
                    @endphp
                    @if($maxTrend > 0)
                    <span style="font-size:0.75rem; color:#64748b;">
                        Puncak tertinggi: <strong style="color:#1d4ed8;">{{ $bulanTertinggi['label'] }}</strong>
                        ({{ $bulanTertinggi['jumlah'] }} karyawan)
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FILTER ===== -->
    <div class="pk-filter-card">
        <form action="{{ url('/exit') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-secondary" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Nama Pegawai</label>
                    <input type="text" class="form-control" name="nama" placeholder="Cari nama pegawai..." value="{{ request('nama') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="mulai" value="{{ request('mulai') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px;">Tanggal Akhir</label>
                    <input type="date" class="form-control" name="akhir" value="{{ request('akhir') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn-search w-100">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ===== TABEL DATA ===== -->
    <div class="pk-table-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width:50px;">No.</th>
                        <th style="min-width:220px;">Nama Pegawai</th>
                        <th class="text-center" style="min-width:120px;">Tgl. Masuk</th>
                        <th class="text-center" style="min-width:120px;">Tgl. Keluar</th>
                        <th class="text-center" style="min-width:90px;">Masa Kerja</th>
                        <th class="text-center" style="min-width:160px;">Jenis Keberhentian</th>
                        <th style="min-width:200px;">Alasan</th>
                        <th class="text-center" style="min-width:130px;">File</th>
                        <th class="text-center" style="min-width:150px;">Approval</th>
                        <th class="text-center" style="min-width:110px;">Status</th>
                        <th style="min-width:180px;">Catatan</th>
                        <th class="text-center" style="min-width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($pegawai_keluars) <= 0)
                        <tr>
                            <td colspan="12">
                                <div class="text-center py-5">
                                    <i class="fa fa-users-slash fa-3x mb-3" style="color:#cbd5e1;"></i>
                                    <p class="text-muted">Tidak ada data pegawai keluar</p>
                                </div>
                            </td>
                        </tr>
                    @else
                        @foreach ($pegawai_keluars as $key => $pegawai_keluar)
                            @php
                                // Hitung masa kerja
                                $masaKerja = '-';
                                if ($pegawai_keluar->user && $pegawai_keluar->user->tgl_join && $pegawai_keluar->tanggal) {
                                    $join   = \Carbon\Carbon::parse($pegawai_keluar->user->tgl_join);
                                    $keluar = \Carbon\Carbon::parse($pegawai_keluar->tanggal);
                                    $totalBulan = $join->diffInMonths($keluar);
                                    $tahunMK    = floor($totalBulan / 12);
                                    $bulanMK    = $totalBulan % 12;
                                    $masaKerja  = ($tahunMK > 0 ? $tahunMK.'th ' : '') . $bulanMK.'bln';
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold text-secondary">
                                    {{ ($pegawai_keluars->currentpage() - 1) * $pegawai_keluars->perpage() + $key + 1 }}.
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle">{{ strtoupper(substr($pegawai_keluar->user->name ?? 'U', 0, 1)) }}</div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:0.875rem;">{{ $pegawai_keluar->user->name ?? '-' }}</div>
                                            @if($pegawai_keluar->user && $pegawai_keluar->user->Jabatan)
                                                <div style="font-size:0.72rem; color:#94a3b8;">{{ $pegawai_keluar->user->Jabatan->nama ?? '' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($pegawai_keluar->user && $pegawai_keluar->user->tgl_join)
                                        <span style="font-size:0.82rem;">
                                            {{ \Carbon\Carbon::parse($pegawai_keluar->user->tgl_join)->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($pegawai_keluar->tanggal)
                                        @php Carbon\Carbon::setLocale('id'); @endphp
                                        <span style="font-size:0.82rem;">
                                            {{ \Carbon\Carbon::parse($pegawai_keluar->tanggal)->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span style="font-size:0.8rem; font-weight:600; color:#475569;">{{ $masaKerja }}</span>
                                </td>
                                <td class="text-center">
                                    @php $jenis = $pegawai_keluar->jenis ?? '-'; @endphp
                                    @if($jenis == 'PHK')
                                        <span class="jenis-badge jenis-phk"><i class="fa fa-ban me-1"></i>PHK</span>
                                    @elseif($jenis == 'Mengundurkan Diri')
                                        <span class="jenis-badge jenis-mundur"><i class="fa fa-door-open me-1"></i>Undur Diri</span>
                                    @elseif($jenis == 'Meninggal Dunia')
                                        <span class="jenis-badge jenis-meninggal"><i class="fa fa-cross me-1"></i>Meninggal</span>
                                    @elseif($jenis == 'Pensiun')
                                        <span class="jenis-badge jenis-pensiun"><i class="fa fa-award me-1"></i>Pensiun</span>
                                    @else
                                        <span class="jenis-badge jenis-default">{{ $jenis }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:0.82rem; color:#475569; line-height:1.4;">
                                        {!! $pegawai_keluar->alasan ? nl2br(e(Str::limit($pegawai_keluar->alasan, 80))) : '-' !!}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($pegawai_keluar->pegawai_keluar_file_path)
                                        <a href="{{ url('/storage/'.$pegawai_keluar->pegawai_keluar_file_path) }}" class="file-link" target="_blank">
                                            <i class="fa fa-download"></i> File
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pegawai_keluar->approvedBy)
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <div class="avatar-circle" style="width:26px;height:26px;font-size:0.65rem;margin-right:4px;">
                                                {{ strtoupper(substr($pegawai_keluar->approvedBy->name, 0, 1)) }}
                                            </div>
                                            <span style="font-size:0.78rem;">{{ $pegawai_keluar->approvedBy->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($pegawai_keluar->status == 'REJECTED')
                                        <span class="badge-status badge-rejected"><i class="fa fa-times-circle me-1"></i>REJECTED</span>
                                    @elseif($pegawai_keluar->status == 'APPROVED')
                                        <span class="badge-status badge-approved"><i class="fa fa-check-circle me-1"></i>APPROVED</span>
                                    @else
                                        <span class="badge-status badge-pending"><i class="fa fa-clock me-1"></i>PENDING</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:0.78rem; color:#475569; line-height:1.4;">
                                        {!! $pegawai_keluar->notes ? nl2br(e($pegawai_keluar->notes)) : '<span class="text-muted">-</span>' !!}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        @if ($pegawai_keluar->status == 'PENDING')
                                            <a href="{{ url('/exit/edit/'.$pegawai_keluar->id) }}" class="btn-action btn-action-edit" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ url('/exit/delete/'.$pegawai_keluar->id) }}" method="post" class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button class="btn-action btn-action-delete" title="Hapus" onClick="return confirm('Yakin hapus data ini?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (($pegawai_keluar->user && $pegawai_keluar->user->Jabatan && $pegawai_keluar->user->Jabatan->manager == auth()->user()->id) || auth()->user()->is_admin == 'admin')
                                            <button class="btn-action btn-action-approve" title="Approval"
                                                type="button" data-bs-toggle="modal" data-bs-target="#approvalModal{{ $pegawai_keluar->id }}">
                                                <i class="fa fa-check-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Approval Modal -->
                            @if (($pegawai_keluar->user && $pegawai_keluar->user->Jabatan && $pegawai_keluar->user->Jabatan->manager == auth()->user()->id) || auth()->user()->is_admin == 'admin')
                            <div class="modal fade" id="approvalModal{{ $pegawai_keluar->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-hdr">
                                            <div class="d-flex align-items-center gap-3">
                                                <div style="width:42px;height:42px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                                    <i class="fa fa-check-circle fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0 fw-bold">Approval Pegawai Keluar</h5>
                                                    <small class="opacity-75">{{ $pegawai_keluar->user->name ?? '' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <form action="{{ url('/exit/approval/'.$pegawai_keluar->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;color:#1e3a5f;">Keputusan</label>
                                                    <select name="status" class="form-select" style="border-radius:10px;border:1.5px solid #dde3ed;padding:10px 14px;">
                                                        <option value="">-- Pilih Keputusan --</option>
                                                        <option value="APPROVED" {{ $pegawai_keluar->status == 'APPROVED' ? 'selected' : '' }}>✅ APPROVE</option>
                                                        <option value="REJECTED" {{ $pegawai_keluar->status == 'REJECTED' ? 'selected' : '' }}>❌ REJECT</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;color:#1e3a5f;">Catatan</label>
                                                    <textarea class="form-control" name="notes" rows="3" placeholder="Catatan (opsional)..." style="border-radius:10px;border:1.5px solid #dde3ed;resize:none;">{{ old('notes') }}</textarea>
                                                </div>
                                                <input type="hidden" name="approved_by" value="{{ auth()->user()->id }}">
                                            </div>
                                            <div class="modal-footer d-flex justify-content-end gap-2" style="border-top:1px solid #f0f5fb;padding:16px 24px;">
                                                <button class="btn btn-light px-4" type="button" data-bs-dismiss="modal" style="border-radius:10px;">Batal</button>
                                                <button class="btn btn-primary px-4 fw-bold" type="submit" style="border-radius:10px;background:linear-gradient(135deg,#1e3a5f,#1d4ed8);border:none;">
                                                    <i class="fa fa-save me-1"></i> Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end px-4 py-3" style="border-top:1px solid #f0f3f8;">
            {{ $pegawai_keluars->links() }}
        </div>
    </div>
</div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---- TREND CHART (Line) ----
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const trendLabels  = {!! json_encode(array_column($trendBulanan, 'label')) !!};
        const trendData    = {!! json_encode(array_column($trendBulanan, 'jumlah')) !!};
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Pegawai Keluar',
                    data: trendData,
                    borderColor: '#1d4ed8',
                    backgroundColor: 'rgba(29, 78, 216, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#1d4ed8',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#94a3b8',
                        bodyColor: '#fff',
                        padding: 12,
                        callbacks: {
                            label: ctx => ` ${ctx.raw} karyawan keluar`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 },
                            color: '#94a3b8'
                        },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    }
                }
            }
        });
    }

    // ---- JENIS CHART (Doughnut) ----
    const jenisCtx = document.getElementById('jenisChart');
    if (jenisCtx) {
        const jenisData = {!! json_encode(array_values($breakdownJenis)) !!};
        const jenisLabels = {!! json_encode(array_keys($breakdownJenis)) !!};
        const jenisColors = {
            'PHK': '#ef4444',
            'Mengundurkan Diri': '#f59e0b',
            'Meninggal Dunia': '#7c3aed',
            'Pensiun': '#16a34a',
        };
        const colors = jenisLabels.map(l => jenisColors[l] || '#94a3b8');

        if (jenisData.length > 0) {
            new Chart(jenisCtx, {
                type: 'doughnut',
                data: {
                    labels: jenisLabels,
                    datasets: [{
                        data: jenisData,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 11 },
                                color: '#374151',
                                padding: 16,
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.raw} orang`
                            }
                        }
                    }
                }
            });
        }
    }

    // Filter tanggal auto sync
    const mulai = document.querySelector('input[name="mulai"]');
    const akhir = document.querySelector('input[name="akhir"]');
    if (mulai && akhir) {
        mulai.addEventListener('change', function() {
            if (!akhir.value) akhir.value = this.value;
        });
    }
});
</script>
@endpush
@endsection
