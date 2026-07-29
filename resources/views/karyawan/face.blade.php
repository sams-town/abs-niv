@extends('templates.dashboard')
@section('isi')
    @push('style')
        <style>
            canvas {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="p-4">
                    @if(isset($self_register) && $self_register)
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="fas fa-camera fa-lg me-2"></i>
                        <div>
                            <strong>Registrasi Wajah Wajib</strong> — Sebagai dosen, Anda perlu mendaftarkan wajah sekali sebelum bisa menggunakan sistem absensi.
                            Pastikan wajah Anda terlihat jelas di kamera, lalu klik <strong>Capture Image</strong>.
                        </div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="name" class="float-left">Nama</label>
                        <input type="text" class="form-control" value="{{ $karyawan->name }}" disabled id="name">
                    </div>
                    <input type="hidden" name="username" id="username" value="{{ $karyawan->username }}">
                    <input type="hidden" id="selfRegister" value="{{ isset($self_register) && $self_register ? '1' : '0' }}">
                    <video id="video" autoplay playsinline class="col-lg-12 col-md-12 col-sm-12 mx-auto"></video>
                    <br>
                    <center>
                        <button id="capture" class="btn btn-primary mt-4"><i class="fas fa-camera me-2"></i>Capture Image</button>
                    </center>
                </div>
            </div>
        </div>
    </div>
    @push('script')
        <script src="{{ url('/face/dist/face-api.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let video = document.getElementById("video");
            let width = 640;
            let height = 480;

            const startStream = () => {
                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "user", width, height },
                    audio: false
                }).then((stream) => {
                    video.srcObject = stream;
                });
            }

            Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceLandmark68Net.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
            ]).then(startStream);

            $(document).ready(function(){
                const descriptions = [];

                $("#capture").click(async function(){
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Detecting face, please wait.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                        var username = $('#username').val();
                        const label = username;
                        var isSelfRegister = $('#selfRegister').val() === '1';

                        var canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        var context = canvas.getContext('2d');
                        context.drawImage(video, 0, 0, width, height);

                        var img = document.createElement('img');
                        img.src = canvas.toDataURL('image/png');

                        const detections = await faceapi.detectSingleFace(canvas, new faceapi.SsdMobilenetv1Options()).withFaceLandmarks().withFaceDescriptor();

                        if(detections) {
                            descriptions.push(detections.descriptor);
                            var descrip = descriptions;

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            // Gunakan endpoint yang sesuai: self-register untuk dosen, atau admin untuk admin
                            var photoUrl = isSelfRegister
                                ? "{{ url('/dosen/registrasi-wajah/simpan') }}"
                                : "{{ url('/pegawai/face/ajaxPhoto') }}";

                            $.ajax({
                                type : 'POST',
                                url : photoUrl,
                                data :  {image: img.src, path: username},
                                cache : false,
                                success: function(msg){
                                    console.log(msg);
                                },
                                error: function(data){
                                    console.log('error:', data);
                                }
                            });

                            var postData = new faceapi.LabeledFaceDescriptors(label, descrip);
                            $.ajax({
                                type : 'POST',
                                url : "{{ url('/pegawai/face/ajaxDescrip') }}",
                                data :  { myData: JSON.stringify(postData), user_id:{{ $karyawan->id }} },
                                datatype : 'json',
                                cache : false,
                                success: function(msg){
                                    Swal.fire({
                                        title: 'Berhasil Daftar Wajah!',
                                        text: isSelfRegister ? 'Wajah Anda berhasil didaftarkan. Selamat menggunakan sistem!' : 'Wajah berhasil didaftarkan.',
                                        icon: 'success'
                                    });
                                    setTimeout(function() {
                                        window.location.href = isSelfRegister
                                            ? "{{ url('/dashboard') }}"
                                            : "{{ url('/pegawai') }}";
                                    }, 2000);
                                },
                                error: function(data){
                                    console.log('error:', data);
                                }
                            });
                        } else {
                            Swal.fire('Gagal Deteksi Wajah!', 'Wajah tidak terdeteksi. Pastikan pencahayaan baik dan wajah terlihat jelas, lalu coba lagi.', 'error');
                        }
                    });
            });
        </script>
    @endpush
@endsection
