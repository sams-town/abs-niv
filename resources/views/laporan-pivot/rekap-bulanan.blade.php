@extends('templates.dashboard')
@section('isi')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    .pv-page { font-family: 'Inter', sans-serif; }

    .pv-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3652 60%, #7c3aed 100%);
        border-radius: 20px; padding: 24px 30px; color: white;
        margin-bottom: 22px; position: relative; overflow: hidden;
    }
    .pv-hero::before { content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,0.04);border-radius:50%; }
    .pv-hero-title { font-size:1.5rem;font-weight:800;margin:0; }
    .pv-hero-sub   { font-size:0.8rem;opacity:0.7;margin:4px 0 0; }

    .pv-filter {
        background: white; border-radius: 14px; padding: 18px 22px;
        margin-bottom: 18px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border:1px solid #f0f4f8;
    }
    .pv-filter label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#64748b;margin-bottom:5px;display:block; }
    .pv-input {
        width:100%; padding:10px 14px; border:1.5px solid #dde3ed; border-radius:10px;
        font-size:0.85rem; background:#f8fafc; color:#374151; outline:none; appearance:none; -webkit-appearance:none; transition:all 0.2s;
    }
    .pv-input:focus { border-color:#7c3aed;background:white;box-shadow:0 0 0 3px rgba(124,58,237,0.1); }
    .btn-pv-gen { background:linear-gradient(135deg,#5b21b6,#7c3aed);color:white;border:none;border-radius:10px;padding:10px 20px;font-weight:700;font-size:0.88rem;display:inline-flex;align-items:center;gap:7px;cursor:pointer;transition:all 0.2s; }
    .btn-pv-gen:hover { opacity:0.9;transform:translateY(-1px); }
    .btn-pv-back { background:#f1f5f9;color:#475569;border:none;border-radius:10px;padding:10px 18px;font-weight:600;font-size:0.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:7px; }
    .btn-pv-back:hover { background:#e2e8f0; }

    /* Monthly table */
    .pv-table-card { background:white;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.07);border:1px solid #f0f4f8; }
    .pv-table { width:100%;font-size:12px;border-collapse:collapse; }
    .pv-table thead th {
        background:#0f172a;color:white;padding:11px 8px;
        font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;
        text-align:center;border:none;white-space:nowrap;
    }
    .pv-table thead th.th-name { text-align:left;padding-left:14px;min-width:200px; }
    .pv-table tbody tr { transition:background 0.12s; }
    .pv-table tbody tr:hover { background:#f0f5ff; }
    .pv-table tbody tr:nth-child(even) { background:#fafcff; }
    .pv-table tbody tr:nth-child(even):hover { background:#eff6ff; }
    .pv-table tbody td { padding:10px 7px;text-align:center;border-bottom:1px solid #f0f5fb;vertical-align:middle; }
    .pv-table tbody td.td-name { text-align:left;padding-left:12px;font-weight:600;color:#1e293b;font-size:0.84rem;white-space:nowrap; }

    .bulan-cell {
        font-weight:700;font-size:13px;
        min-width:36px;
    }
    .bulan-zero { color:#e2e8f0; font-weight:400; }
    .bulan-low  { color:#fca5a5; }
    .bulan-mid  { color:#f59e0b; }
    .bulan-high { color:#16a34a; }

    .total-cell { font-weight:800;font-size:14px;color:#1d4ed8;min-width:50px; }
    .avg-cell   { font-size:12px;color:#64748b; min-width:40px; }

    .heatmap-bar {
        height:6px;border-radius:3px;margin-top:2px;background:#e2e8f0;overflow:hidden;min-width:32px;
    }
    .heatmap-fill { height:100%;border-radius:3px;transition:width 0.6s ease; }

    tfoot td { background:#1e3a5f!important;color:white!important;font-weight:800;padding:10px 7px;font-size:12px;text-align:center; }
    tfoot td.td-name { text-align:left;padding-left:14px;font-size:0.82rem; }

    .badge-tipe { padding:3px 9px;border-radius:8px;font-size:0.65rem;font-weight:700; }
    .badge-dosen { background:#ede9fe;color:#5b21b6; }
    .badge-pegawai { background:#dbeafe;color:#1e40af; }
    .badge-other { background:#f1f5f9;color:#475569; }

    .search-bar { padding:8px 14px;border:1.5px solid #dde3ed;border-radius:9px;font-size:0.82rem;background:#f8fafc;width:220px;outline:none;transition:all 0.2s; }
    .search-bar:focus { border-color:#7c3aed;background:white; }
    .toolbar { display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
</style>

<div class="pv-page">
    <div class="pv-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="pv-hero-title"><i class="fa fa-calendar-alt me-2"></i>Rekap Bulanan Absensi</h2>
                <p class="pv-hero-sub">Ringkasan kehadiran per bulan dalam satu tahun — Tahun {{ $tahun }}</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="pv-filter">
        <form action="{{ url('/laporan-pivot/rekap-bulanan') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="pv-input" value="{{ $tahun }}" min="2018" max="2035" placeholder="Tahun">
                </div>
                <div class="col-md-3">
                    <label>Lokasi Kantor</label>
                    <select name="lokasi_id" class="pv-input">
                        <option value="">🏢 Semua Lokasi</option>
                        @foreach($lokasi as $l)
                            <option value="{{ $l->id }}" {{ $lokasi_id == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tipe Pegawai</label>
                    <select name="tipe_user" class="pv-input">
                        <option value="semua"   {{ $tipe_user=='semua'   ? 'selected' : '' }}>👥 Semua</option>
                        <option value="pegawai" {{ $tipe_user=='pegawai' ? 'selected' : '' }}>🧑‍💼 Pegawai</option>
                        <option value="dosen"   {{ $tipe_user=='dosen'   ? 'selected' : '' }}>👨‍🏫 Dosen</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 flex-wrap align-items-end">
                    <button type="submit" class="btn-pv-gen">
                        <i class="fa fa-sync"></i> Tampilkan
                    </button>
                    <a href="{{ url('/laporan-pivot') }}" class="btn-pv-back">
                        <i class="fa fa-table"></i> Pivot Harian
                    </a>
                </div>
            </div>
        </form>
    </div>

    @php
        // Hitung maksimal hadir per bulan untuk heatmap
        $maxPerBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $maxPerBulan[$m] = collect($rows)->max(fn($r) => $r['monthly'][$m] ?? 0);
        }
        $maxTotal = collect($rows)->max(fn($r) => $r['total']) ?: 1;

        // Summary baris bawah
        $totalPerBulan = [];
        for ($m = 1; $m <= 12; $m++) {
            $totalPerBulan[$m] = collect($rows)->sum(fn($r) => $r['monthly'][$m] ?? 0);
        }
        $grandTotal = collect($rows)->sum(fn($r) => $r['total']);
    @endphp

    <!-- Toolbar -->
    <div class="toolbar">
        <input type="text" id="searchPegawai" class="search-bar" placeholder="🔍 Cari nama pegawai...">
        <span style="margin-left:auto;font-size:0.78rem;color:#64748b;">
            <b>{{ count($rows) }}</b> pegawai &bull; Tahun <b>{{ $tahun }}</b>
        </span>
    </div>

    <!-- Table -->
    @if(count($rows) == 0)
        <div class="pv-table-card" style="text-align:center;padding:60px;">
            <i class="fa fa-calendar-times fa-3x mb-3" style="color:#cbd5e1;"></i>
            <p class="text-muted">Tidak ada data untuk tahun {{ $tahun }}.</p>
        </div>
    @else
    <div class="pv-table-card">
        <div style="overflow-x:auto;">
            <table class="pv-table" id="rekapTable">
                <thead>
                    <tr>
                        <th style="width:36px;">No</th>
                        <th class="th-name">Nama Pegawai</th>
                        <th style="min-width:60px;">Tipe</th>
                        <th style="min-width:100px;">Jabatan</th>
                        @foreach($bulan_list as $bi => $b)
                            <th style="min-width:50px;">{{ $b }}</th>
                        @endforeach
                        <th style="min-width:55px;">Total</th>
                        <th style="min-width:45px;">Rata²</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php
                        $u = $row['user'];
                        $avg = $row['total'] > 0 ? round($row['total'] / 12, 1) : 0;
                        $tipe = $u->tipe_user ?? '';
                        $badgeClass = $tipe === 'dosen' ? 'badge-dosen' : ($tipe ? 'badge-pegawai' : 'badge-other');
                        $badgeLabel = $tipe === 'dosen' ? 'Dosen' : ($tipe === 'pegawai' ? 'Pegawai' : ucfirst($tipe));
                    @endphp
                    <tr class="pegawai-row">
                        <td style="color:#94a3b8;font-weight:600;font-size:11px;">{{ $i + 1 }}</td>
                        <td class="td-name">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,#5b21b6,#7c3aed);color:white;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex-shrink:0;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span class="pegawai-name">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-tipe {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        </td>
                        <td style="font-size:0.72rem;color:#64748b;white-space:nowrap;">{{ $u->Jabatan->nama ?? '-' }}</td>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $val     = $row['monthly'][$m] ?? 0;
                                $max     = $maxPerBulan[$m] ?: 1;
                                $pct     = $max > 0 ? round($val / $max * 100) : 0;
                                $cellClass = $val == 0 ? 'bulan-zero' : ($val < 5 ? 'bulan-low' : ($val < 15 ? 'bulan-mid' : 'bulan-high'));
                                $fillColor = $val == 0 ? '#e2e8f0' : ($val < 5 ? '#ef4444' : ($val < 15 ? '#f59e0b' : '#16a34a'));
                            @endphp
                            <td>
                                <div class="bulan-cell {{ $cellClass }}">{{ $val }}</div>
                                <div class="heatmap-bar">
                                    <div class="heatmap-fill" style="width:{{ $pct }}%;background:{{ $fillColor }};"></div>
                                </div>
                            </td>
                        @endfor
                        <td class="total-cell">{{ $row['total'] }}</td>
                        <td class="avg-cell">{{ $avg }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="td-name" style="font-size:0.82rem;font-weight:800;">
                            TOTAL ({{ count($rows) }} Pegawai)
                        </td>
                        @for($m = 1; $m <= 12; $m++)
                            <td style="font-weight:800;color:{{ $totalPerBulan[$m] == 0 ? '#94a3b8' : '#4ade80' }};">
                                {{ $totalPerBulan[$m] }}
                            </td>
                        @endfor
                        <td style="font-weight:800;color:#60a5fa;font-size:14px;">{{ $grandTotal }}</td>
                        <td style="color:#94a3b8;font-size:12px;">{{ count($rows) > 0 ? round($grandTotal / count($rows), 1) : 0 }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Legend Heatmap -->
        <div style="padding:12px 18px;border-top:1px solid #f0f5fb;background:#fafcff;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <span style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Heatmap:</span>
            <div style="display:flex;align-items:center;gap:5px;font-size:0.72rem;color:#94a3b8;">
                <div style="width:14px;height:14px;border-radius:3px;background:#e2e8f0;"></div> 0 hari
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:0.72rem;color:#ef4444;">
                <div style="width:14px;height:14px;border-radius:3px;background:#ef4444;"></div> 1–4 hari
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:0.72rem;color:#d97706;">
                <div style="width:14px;height:14px;border-radius:3px;background:#f59e0b;"></div> 5–14 hari
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:0.72rem;color:#16a34a;">
                <div style="width:14px;height:14px;border-radius:3px;background:#16a34a;"></div> ≥15 hari
            </div>
            <span style="margin-left:auto;font-size:0.7rem;color:#94a3b8;">*Jumlah hari hadir (Masuk + Izin Telat + Izin Pulang Cepat)</span>
        </div>
    </div>
    @endif
</div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchPegawai').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.pegawai-row').forEach(function(row) {
            const name = row.querySelector('.pegawai-name')?.textContent?.toLowerCase() || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
