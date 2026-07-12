@extends('templates.dashboard')
@section('isi')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    .pv-page { font-family: 'Inter', sans-serif; }

    .pv-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3652 60%, #1a56db 100%);
        border-radius: 20px; padding: 24px 30px; color: white;
        margin-bottom: 22px; position: relative; overflow: hidden;
    }
    .pv-hero::before { content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,0.04);border-radius:50%; }
    .pv-hero-title { font-size:1.5rem;font-weight:800;margin:0;letter-spacing:-0.5px; }
    .pv-hero-sub   { font-size:0.8rem;opacity:0.7;margin:4px 0 0; }

    /* Stat Pills */
    .stat-pills { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:22px; }
    .stat-pill {
        background: white; border-radius: 14px; padding: 16px 20px;
        flex: 1; min-width: 130px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        border: 1px solid #f0f4f8; text-align: center;
    }
    .stat-pill .val { font-size:1.7rem;font-weight:800;line-height:1; }
    .stat-pill .lbl { font-size:0.66rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-top:4px; }
    .pill-hadir .val { color: #16a34a; }
    .pill-alfa  .val { color: #ef4444; }
    .pill-sakit .val { color: #f59e0b; }
    .pill-cuti  .val { color: #3b82f6; }
    .pill-izin  .val { color: #8b5cf6; }
    .pill-libur .val { color: #6b7280; }
    .pill-pct   .val { color: #0ea5e9; }

    /* Toolbar */
    .pv-toolbar {
        background: white; border-radius: 14px; padding: 16px 22px;
        margin-bottom: 18px; display:flex; align-items:center; gap:10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #f0f4f8;
        flex-wrap: wrap;
    }
    .btn-tool {
        display:inline-flex; align-items:center; gap:6px;
        padding: 9px 16px; border-radius: 9px; font-weight: 600;
        font-size: 0.82rem; text-decoration: none; border: none;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-tool-excel { background:#dcfce7; color:#166534; }
    .btn-tool-excel:hover { background:#16a34a; color:white; }
    .btn-tool-pdf   { background:#fee2e2; color:#991b1b; }
    .btn-tool-pdf:hover { background:#ef4444; color:white; }
    .btn-tool-back  { background:#f1f5f9; color:#475569; }
    .btn-tool-back:hover { background:#e2e8f0; color:#1e293b; }
    .pv-period {
        margin-left: auto;
        font-size: 0.8rem; color: #64748b;
        background: #f8fafc; padding: 7px 14px;
        border-radius: 8px; border: 1px solid #e2e8f0;
    }
    .pv-period b { color: #1e3a5f; }

    /* Charts */
    .chart-row { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:22px; }
    .chart-card {
        background:white; border-radius:14px; padding:18px;
        box-shadow:0 2px 10px rgba(0,0,0,0.06); border:1px solid #f0f4f8;
    }
    .chart-title {
        font-size:0.68rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.8px; color:#64748b; margin-bottom:12px;
        display:flex; align-items:center; gap:6px;
    }

    /* Table */
    .pv-table-card {
        background: white; border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07); border: 1px solid #f0f4f8;
    }
    .pv-table {
        width: 100%; font-size: 11.5px; border-collapse: collapse;
    }
    .pv-table thead tr:first-child th {
        background: #0f172a; color: white; padding: 10px 6px;
        font-weight: 700; font-size: 0.65rem; text-transform: uppercase;
        letter-spacing: 0.5px; white-space: nowrap; text-align: center;
        border: none; position: sticky; top: 0; z-index: 10;
    }
    .pv-table thead tr:first-child th.th-name { text-align: left; padding-left: 14px; }
    .pv-table thead tr.th-dates th {
        background: #1e3a5f; color: rgba(255,255,255,0.85);
        padding: 7px 4px; font-size: 0.62rem; font-weight: 700;
        text-align: center; border: none; min-width: 26px;
    }
    .pv-table thead tr.th-dates th.sun { background: #374151; }
    .pv-table tbody tr { transition: background 0.12s; }
    .pv-table tbody tr:hover { background: #f8faff; }
    .pv-table tbody td {
        padding: 7px 5px; text-align: center; font-size: 11px;
        border-bottom: 1px solid #f0f5fb; vertical-align: middle;
    }
    .pv-table tbody td.td-name {
        text-align: left; padding-left: 12px; white-space: nowrap;
        font-weight: 600; color: #1e293b; font-size: 0.82rem;
        min-width: 180px;
    }
    .pv-table tbody td.td-nip { color:#94a3b8; font-size:0.7rem; }
    .pv-table tbody td.td-jabatan { color:#64748b; font-size:0.72rem; min-width:100px; }

    /* Status codes */
    .code {
        display: inline-block; width: 22px; height: 22px;
        border-radius: 5px; font-size: 9px; font-weight: 800;
        line-height: 22px; text-align: center; letter-spacing: 0;
    }
    .code-H  { background:#dcfce7; color:#166534; }
    .code-IT { background:#a7f3d0; color:#065f46; }
    .code-IP { background:#bbf7d0; color:#065f46; }
    .code-C  { background:#dbeafe; color:#1e40af; }
    .code-I  { background:#ede9fe; color:#5b21b6; }
    .code-S  { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; }
    .code-L  { background:#f1f5f9; color:#64748b; }
    .code-A  { background:#fee2e2; color:#991b1b; }
    .code-sun{ opacity: 0.45; }
    .code-def{ background:#f1f5f9; color:#94a3b8; }

    /* Summary cols */
    .td-sum { font-weight:700; font-size:12px; min-width:28px; }
    .td-h   { color:#16a34a; }
    .td-c   { color:#1d4ed8; }
    .td-i   { color:#7c3aed; }
    .td-a   { color:#dc2626; }
    .td-s   { color:#d97706; }
    .td-l   { color:#64748b; }
    .td-it  { color:#059669; }
    .td-ip  { color:#10b981; }
    .td-pct {
        font-weight:800; font-size:12px;
        min-width: 42px;
    }

    /* Legend */
    .legend {
        display:flex; flex-wrap:wrap; gap:10px; padding:14px 18px;
        border-top:1px solid #f0f5fb; background:#fafcff;
    }
    .legend-item { display:flex; align-items:center; gap:5px; font-size:0.72rem; color:#475569; }
    .legend-dot { width:18px;height:18px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:800; }

    /* Search input */
    .pv-search {
        padding:8px 14px; border:1.5px solid #dde3ed; border-radius:9px;
        font-size:0.82rem; background:#f8fafc; width:220px; outline:none;
        transition:all 0.2s;
    }
    .pv-search:focus { border-color:#1d4ed8; background:white; }
    .table-scroll { overflow-x: auto; max-height: calc(100vh - 340px); }
    tbody tr:nth-child(even) { background: #fafcff; }
    tbody tr:nth-child(even):hover { background: #f0f5ff; }
    .no-data { text-align:center; padding:60px 20px; }
    .no-data i { font-size:3rem; color:#cbd5e1; margin-bottom:12px; display:block; }
    .no-data p { color:#94a3b8; margin:0; }
</style>

<div class="pv-page">
    <!-- HERO -->
    <div class="pv-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="pv-hero-title"><i class="fa fa-table me-2"></i>Laporan Pivot Absensi</h2>
                <p class="pv-hero-sub">
                    Periode: <strong>{{ \Carbon\Carbon::parse($mulai)->translatedFormat('d F Y') }}</strong>
                    &mdash; <strong>{{ \Carbon\Carbon::parse($akhir)->translatedFormat('d F Y') }}</strong>
                    &bull; {{ count($dates) }} hari kerja
                </p>
            </div>
        </div>
    </div>

    @if(count($rows) == 0)
        <div class="pv-table-card">
            <div class="no-data">
                <i class="fa fa-table"></i>
                <p>Tidak ada data untuk periode ini. Silakan ubah filter dan generate ulang.</p>
                <a href="{{ url('/laporan-pivot') }}" class="btn-tool btn-tool-back mt-3 d-inline-flex">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    @else

    {{-- Hitung summary global --}}
    @php
        $totalH  = collect($rows)->sum(fn($r) => $r['summary']['hadir']);
        $totalC  = collect($rows)->sum(fn($r) => $r['summary']['cuti']);
        $totalI  = collect($rows)->sum(fn($r) => $r['summary']['izin']);
        $totalA  = collect($rows)->sum(fn($r) => $r['summary']['alfa']);
        $totalS  = collect($rows)->sum(fn($r) => $r['summary']['sakit']);
        $totalL  = collect($rows)->sum(fn($r) => $r['summary']['libur']);
        $grandTotal = $totalH + $totalC + $totalI + $totalA + $totalS;
        $avgPct  = $grandTotal > 0 ? round($totalH / $grandTotal * 100, 1) : 0;
        $peserta = count($rows);
    @endphp

    <!-- STAT PILLS -->
    <div class="stat-pills">
        <div class="stat-pill pill-hadir">
            <div class="val">{{ $totalH }}</div>
            <div class="lbl">⬛ Hadir (H)</div>
        </div>
        <div class="stat-pill pill-alfa">
            <div class="val">{{ $totalA }}</div>
            <div class="lbl">🔴 Alfa (A)</div>
        </div>
        <div class="stat-pill pill-sakit">
            <div class="val">{{ $totalS }}</div>
            <div class="lbl">🟡 Sakit (S)</div>
        </div>
        <div class="stat-pill pill-cuti">
            <div class="val">{{ $totalC }}</div>
            <div class="lbl">🔵 Cuti (C)</div>
        </div>
        <div class="stat-pill pill-izin">
            <div class="val">{{ $totalI }}</div>
            <div class="lbl">🟣 Izin (I)</div>
        </div>
        <div class="stat-pill pill-libur">
            <div class="val">{{ $totalL }}</div>
            <div class="lbl">⚪ Libur (L)</div>
        </div>
        <div class="stat-pill pill-pct">
            <div class="val">{{ $avgPct }}%</div>
            <div class="lbl">📊 Kehadiran</div>
        </div>
        <div class="stat-pill" style="background:linear-gradient(135deg,#1e3a5f,#1d4ed8);">
            <div class="val" style="color:white;">{{ $peserta }}</div>
            <div class="lbl" style="color:rgba(255,255,255,0.7);">👥 Pegawai</div>
        </div>
    </div>

    <!-- CHARTS ROW -->
    @if($chartData)
    <div class="chart-row">
        <div class="chart-card" style="grid-column: span 1;">
            <div class="chart-title"><i class="fa fa-chart-pie" style="color:#1d4ed8;"></i> Distribusi Status</div>
            <canvas id="donutChart" height="180"></canvas>
        </div>
        <div class="chart-card" style="grid-column: span 2;">
            <div class="chart-title"><i class="fa fa-chart-area" style="color:#16a34a;"></i> Tren Kehadiran Harian</div>
            <canvas id="lineChart" height="160"></canvas>
        </div>
    </div>
    @endif

    <!-- TOOLBAR -->
    <div class="pv-toolbar">
        <a href="{{ url('/laporan-pivot/export-excel?'.http_build_query(['tanggal_mulai'=>$mulai,'tanggal_akhir'=>$akhir,'lokasi_id'=>$lokasi_id,'tipe_user'=>$tipe_user])) }}"
           class="btn-tool btn-tool-excel">
            <i class="fa fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ url('/laporan-pivot/export-pdf?'.http_build_query(['tanggal_mulai'=>$mulai,'tanggal_akhir'=>$akhir,'lokasi_id'=>$lokasi_id,'tipe_user'=>$tipe_user])) }}"
           class="btn-tool btn-tool-pdf" target="_blank">
            <i class="fa fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ url('/laporan-pivot') }}" class="btn-tool btn-tool-back">
            <i class="fa fa-arrow-left"></i> Filter Ulang
        </a>
        <a href="{{ url('/laporan-pivot/rekap-bulanan') }}" class="btn-tool" style="background:#fef3c7;color:#92400e;">
            <i class="fa fa-calendar-alt"></i> Rekap Bulanan
        </a>
        <div style="margin-left:auto;">
            <input type="text" id="searchPegawai" class="pv-search" placeholder="🔍 Cari nama pegawai...">
        </div>
        <span class="pv-period">
            <b>{{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }}</b>
            &ndash;
            <b>{{ \Carbon\Carbon::parse($akhir)->format('d/m/Y') }}</b>
        </span>
    </div>

    <!-- TABLE -->
    <div class="pv-table-card">
        <div class="table-scroll">
            <table class="pv-table" id="pivotTable">
                <thead>
                    <tr>
                        <th style="min-width:34px;">No</th>
                        <th class="th-name" style="min-width:180px;">Nama Pegawai</th>
                        <th style="min-width:90px;">NIP / NIDN</th>
                        <th style="min-width:100px;">Jabatan</th>
                        <th style="min-width:80px;">Lokasi</th>
                        @foreach($dates as $d)
                            <th class="{{ \App\Helpers\PivotBuilder::isSunday($d) ? 'sun' : '' }}">
                                {{ date('d', strtotime($d)) }}
                                <br>
                                <span style="font-size:8px;opacity:0.7;">{{ date('D', strtotime($d)) }}</span>
                            </th>
                        @endforeach
                        {{-- Summary columns --}}
                        <th title="Hadir">H</th>
                        <th title="Izin Telat">IT</th>
                        <th title="Izin Pulang Cepat">IP</th>
                        <th title="Cuti">C</th>
                        <th title="Izin Masuk">I</th>
                        <th title="Alfa / Tanpa Keterangan">A</th>
                        <th title="Sakit">S</th>
                        <th title="Libur">L</th>
                        <th title="% Kehadiran">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    @php
                        $u  = $row['user'];
                        $it = 0; $ip = 0; $hadir_asli = 0;
                        foreach($row['codes'] as $j => $code) {
                            if ($code === 'IT') $it++;
                            if ($code === 'IP') $ip++;
                            if ($code === 'H')  $hadir_asli++;
                        }
                        $s = $row['summary'];
                    @endphp
                    <tr class="pegawai-row">
                        <td style="color:#94a3b8;font-weight:600;font-size:11px;">{{ $i + 1 }}</td>
                        <td class="td-name">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#1e3a5f,#1d4ed8);color:white;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex-shrink:0;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span class="pegawai-name">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="td-nip">{{ $u->nip ?: ($u->nidn ?: '-') }}</td>
                        <td class="td-jabatan">{{ $u->Jabatan->nama ?? '-' }}</td>
                        <td class="td-jabatan">{{ $u->Lokasi->nama_lokasi ?? '-' }}</td>

                        @foreach($row['codes'] as $j => $code)
                        @php $isSun = \App\Helpers\PivotBuilder::isSunday($dates[$j]); @endphp
                        <td class="{{ $isSun ? '' : '' }}">
                            @if($code === 'H')
                                <span class="code code-H {{ $isSun ? 'code-sun' : '' }}">H</span>
                            @elseif($code === 'IT')
                                <span class="code code-IT {{ $isSun ? 'code-sun' : '' }}">IT</span>
                            @elseif($code === 'IP')
                                <span class="code code-IP {{ $isSun ? 'code-sun' : '' }}">IP</span>
                            @elseif($code === 'C')
                                <span class="code code-C {{ $isSun ? 'code-sun' : '' }}">C</span>
                            @elseif($code === 'I')
                                <span class="code code-I {{ $isSun ? 'code-sun' : '' }}">I</span>
                            @elseif($code === 'S')
                                <span class="code code-S {{ $isSun ? 'code-sun' : '' }}">S</span>
                            @elseif($code === 'L')
                                <span class="code code-L">L</span>
                            @elseif($code === 'A')
                                <span class="code code-A {{ $isSun ? 'code-sun' : '' }}">A</span>
                            @else
                                <span class="code code-def">-</span>
                            @endif
                        </td>
                        @endforeach

                        {{-- Summary --}}
                        <td class="td-sum td-h">{{ $hadir_asli }}</td>
                        <td class="td-sum td-it">{{ $it }}</td>
                        <td class="td-sum td-ip">{{ $ip }}</td>
                        <td class="td-sum td-c">{{ $s['cuti'] }}</td>
                        <td class="td-sum td-i">{{ $s['izin'] }}</td>
                        <td class="td-sum td-a">{{ $s['alfa'] }}</td>
                        <td class="td-sum td-s">{{ $s['sakit'] }}</td>
                        <td class="td-sum td-l">{{ $s['libur'] }}</td>
                        <td class="td-pct" style="color:{{ $s['persentase'] >= 80 ? '#16a34a' : ($s['persentase'] >= 60 ? '#d97706' : '#dc2626') }};">
                            {{ $s['persentase'] }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                {{-- Total row --}}
                <tfoot>
                    <tr style="background:#0f172a;color:white;">
                        <td colspan="5" style="text-align:left;padding:10px 12px;font-weight:800;font-size:0.8rem;letter-spacing:0.3px;">
                            TOTAL ({{ $peserta }} Pegawai)
                        </td>
                        @foreach($dates as $d)
                            @php
                                $dayH = 0;
                                foreach($rows as $r) {
                                    $idx = array_search($d, $dates);
                                    if ($idx !== false && isset($r['codes'][$idx]) && in_array($r['codes'][$idx], ['H','IT','IP'])) $dayH++;
                                }
                            @endphp
                            <td style="font-weight:700;font-size:11px;color:{{ $dayH == 0 ? '#ef4444' : '#4ade80' }};">
                                {{ $dayH }}
                            </td>
                        @endforeach
                        <td style="font-weight:800;color:#4ade80;font-size:12px;">{{ $totalH }}</td>
                        <td style="font-weight:800;color:#a7f3d0;font-size:12px;">-</td>
                        <td style="font-weight:800;color:#a7f3d0;font-size:12px;">-</td>
                        <td style="font-weight:800;color:#93c5fd;font-size:12px;">{{ $totalC }}</td>
                        <td style="font-weight:800;color:#c4b5fd;font-size:12px;">{{ $totalI }}</td>
                        <td style="font-weight:800;color:#fca5a5;font-size:12px;">{{ $totalA }}</td>
                        <td style="font-weight:800;color:#fcd34d;font-size:12px;">{{ $totalS }}</td>
                        <td style="font-weight:800;color:#9ca3af;font-size:12px;">{{ $totalL }}</td>
                        <td style="font-weight:800;color:#38bdf8;font-size:12px;">{{ $avgPct }}%</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- LEGEND -->
        <div class="legend">
            <div class="legend-item"><span class="legend-dot code-H">H</span> Hadir</div>
            <div class="legend-item"><span class="legend-dot code-IT">IT</span> Izin Telat</div>
            <div class="legend-item"><span class="legend-dot code-IP">IP</span> Izin Pulang Cepat</div>
            <div class="legend-item"><span class="legend-dot code-C">C</span> Cuti</div>
            <div class="legend-item"><span class="legend-dot code-I">I</span> Izin Masuk</div>
            <div class="legend-item"><span class="legend-dot code-S">S</span> Sakit</div>
            <div class="legend-item"><span class="legend-dot code-L">L</span> Libur</div>
            <div class="legend-item"><span class="legend-dot code-A">A</span> Alfa (Tanpa Keterangan)</div>
            <div style="margin-left:auto;font-size:0.72rem;color:#94a3b8;">
                <i class="fa fa-info-circle me-1"></i>
                % = Hadir / (Total - Libur) × 100%
            </div>
        </div>
    </div>
    @endif
</div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($chartData ?? null)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Donut chart
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Alfa', 'Sakit', 'Cuti', 'Izin', 'Libur'],
            datasets: [{
                data: [
                    {!! json_encode($chartData['bar']['Hadir'] ?? 0) !!},
                    {!! json_encode($chartData['bar']['Alfa']  ?? 0) !!},
                    {!! json_encode($chartData['bar']['Sakit'] ?? 0) !!},
                    {!! json_encode($chartData['bar']['Cuti']  ?? 0) !!},
                    {!! json_encode($chartData['bar']['Izin']  ?? 0) !!},
                    {!! json_encode($chartData['bar']['Libur'] ?? 0) !!},
                ],
                backgroundColor: ['#16a34a','#ef4444','#f59e0b','#3b82f6','#8b5cf6','#9ca3af'],
                borderWidth: 0, hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { font:{size:10}, padding:10, usePointStyle:true } },
                tooltip: {
                    backgroundColor: '#0f172a',
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} hari-orang` }
                }
            }
        }
    });

    // Line chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(fn($d) => date('d/m', strtotime($d)), array_keys($chartData['line']))) !!},
            datasets: [{
                label: 'Jumlah Hadir',
                data: {!! json_encode(array_values($chartData['line'])) !!},
                borderColor: '#1d4ed8',
                backgroundColor: 'rgba(29,78,216,0.08)',
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 6,
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
                    callbacks: { label: ctx => ` ${ctx.raw} pegawai hadir` }
                }
            },
            scales: {
                x: { grid:{display:false}, ticks:{font:{size:9},color:'#94a3b8'} },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font:{size:9}, color:'#94a3b8' },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                }
            }
        }
    });

    // Search filter
    document.getElementById('searchPegawai').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.pegawai-row').forEach(function(row) {
            const name = row.querySelector('.pegawai-name')?.textContent?.toLowerCase() || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    });
});
</script>
@endif
@endpush
@endsection
