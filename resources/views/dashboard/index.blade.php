@extends('templates.dashboard')
@section('isi')
  <!-- Welcome Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card" style="background: linear-gradient(135deg, #0056b3 0%, #009688 100%); color: white;">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-1" style="font-weight: 700;">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
              <p class="mb-0" style="opacity: 0.9;">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="text-right">
              <div class="display-4" id="current-time"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Monitoring Kontrak Kerja (Deteksi Dini 3 Bulan) -->
  @if(isset($kontrak_expiring))
  <div class="row mb-4">
    <div class="col-12">
      <h4 class="mb-3" style="font-weight: 600; color: #1e293b;"><i data-feather="shield" class="mr-2 text-warning"></i> Monitoring Masa Aktif Kontrak Kerja (Deteksi Dini 3 Bulan)</h4>
      <div class="card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid {{ count($kontrak_expiring) > 0 ? '#f59e0b' : '#10b981' }};">
        <div class="card-body p-4">
          @if(count($kontrak_expiring) > 0)
            <div class="alert alert-warning d-flex align-items-center mb-3" style="border-radius: 8px; background-color: #fffbeb; border: 1px solid #fcd34d; color: #92400e;">
              <i data-feather="alert-triangle" class="mr-2" style="width: 24px; height: 24px; color: #d97706;"></i>
              <div>
                <strong>Peringatan Sistem:</strong> Ditemukan <strong>{{ count($kontrak_expiring) }} Pegawai/Dosen</strong> yang masa kontrak kerjanya akan habis dalam waktu kurang dari 3 bulan (atau sudah habis). <br>
                <small>Suara alarm notifikasi tidak akan berhenti (berulang otomatis) hingga Anda membuka dan memeriksa notifikasi terkait!</small>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle mb-0">
                <thead style="background-color: #f8fafc;">
                  <tr>
                    <th>No</th>
                    <th>Nama Pegawai / Dosen</th>
                    <th>Tipe User</th>
                    <th>Jabatan / Divisi</th>
                    <th>Tanggal Habis Kontrak</th>
                    <th>Status & Sisa Waktu</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($kontrak_expiring as $index => $item)
                    @php
                      $tglHabis = \Carbon\Carbon::parse($item['tanggal_selesai']);
                      $diffDays = \Carbon\Carbon::now()->startOfDay()->diffInDays($tglHabis->startOfDay(), false);
                    @endphp
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td class="font-weight-bold" style="color: #1e293b;">{{ $item['name'] }}</td>
                      <td><span class="badge badge-info" style="font-size: 11px;">{{ ucfirst($item['tipe_user']) }}</span></td>
                      <td>{{ $item['jabatan'] ?? '-' }}</td>
                      <td><span class="text-danger font-weight-bold">{{ $tglHabis->translatedFormat('d F Y') }}</span></td>
                      <td>
                        @if($diffDays < 0)
                          <span class="badge badge-danger p-2" style="font-size: 12px;"><i data-feather="x-circle" style="width:14px;height:14px;margin-right:4px;"></i> Habis {{ abs($diffDays) }} hari lalu</span>
                        @elseif($diffDays == 0)
                          <span class="badge badge-danger p-2" style="font-size: 12px;"><i data-feather="alert-octagon" style="width:14px;height:14px;margin-right:4px;"></i> Habis Hari Ini!</span>
                        @else
                          <span class="badge badge-warning text-dark p-2" style="font-size: 12px; background-color: #fef08a;"><i data-feather="clock" style="width:14px;height:14px;margin-right:4px;"></i> {{ $diffDays }} Hari Lagi</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ url('/kontrak') }}" class="btn btn-sm btn-primary" style="border-radius: 6px;">
                          <i data-feather="external-link" style="width: 14px; height: 14px;"></i> Kelola Kontrak
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4">
              <div class="mb-2" style="color: #10b981;"><i data-feather="check-circle" style="width: 48px; height: 48px;"></i></div>
              <h5 style="font-weight: 600; color: #1e293b;">Semua Kontrak Kerja Pegawai Aman</h5>
              <p class="text-muted mb-0">Saat ini tidak ada pegawai atau dosen yang masa kontrak atau masa aktifnya habis dalam waktu 3 bulan ke depan.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Kehadiran Section -->
  <div class="row mb-4">
    <div class="col-12">
      <h4 class="mb-3" style="font-weight: 600; color: #1e293b;"><i data-feather="calendar" class="mr-2"></i> Statistik Kehadiran</h4>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="animated-bg"><i></i><i></i><i></i></div>
        <div class="card-body">
          <div class="icon"><i data-feather="users"></i></div>
          <p>Total Pegawai</p>
          <h3>{{ $jumlah_user }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="animated-bg"><i></i><i></i><i></i></div>
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);"><i data-feather="check-circle"></i></div>
          <p>Masuk</p>
          <h3>{{ $jumlah_masuk + $jumlah_izin_telat + $jumlah_izin_pulang_cepat  }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="animated-bg"><i></i><i></i><i></i></div>
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);"><i data-feather="x-circle"></i></div>
          <p>Alfa</p>
          <h3>{{ ($jumlah_user - ($jumlah_masuk + $jumlah_izin_telat + $jumlah_izin_pulang_cepat + $jumlah_libur + $jumlah_cuti + $jumlah_izin_masuk + $jumlah_sakit)) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="animated-bg"><i></i><i></i><i></i></div>
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);"><i data-feather="clock"></i></div>
          <p>Lembur</p>
          <h3>{{ $jumlah_karyawan_lembur }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Izin & Cuti Section -->
  <div class="row mb-4">
    <div class="col-12">
      <h4 class="mb-3" style="font-weight: 600; color: #1e293b;"><i data-feather="file-text" class="mr-2"></i> Izin & Cuti</h4>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);"><i data-feather="clipboard"></i></div>
          <p>Libur</p>
          <h3>{{ $jumlah_libur }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);"><i data-feather="credit-card"></i></div>
          <p>Cuti</p>
          <h3>{{ $jumlah_cuti }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);"><i data-feather="heart"></i></div>
          <p>Sakit</p>
          <h3>{{ $jumlah_sakit }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);"><i data-feather="umbrella"></i></div>
          <p>Izin</p>
          <h3>{{ $jumlah_izin_masuk }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%);"><i data-feather="droplet"></i></div>
          <p>Izin Telat</p>
          <h3>{{ $jumlah_izin_telat }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #14b8a6 0%, #2dd4bf 100%);"><i data-feather="navigation"></i></div>
          <p>Izin Pulang Cepat</p>
          <h3>{{ $jumlah_izin_pulang_cepat }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Keuangan Section -->
  <div class="row mb-4">
    <div class="col-12">
      <h4 class="mb-3" style="font-weight: 600; color: #1e293b;"><i data-feather="dollar-sign" class="mr-2"></i> Keuangan {{ date('F Y') }}</h4>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);"><i data-feather="dollar-sign"></i></div>
          <p>Payroll</p>
          <h3>Rp {{ number_format($payroll) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);"><i data-feather="git-commit"></i></div>
          <p>Kasbon</p>
          <h3>Rp {{ number_format($kasbon) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3">
      <div class="card investment-sec">
        <div class="card-body">
          <div class="icon" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);"><i data-feather="pocket"></i></div>
          <p>Reimbursement</p>
          <h3>Rp {{ number_format($reimbursement) }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Kalender Section -->
  <div class="row">
    <div class="col-12">
      <h4 class="mb-3" style="font-weight: 600; color: #1e293b;"><i data-feather="calendar" class="mr-2"></i> Kalender</h4>
    </div>
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <div class="row" id="wrap">
            <div class="col-xxl-12 col-xl-12 box-col-70">
              <div id="external-events mb-4">
                <div id="external-events-list">
                </div>
              </div>
              <div class="calendar-default" id="calendar-container">
                <div id="calendar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @push('script')
      <script>
        // Live clock
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('current-time').textContent = timeString;
        }
        updateTime();
        setInterval(updateTime, 1000);

        document.addEventListener("DOMContentLoaded", function () {
            var date = new Date();
            var d    = date.getDate();
            m    = date.getMonth();
            y    = date.getFullYear();

            var containerEl = document.getElementById("external-events-list");
            new FullCalendar.Draggable(containerEl, {
                itemSelector: ".fc-event",
                eventData: function (eventEl) {
                    return {
                        title: eventEl.innerText.trim(),
                    };
                },
            });

            var calendarEl = document.getElementById("calendar");
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
                },
                initialView: "dayGridMonth",
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                selectable: true,
                nowIndicator: true,
                // dayMaxEvents: true, // allow "more" link when too many events
                events: [
                  @php
                    $tahun_skrg = date('Y');
                    $bulan_skrg = date('m');
                    $jmlh_bulan = cal_days_in_month(CAL_GREGORIAN,$bulan_skrg,$tahun_skrg);
                    $tgl_mulai = date('1945-01-01');
                    $tgl_akhir = date('Y-m-'.$jmlh_bulan);
                    $data_user = App\Models\User::pegawaiDanDosen()->select('name', 'tgl_lahir')->whereBetween('tgl_lahir', [$tgl_mulai, $tgl_akhir])->get();
                    $data_sakit = App\Models\MappingShift::where('status_absen', 'Sakit')->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->get();
                    $data_cuti = App\Models\MappingShift::where('status_absen', 'Cuti')->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->get();
                    $data_izin_masuk = App\Models\MappingShift::where('status_absen', 'Izin Masuk')->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->get();
                    $data_izin_telat = App\Models\MappingShift::where('status_absen', 'Izin Telat')->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->get();
                    $data_izin_pulang_cepat = App\Models\MappingShift::where('status_absen', 'Izin Pulang Cepat')->whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->get();
                  @endphp
                  @foreach($data_user as $du)
                    @php
                      $pecah = explode("-", $du->tgl_lahir)
                    @endphp
                    {
                      title          : 'Ulang Tahun: {{ $du->name }}',
                      start          : new Date(y, {{ $pecah[1]-1 }}, {{ $pecah[2] }}),
                      allDay         : true
                    },
                  @endforeach
                  @foreach($data_sakit as $ds)
                    @php
                      $pecah2 = explode("-", $ds->tanggal)
                    @endphp
                    {
                      title          : 'Sakit: {{ $ds->User->name }}',
                      start          : new Date({{ $pecah2[0] }}, {{ $pecah2[1]-1 }}, {{ $pecah2[2] }}),
                      allDay         : true
                    },
                  @endforeach
                  @foreach($data_cuti as $dc)
                    @php
                      $pecah3 = explode("-", $dc->tanggal)
                    @endphp
                    {
                      title          : 'Cuti: {{ $dc->User->name }}',
                      start          : new Date({{ $pecah3[0] }}, {{ $pecah3[1]-1 }}, {{ $pecah3[2] }}),
                      allDay         : true
                    },
                  @endforeach
                  @foreach($data_izin_masuk as $dim)
                    @php
                      $pecah4 = explode("-", $dim->tanggal)
                    @endphp
                    {
                      title          : 'Izin Masuk: {{ $dim->User->name }}',
                      start          : new Date({{ $pecah4[0] }}, {{ $pecah4[1]-1 }}, {{ $pecah4[2] }}),
                      allDay         : true
                    },
                  @endforeach
                  @foreach($data_izin_telat as $dit)
                    @php
                      $pecah5 = explode("-", $dit->tanggal)
                    @endphp
                    {
                      title          : 'Izin Telat: {{ $dit->User->name }}',
                      start          : new Date({{ $pecah5[0] }}, {{ $pecah5[1]-1 }}, {{ $pecah5[2] }}),
                      allDay         : true
                    },
                  @endforeach
                  @foreach($data_izin_pulang_cepat as $dipc)
                    @php
                      $pecah6 = explode("-", $dipc->tanggal)
                    @endphp
                    {
                      title          : 'Izin Pulang Cepat: {{ $dipc->User->name }}',
                      start          : new Date({{ $pecah6[0] }}, {{ $pecah6[1]-1 }}, {{ $pecah6[2] }}),
                      allDay         : true
                    },
                  @endforeach
                ],
                editable: true,
                droppable: true,
                drop: function (arg) {
                    if (document.getElementById("drop-remove").checked) {
                        arg.draggedEl.parentNode.removeChild(arg.draggedEl);
                    }
                },
            });
            calendar.render();
        });
      </script>
  @endpush
@endsection
