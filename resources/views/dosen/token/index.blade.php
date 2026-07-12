@extends('templates.app')
@section('container')
<style>
/* ===== TOKEN DARING PAGE ===== */
.token-hero {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3b82c4 100%);
    border-radius: 20px;
    padding: 32px;
    color: #fff;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.token-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.token-hero::after {
    content: '';
    position: absolute;
    bottom: -30px; left: 40px;
    width: 100px; height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.token-hero h2 { font-size:22px; font-weight:700; margin-bottom:6px; }
.token-hero p  { font-size:13px; opacity:.85; margin:0; }
.token-icon-wrap {
    width:56px; height:56px;
    background: rgba(255,255,255,.18);
    border-radius: 16px;
    display:flex; align-items:center; justify-content:center;
    font-size:26px;
    margin-bottom:16px;
    backdrop-filter: blur(6px);
}

/* ===== CARD SESI PENDING ===== */
.sesi-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(30,60,114,.08);
    padding: 18px 20px;
    margin-bottom: 16px;
    border-left: 4px solid #f59e0b;
    transition: box-shadow .2s;
}
.sesi-card:hover { box-shadow: 0 6px 24px rgba(30,60,114,.14); }
.sesi-card.valid { border-left-color: #10b981; }

.sesi-meta { font-size:12px; color:#64748b; margin-bottom:4px; }
.sesi-title { font-size:15px; font-weight:700; color:#1e293b; margin-bottom:2px; }
.sesi-sub   { font-size:12px; color:#94a3b8; }

.badge-pending { background:#fef3c7; color:#92400e; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:600; }
.badge-valid   { background:#d1fae5; color:#065f46; border-radius:20px; padding:3px 10px; font-size:11px; font-weight:600; }

/* ===== TOKEN INPUT FORM ===== */
.token-form-box {
    background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
    border-radius: 16px;
    border: 2px dashed #e2e8f0;
    padding: 20px;
    margin-top: 14px;
}
.token-input-field {
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    width: 100%;
    text-align: center;
    background: #fff;
    color: #1e293b;
    outline: none;
    transition: border .2s, box-shadow .2s;
}
.token-input-field:focus {
    border-color: #2a5298;
    box-shadow: 0 0 0 3px rgba(42,82,152,.12);
}
.btn-verify {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 13px 28px;
    font-size: 14px;
    font-weight: 700;
    width: 100%;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    margin-top: 10px;
    letter-spacing: .5px;
}
.btn-verify:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(30,60,114,.35);
}
.btn-verify:active { transform: translateY(0); }

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}
.empty-state i { font-size: 48px; margin-bottom: 14px; display:block; }
.empty-state p { font-size: 14px; }

.section-title {
    font-size: 15px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

.info-hint {
    background: #eff6ff;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 12px;
    color: #1d4ed8;
    display: flex;
    gap: 8px;
    align-items: flex-start;
    margin-top: 14px;
}
.info-hint i { margin-top: 2px; flex-shrink: 0; }
</style>

<div class="mt-4">
    <div class="tf-container">

        {{-- HERO SECTION --}}
        <div class="token-hero">
            <div class="token-icon-wrap">🔑</div>
            <h2>Input Token Daring</h2>
            <p>Masukkan kode token setelah sesi mengajar daring selesai untuk mencatat gaji Anda ke sistem.</p>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius:12px; font-size:13px;">
                <i class="fa fa-check-circle text-success"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="border-radius:12px; font-size:13px;">
                <i class="fa fa-times-circle text-danger"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" style="border-radius:12px; font-size:13px;">
                <i class="fa fa-exclamation-triangle text-warning"></i> {{ session('warning') }}
            </div>
        @endif

        {{-- SESI PENDING --}}
        <div class="section-title">
            <span>⏳ Menunggu Verifikasi</span>
        </div>

        @forelse($sesiPending as $sesi)
            <div class="sesi-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="sesi-title">{{ $sesi->jadwal->mata_kuliah ?? '-' }}</div>
                        <div class="sesi-sub">Kelas: {{ $sesi->jadwal->nama_kelas ?? '-' }}</div>
                    </div>
                    <span class="badge-pending">⏳ Pending</span>
                </div>
                <div class="sesi-meta">
                    <i class="fa fa-clock-o me-1"></i>
                    Selesai: {{ $sesi->end_time ? \Carbon\Carbon::parse($sesi->end_time)->format('d M Y, H:i') : '-' }}
                </div>
                <div class="sesi-meta mb-0">
                    <i class="fa fa-key me-1"></i>
                    Token Sistem: <strong style="letter-spacing:2px; color:#1e3c72;">{{ $sesi->token_daring }}</strong>
                    <small class="text-muted ms-1">(Token ini ditampilkan untuk keperluan simulasi)</small>
                </div>

                {{-- FORM INPUT TOKEN --}}
                <div class="token-form-box">
                    <p style="font-size:13px; color:#475569; font-weight:600; margin-bottom:10px;">
                        <i class="fa fa-keyboard-o me-1"></i> Masukkan Kode Token:
                    </p>
                    <form action="{{ url('/dosen/token-daring/verify') }}" method="POST">
                        @csrf
                        <input type="hidden" name="sesi_daring_id" value="{{ $sesi->id }}">
                        <input
                            type="text"
                            name="token_input"
                            class="token-input-field"
                            placeholder="TOK-XXXXXX"
                            maxlength="20"
                            autocomplete="off"
                            oninput="this.value = this.value.toUpperCase()"
                        >
                        <button type="submit" class="btn-verify">
                            <i class="fa fa-check me-2"></i> Verifikasi Token
                        </button>
                    </form>
                </div>

                <div class="info-hint">
                    <i class="fa fa-info-circle"></i>
                    <span>Token diberikan otomatis saat Anda menjadwalkan sesi daring. Token juga ditampilkan di notifikasi saat sesi berakhir.</span>
                </div>
            </div>
        @empty
            <div class="sesi-card" style="border-left-color: #10b981;">
                <div class="empty-state">
                    <i class="fa fa-check-circle text-success"></i>
                    <p><strong>Tidak ada sesi yang menunggu verifikasi!</strong></p>
                    <p style="font-size:12px;">Semua sesi daring Anda sudah terverifikasi.</p>
                </div>
            </div>
        @endforelse

        {{-- RIWAYAT SESI TERVERIFIKASI --}}
        @if($sesiValid->count() > 0)
            <div class="section-title mt-4">
                <span>✅ Riwayat Terverifikasi</span>
            </div>

            @foreach($sesiValid as $sesi)
                <div class="sesi-card valid">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="sesi-title">{{ $sesi->jadwal->mata_kuliah ?? '-' }}</div>
                            <div class="sesi-sub">Kelas: {{ $sesi->jadwal->nama_kelas ?? '-' }}</div>
                            <div class="sesi-meta mt-1">
                                <i class="fa fa-clock-o me-1"></i>
                                {{ $sesi->end_time ? \Carbon\Carbon::parse($sesi->end_time)->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>
                        <span class="badge-valid">✅ Terverifikasi</span>
                    </div>
                </div>
            @endforeach
        @endif

        <br><br><br>
    </div>
</div>
@endsection
