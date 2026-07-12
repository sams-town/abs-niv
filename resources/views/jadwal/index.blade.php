@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12">
        <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: none; border-bottom: 1px solid #f1f5f9; padding: 24px;">
                <div>
                    <h4 style="font-weight: 800; color: #0f172a; margin: 0;">{{ $title }}</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Kelola jadwal kelas daring & hitung honorarium mengajar otomatis</p>
                </div>
                @if(auth()->user()->hasRole('admin') || auth()->user()->is_admin == 'admin')
                    <a href="{{ url('/jadwal/create') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 600; padding: 10px 20px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                        <i class="fa fa-plus me-2"></i> Buat Jadwal
                    </a>
                @endif
            </div>
            <div class="card-body" style="padding: 24px;">
                @if(session('success'))
                    <div class="alert alert-success" style="border-radius: 12px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning" style="border-radius: 12px; margin-bottom: 20px;">
                        {{ session('warning') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 20px;">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover" style="vertical-align: middle;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Kelas & Mata Kuliah</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Dosen Pengampu</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Waktu Jadwal</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Status Sesi</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Meeting Details</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px; width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $jadwal)
                                @php
                                    $sesi = $jadwal->sesiDarings->first(); // Get the latest session
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 16px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 14px;">{{ $jadwal->mata_kuliah }}</div>
                                        <span class="badge bg-light text-dark mt-1" style="font-weight: 600; border: 1px solid #cbd5e1;">{{ $jadwal->nama_kelas }}</span>
                                    </td>
                                    <td style="padding: 16px; font-weight: 600; color: #334155;">
                                        {{ $jadwal->dosen->name }}
                                    </td>
                                    <td style="padding: 16px; font-size: 13px; color: #64748b;">
                                        <div><i class="fa fa-calendar-alt text-muted me-1"></i> {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('d M Y') }}</div>
                                        <div class="mt-1"><i class="fa fa-clock text-muted me-1"></i> {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</div>
                                    </td>
                                    <td style="padding: 16px;">
                                        @if(!$sesi)
                                            <span class="badge bg-secondary" style="font-weight: 700; padding: 6px 12px; border-radius: 6px;">Belum Dijadwalkan</span>
                                        @elseif($sesi->status_sesi === 'scheduled')
                                            <span class="badge bg-warning text-dark" style="font-weight: 700; padding: 6px 12px; border-radius: 6px;">Terjadwal</span>
                                        @elseif($sesi->status_sesi === 'live')
                                            <span class="badge bg-danger animate__animated animate__flash animate__infinite" style="font-weight: 700; padding: 6px 12px; border-radius: 6px; animation: flash 2s infinite;"><i class="fa fa-circle text-white me-1" style="font-size: 9px;"></i> LIVE</span>
                                        @elseif($sesi->status_sesi === 'ended')
                                            <span class="badge bg-success" style="font-weight: 700; padding: 6px 12px; border-radius: 6px;">Selesai</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px; font-size: 12px;">
                                        @if($sesi)
                                            <div>
                                                <a href="{{ $sesi->meeting_url }}" target="_blank" class="text-primary font-weight-bold" style="text-decoration: underline;">
                                                    <i class="fa fa-video me-1"></i> Gabung Meeting
                                                </a>
                                            </div>
                                            <div class="text-muted mt-1">ID: {{ $sesi->meeting_id }}</div>
                                            <div class="text-muted">Pass: {{ $sesi->passcode }}</div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px;">
                                        @if(!$sesi)
                                            @if(auth()->user()->id == $jadwal->dosen_id || auth()->user()->hasRole('admin') || auth()->user()->is_admin == 'admin')
                                                <a href="{{ url('/sesi-daring/create/'.$jadwal->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; font-weight: 700; padding: 6px 12px;">
                                                    <i class="fa fa-plus me-1"></i> Buat Sesi
                                                </a>
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">Menunggu Dosen</span>
                                            @endif
                                        @elseif($sesi->status_sesi === 'scheduled')
                                            @if(auth()->user()->id == $jadwal->dosen_id || auth()->user()->hasRole('admin') || auth()->user()->is_admin == 'admin')
                                                <form action="{{ url('/sesi-daring/'.$sesi->id.'/start') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" style="border-radius: 8px; font-weight: 700; padding: 6px 12px; width: 100%;">
                                                        <i class="fa fa-play me-1"></i> Mulai Live
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">Menunggu Mulai</span>
                                            @endif
                                        @elseif($sesi->status_sesi === 'live')
                                            @if(auth()->user()->id == $jadwal->dosen_id || auth()->user()->hasRole('admin') || auth()->user()->is_admin == 'admin')
                                                <form action="{{ url('/sesi-daring/'.$sesi->id.'/end') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" style="border-radius: 8px; font-weight: 700; padding: 6px 12px; width: 100%;">
                                                        <i class="fa fa-stop me-1"></i> Akhiri Sesi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted" style="font-size: 12px;">Kelas Berjalan</span>
                                            @endif
                                        @elseif($sesi->status_sesi === 'ended')
                                            <span class="text-success" style="font-size: 13px; font-weight: 700;"><i class="fa fa-check-circle me-1"></i> Terhitung</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center" style="padding: 32px; color: #94a3b8; font-weight: 500;">
                                        <i class="fa fa-info-circle me-2"></i> Belum ada jadwal mengajar yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes flash {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
</style>
@endsection
