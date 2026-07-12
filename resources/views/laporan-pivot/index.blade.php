@extends('templates.dashboard')
@section('isi')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    .pv-page { font-family: 'Inter', sans-serif; }

    /* Hero */
    .pv-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3652 60%, #1a56db 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .pv-hero::before {
        content:'';position:absolute;top:-50px;right:-50px;
        width:200px;height:200px;background:rgba(255,255,255,0.04);border-radius:50%;
    }
    .pv-hero-title { font-size:1.7rem;font-weight:800;margin:0;letter-spacing:-0.5px; }
    .pv-hero-sub   { font-size:0.82rem;opacity:0.7;margin:4px 0 0; }

    /* Filter card */
    .pv-filter {
        background: white;
        border-radius: 16px;
        padding: 22px 26px;
        margin-bottom: 22px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #f0f4f8;
    }
    .pv-filter label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        display: block;
        margin-bottom: 6px;
    }
    .pv-input {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #dde3ed;
        border-radius: 10px;
        font-size: 0.85rem;
        background: #f8fafc;
        color: #374151;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        transition: all 0.2s;
    }
    .pv-input:focus {
        border-color: #1d4ed8;
        background: white;
        box-shadow: 0 0 0 3px rgba(29,78,216,0.1);
    }
    .btn-pv-generate {
        background: linear-gradient(135deg, #1e3a5f, #1d4ed8);
        color: white; border: none; border-radius: 10px;
        padding: 10px 22px; font-weight: 700; font-size: 0.88rem;
        display: inline-flex; align-items: center; gap: 8px;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-pv-generate:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-pv-rekap {
        background: #f1f5f9; color: #475569; border: none;
        border-radius: 10px; padding: 10px 18px; font-weight: 600;
        font-size: 0.88rem; text-decoration: none; display: inline-flex;
        align-items: center; gap: 8px; transition: all 0.2s;
    }
    .btn-pv-rekap:hover { background: #e2e8f0; color: #1e3a5f; }
</style>

<div class="pv-page">
    <div class="pv-hero">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="pv-hero-title"><i class="fa fa-table me-2"></i>Laporan Pivot Absensi</h2>
                <p class="pv-hero-sub">Generate laporan harian, rekap bulanan &amp; analitik kehadiran pegawai</p>
            </div>
        </div>
    </div>

    <div class="pv-filter">
        <form action="{{ url('/laporan-pivot/generate') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label>Lokasi Kantor</label>
                    <select name="lokasi_id" class="pv-input">
                        <option value="">🏢 Semua Lokasi</option>
                        @foreach($lokasi as $l)
                            <option value="{{ $l->id }}" {{ request('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="pv-input" value="{{ request('tanggal_mulai', date('Y-m-01')) }}" required>
                </div>
                <div class="col-md-2">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="pv-input" value="{{ request('tanggal_akhir', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-2">
                    <label>Tipe Pegawai</label>
                    <select name="tipe_user" class="pv-input">
                        <option value="semua" {{ request('tipe_user','semua')=='semua'?'selected':'' }}>👥 Semua</option>
                        <option value="pegawai" {{ request('tipe_user')=='pegawai'?'selected':'' }}>🧑‍💼 Pegawai</option>
                        <option value="dosen" {{ request('tipe_user')=='dosen'?'selected':'' }}>👨‍🏫 Dosen</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-pv-generate">
                        <i class="fa fa-chart-bar"></i> Generate Laporan
                    </button>
                    <a href="{{ url('/laporan-pivot/rekap-bulanan') }}" class="btn-pv-rekap">
                        <i class="fa fa-calendar-alt"></i> Rekap Bulanan
                    </a>
                </div>
            </div>
            @if($errors->any())
                <div class="alert alert-danger mt-3 mb-0 rounded-3">
                    @foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
