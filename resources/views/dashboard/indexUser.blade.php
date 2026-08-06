@extends('templates.app')
@section('container')
    <div class="card-secton">
        <div class="tf-container">
            <div class="tf-balance-box">
                <div class="balance">
                    <div class="row">
                        <div class="col-4 br-right">
                            <div class="inner-left">
                                <h4>Jam Kerja</h4>
                                <span>{{ $shift_karyawan?->Shift?->jam_masuk ?? '-' }} - {{ $shift_karyawan?->Shift?->jam_keluar ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-4 br-right">
                            <center>
                                <h4>Lokasi</h4>
                                <span>{{ auth()->user()->Lokasi?->nama_lokasi ?? '-' }}</span>
                            </center>
                        </div>
                        <div class="col-4">
                            <div class="inner-right">
                                <h4>Istirahat</h4>
                                <h3>
                                    <span>{{ $shift_karyawan?->Shift?->jam_mulai_istirahat ?? '-' }} - {{ $shift_karyawan?->Shift?->jam_selesai_istirahat ?? '-' }}</span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $tahun_skrg = date('Y');
                    $bulan_skrg = date('m');
                    $jmlh_bulan = cal_days_in_month(CAL_GREGORIAN,$bulan_skrg,$tahun_skrg);
                    $tgl_mulai = date('Y-m-01');
                    $tgl_akhir = date('Y-m-'.$jmlh_bulan);
                    $sisa_reimbursement = auth()->user()->reimbursement->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->where('status', 'Approved')->sum('sisa');
                    $fee_reimbursement = App\Models\ReimbursementsItem::whereHas('reimbursement', function ($query) use ($tgl_mulai, $tgl_akhir) {
                        $query->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->where('status', 'Approved');
                    })->where('user_id', auth()->user()->id)->sum('fee');
                    $total_reimbursement = $sisa_reimbursement + $fee_reimbursement;

                    $total_kasbon = App\Models\Kasbon::where('user_id', auth()->user()->id)->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->where('status', 'Acc')->sum('nominal');
                @endphp
                <div class="wallet-footer">
                    <ul class="d-flex justify-content-around align-items-center mb-0 pl-0 list-unstyled">
                        <li class="wallet-card-item flex-fill text-center">
                            <a href="{{ url('/payroll') }}">
                                <div class="modern-icon-box" style="background: linear-gradient(135deg, #DCFCE7, #BBF7D0); width: 44px !important; height: 44px !important; margin: 0 auto 6px !important;">
                                    <i class="fas fa-file-invoice-dollar" style="color: #16A34A; font-size: 20px;"></i>
                                </div>
                                <p>Payroll</p>
                                <span style="font-size: 11px; color: #6B7280;">Slip & Gaji</span>
                            </a>
                        </li>
                        <li class="wallet-card-item flex-fill text-center">
                            <a href="{{ url('/reimbursement') }}">
                                <div class="modern-icon-box" style="background: linear-gradient(135deg, #D1FAE5, #A7F3D0); width: 44px !important; height: 44px !important; margin: 0 auto 6px !important;">
                                    <i class="fas fa-receipt" style="color: #059669; font-size: 20px;"></i>
                                </div>
                                <p>Reimbursement</p>
                                <span style="font-size: 12px; font-weight: 700; color: #059669;">Rp {{ number_format($total_reimbursement) }}</span>
                            </a>
                        </li>
                        <li class="wallet-card-item flex-fill text-center">
                            <a href="{{ url('/kasbon') }}">
                                <div class="modern-icon-box" style="background: linear-gradient(135deg, #FFE4E6, #FECDD3); width: 44px !important; height: 44px !important; margin: 0 auto 6px !important;">
                                    <i class="fas fa-hand-holding-usd" style="color: #E11D48; font-size: 20px;"></i>
                                </div>
                                <p>Kasbon</p>
                                <span style="font-size: 12px; font-weight: 700; color: #E11D48;">Rp {{ number_format($total_kasbon) }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-2">
        <div class="tf-container">
            <div class="tf-title d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw_7">Layanan</h3>
                <span style="font-size: 13px; color: #6B7280; font-weight: 600;">Menu Cepat</span>
            </div>
            <ul class="box-service">
                <li>
                    <a href="{{ url('/absen') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #E0E7FF, #C7D2FE);">
                            <i class="fas fa-fingerprint" style="color: #4F46E5;"></i>
                        </div>
                        Absensi
                    </a>
                </li>
                <li>
                    <a href="{{ url('/cuti') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #E0F2FE, #BAE6FD);">
                            <i class="fas fa-calendar-minus" style="color: #0284C7;"></i>
                        </div>
                        Cuti & Izin
                    </a>
                </li>
                <li>
                    <a href="{{ url('/dinas-luar') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #FEF3C7, #FDE68A);">
                            <i class="fas fa-briefcase" style="color: #D97706;"></i>
                        </div>
                        Dinas Luar
                    </a>
                </li>
                <li>
                    <a href="{{ url('/lembur') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #EDE9FE, #DDD6FE);">
                            <i class="fas fa-user-clock" style="color: #7C3AED;"></i>
                        </div>
                        Lembur
                    </a>
                </li>
                <li>
                    <a href="{{ url('/request-location') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #FFEDD5, #FED7AA);">
                            <i class="fas fa-map-marked-alt" style="color: #EA580C;"></i>
                        </div>
                        Request Location
                    </a>
                </li>
                <li>
                    <a href="{{ url('/my-profile/edit-password') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #F1F5F9, #E2E8F0);">
                            <i class="fas fa-key" style="color: #475569;"></i>
                        </div>
                        Change Password
                    </a>
                </li>
                <li>
                    <a href="{{ url('/pegawai') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #CCFBF1, #99F6E4);">
                            <i class="fas fa-users" style="color: #0D9488;"></i>
                        </div>
                        Pegawai
                    </a>
                </li>
                @if(auth()->user()->tipe_user === 'dosen')
                <li>
                    <a href="{{ url('/dosen/token-daring') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                            <i class="fas fa-laptop-code" style="color: #F59E0B;"></i>
                        </div>
                        Token Daring
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ url('/menu') }}">
                        <div class="modern-icon-box" style="background: linear-gradient(135deg, #FCE7F3, #F8B4D9);">
                            <i class="fas fa-th" style="color: #C026D3;"></i>
                        </div>
                        Other
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div style="margin-bottom: 80px;"></div>
@endsection
