@extends('templates.app')
@section('container')

    <div class="card-secton transfer-section">
        <div class="tf-container">
            <div class="tf-balance-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="inner-left d-flex justify-content-between align-items-center">
                        <span>Tanggal</span>
                    </div>
                    <span>{{ date('Y-m-d') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <br>
    <style>
        .jam-digital-malasngoding {
          overflow: hidden;
          float: center;
          width: 100px;
          margin: 2px auto;
          border: 0px solid #efefef;
        }

        .kotak {
          float: left;
          width: 30px;
          height: 30px;
          background-color: #189fff;
        }

        .jam-digital-malasngoding p {
          color: #fff;
          font-size: 16px;
          text-align: center;
          margin-top: 3px;
        }
    </style>

    <div class="jam-digital-malasngoding">
        <div class="kotak">
          <p id="jam"></p>
        </div>
        <div class="kotak">
          <p id="menit"></p>
        </div>
        <div class="kotak">
          <p id="detik"></p>
        </div>
    </div>

    <script>
        window.setTimeout("waktu()", 1000);

        function waktu() {
          var waktu = new Date();
          setTimeout("waktu()", 1000);
          document.getElementById("jam").innerHTML = waktu.getHours();
          document.getElementById("menit").innerHTML = waktu.getMinutes();
          document.getElementById("detik").innerHTML = waktu.getSeconds();
        }
    </script>
    <br>

    <div class="d-flex justify-content-center mb-4">
        <form action="{{ url('/my-location') }}" method="get">
            @csrf
            <input type="hidden" name="lat" id="lat2">
            <input type="hidden" name="long" id="long2">
            <input type="hidden" name="userid" value="{{ auth()->user()->id }}">
            <button type="submit" class="btn btn-success">Lihat Lokasi Saya</button>
        </form>
    @php
        $settings = App\Models\settings::first();
    @endphp
    @if($settings && $settings->template_lembur)
    <div class="tf-container mb-3">
        <a href="{{ url('/storage/'.$settings->template_lembur) }}" target="_blank" class="btn btn-sm btn-info text-white w-100 d-block text-center py-2" style="border-radius: 10px; background-color: #17a2b8; border: none;"><i class="fa fa-download mr-1"></i> Download Template Form Lembur</a>
    </div>
    @endif

    <div class="transfer-content">
        {{-- Daftar lembur hari ini yang sudah selesai --}}
        @if(isset($lembur_hari_ini) && $lembur_hari_ini->count() > 0)
        <div class="tf-container mb-3">
            <h6 class="fw-bold mb-2">Lembur Hari Ini</h6>
            @foreach($lembur_hari_ini as $done)
            @php
                $jam = floor($done->total_lembur / 3600);
                $menit = floor(($done->total_lembur % 3600) / 60);
            @endphp
            <div class="d-flex justify-content-between align-items-center p-2 mb-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0">
                <div>
                    <small class="fw-semibold text-success">✓ Selesai</small><br>
                    <small>{{ $done->jam_masuk }} – {{ $done->jam_keluar }}</small>
                </div>
                <span class="badge bg-success">{{ $jam }}j {{ $menit }}m</span>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Form lembur aktif atau form masuk baru --}}
        @if(isset($lembur_berjalan) && $lembur_berjalan && $lembur_berjalan->jam_keluar == null)
            {{-- Ada lembur yang sedang berjalan → tampilkan form pulang --}}
            <form method="post" action="{{ url('/lembur/pulang/'.$lembur_berjalan->id) }}">
                @method('PUT')
                @csrf
                <div class="tf-container">
                    <center>
                        <h2>Pulang Lembur</h2>
                        <small class="text-muted">Masuk: {{ $lembur_berjalan->jam_masuk }}</small>
                        <div class="webcam mt-2" id="results"></div>
                    </center>
                    <input type="hidden" name="jam_keluar" value="{{ date('Y-m-d H:i') }}">
                    <input type="hidden" name="lat_keluar" id="lat">
                    <input type="hidden" name="long_keluar" id="long">
                    <input type="hidden" name="jarak_keluar">
                    <input type="hidden" name="foto_jam_keluar" class="image-tag">
                    <input type="hidden" name="total_lembur">
                    <button type="submit" class="tf-btn accent large mt-3" onClick="take_snapshot()">Save</button>
                </div>
            </form>
        @else
            {{-- Tidak ada lembur berjalan → tampilkan form masuk lembur baru --}}
            <form method="post" action="{{ url('/lembur/masuk') }}">
                @csrf
                <div class="tf-container">
                    <center>
                        <h2>Masuk Lembur
                            @if(isset($lembur_hari_ini) && $lembur_hari_ini->count() > 0)
                                <span class="badge bg-primary" style="font-size:12px">Sesi {{ $lembur_hari_ini->count() + 1 }}</span>
                            @endif
                        </h2>
                        <div class="webcam mt-2" id="results"></div>
                    </center>
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="jam_masuk" value="{{ date('Y-m-d H:i') }}">
                    <input type="hidden" name="lat_masuk" id="lat">
                    <input type="hidden" name="long_masuk" id="long">
                    <input type="hidden" name="jarak_masuk">
                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="foto_jam_masuk" class="image-tag">
                    <button type="submit" class="tf-btn accent large mt-3" onClick="take_snapshot()">Save</button>
                </div>
            </form>
        @endif
    </div>

    @push('script')
        <script type="text/javascript" src="{{ url('webcamjs/webcam.min.js') }}"></script>
        <script language="JavaScript">
        Webcam.set({ width: 310, height: 420, image_format: 'jpeg', jpeg_quality: 50 });
        Webcam.attach('.webcam');
        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
            });
        }
        </script>
        <script>
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else {
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }
            function showPosition(position) {
                $('#lat').val(position.coords.latitude);
                $('#lat2').val(position.coords.latitude);
                $('#long').val(position.coords.longitude);
                $('#long2').val(position.coords.longitude);
            }

            setInterval(getLocation, 1000);
        </script>
    @endpush
    
@endsection