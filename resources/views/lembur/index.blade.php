@extends('templates.dashboard')
@section('isi')
    <div class="container-fluid">

        <center>
            <p class="p mb-2 text-gray-800">Tanggal : {{ date('Y-m-d') }}</p>
        </center>

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
        
        <div class="d-flex justify-content-center">
            <form action="{{ url('/my-location') }}" method="get">
                @csrf
                <input type="hidden" name="lat" id="lat2">
                <input type="hidden" name="long" id="long2">
                <input type="hidden" name="userid" value="{{ auth()->user()->id }}">
                <button type="submit" class="btn btn-success">Lihat Lokasi Saya</button>
            </form>
        </div>

        <br>

        @if(isset($lembur_hari_ini) && $lembur_hari_ini->count() > 0)
        <div class="col-lg-12 mb-3">
            <div class="card">
                <div class="card-header py-2"><strong>Lembur Hari Ini</strong></div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead><tr><th>#</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Durasi</th><th>Status</th></tr></thead>
                            <tbody>
                            @foreach($lembur_hari_ini as $i => $done)
                            @php
                                $j = floor($done->total_lembur / 3600);
                                $m = floor(($done->total_lembur % 3600) / 60);
                            @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $done->jam_masuk }}</td>
                                <td>{{ $done->jam_keluar }}</td>
                                <td><span class="badge badge-success">{{ $j }}j {{ $m }}m</span></td>
                                <td><span class="badge badge-info">{{ $done->status }}</span></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($lembur_berjalan) && $lembur_berjalan && $lembur_berjalan->jam_keluar == null)
            <div class="col-lg-12">
                <div class="card">
                    <form method="post" action="{{ url('/lembur/pulang/'.$lembur_berjalan->id) }}" class="p-4">
                        @method('put')
                        @csrf
                        <div class="form-row">
                            <div class="col"></div>
                            <div class="col">
                                <center>
                                    <h2>Pulang Lembur</h2>
                                    <small class="text-muted d-block mb-2">Masuk: {{ $lembur_berjalan->jam_masuk }}</small>
                                    <div class="webcam" id="results"></div>
                                </center>
                            </div>
                            <div class="col">
                                <input type="hidden" name="jam_keluar" value="{{ date('Y-m-d H:i') }}">
                                <input type="hidden" name="lat_keluar" id="lat">
                                <input type="hidden" name="long_keluar" id="long">
                                <input type="hidden" name="jarak_keluar">
                                <input type="hidden" name="foto_jam_keluar" class="image-tag">
                                <input type="hidden" name="total_lembur">
                            </div>
                        </div>
                        <center>
                            <button type="submit" class="btn btn-primary" value="Ambil Foto" onClick="take_snapshot()">Pulang</button>
                        </center>
                    </form>
                </div>
            </div>
        @else
            <div class="col-lg-12">
                <div class="card">
                    <form method="post" action="{{ url('/lembur/masuk') }}" class="p-4">
                        @csrf
                        <div class="form-row">
                            <div class="col"></div>
                            <div class="col">
                                <center>
                                    <h2>Masuk Lembur
                                        @if(isset($lembur_hari_ini) && $lembur_hari_ini->count() > 0)
                                            <span class="badge badge-primary">Sesi {{ $lembur_hari_ini->count() + 1 }}</span>
                                        @endif
                                    </h2>
                                    <div class="webcam" id="results"></div>
                                </center>
                            </div>
                            <div class="col">
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                                <input type="hidden" name="jam_masuk" value="{{ date('Y-m-d H:i') }}">
                                <input type="hidden" name="lat_masuk" id="lat">
                                <input type="hidden" name="long_masuk" id="long">
                                <input type="hidden" name="jarak_masuk">
                                <input type="hidden" name="status" value="Pending">
                                <input type="hidden" name="foto_jam_masuk" class="image-tag">
                            </div>
                        </div>
                        <center>
                            <button type="submit" class="btn btn-primary" value="Ambil Foto" onClick="take_snapshot()">Masuk</button>
                        </center>
                    </form>
                </div>
            </div>
        @endif
    </div>
    <script type="text/javascript" src="{{ url('webcamjs/webcam.min.js') }}"></script>
    <script language="JavaScript">
        Webcam.set({ width: 240, height: 320, image_format: 'jpeg', jpeg_quality: 50 });
        Webcam.attach('.webcam');
        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
            });
        }
    </script>
@endsection

