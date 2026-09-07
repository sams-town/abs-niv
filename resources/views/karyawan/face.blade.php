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
                        <p id="model-loading-info" class="text-muted mt-2">
                            <i class="fas fa-spinner fa-spin me-1"></i> Memuat model AI, harap tunggu...
                        </p>
                        <button id="capture" class="btn btn-primary mt-4" disabled>
                            <i class="fas fa-camera me-2"></i>Capture Image
                        </button>
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
            // Resolusi kecil = deteksi lebih cepat
            let width = 256;
            let height = 192;
            let modelsLoaded = false;

            const startStream = () => {
                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "user", width: { ideal: width }, height: { ideal: height } },
                    audio: false
                }).then((stream) => {
                    video.srcObject = stream;
                }).catch((err) => {
                    Swal.fire('Kamera Tidak Tersedia', 'Pastikan Anda mengizinkan akses kamera. Error: ' + err.message, 'error');
                });
            }

            // Tampilkan loading model
            Swal.fire({
                title: 'Memuat Model AI...',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Pakai tiny landmark model (jauh lebih cepat dari faceLandmark68Net)
            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
            ]).then(() => {
                modelsLoaded = true;
                startStream();
                Swal.close();
                document.getElementById('capture').disabled = false;
                var info = document.getElementById('model-loading-info');
                if (info) info.style.display = 'none';
            }).catch((err) => {
                Swal.fire('Gagal Memuat Model', 'Error: ' + err.message, 'error');
            });

            $(document).ready(function(){

                $("#capture").click(async function(){
                    if (!modelsLoaded) {
                        Swal.fire('Model Belum Siap', 'Tunggu hingga kamera aktif, lalu coba lagi.', 'warning');
                        return;
                    }

                    // Disable tombol agar tidak dobel klik
                    $("#capture").prop('disabled', true);

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mendeteksi wajah, harap tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    var username = $('#username').val();
                    var isSelfRegister = $('#selfRegister').val() === '1';

                    // Ambil frame dari video
                    var canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    var context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, width, height);

                    var img = document.createElement('img');
                    img.src = canvas.toDataURL('image/png');

                    try {
                        // inputSize 224 = balance antara kecepatan dan akurasi deteksi
                        const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.3 });
                        const detections = await faceapi.detectSingleFace(canvas, opts)
                            .withFaceLandmarks(true)   // true = pakai tiny landmark
                            .withFaceDescriptor();

                        if (!detections) {
                            Swal.fire('Wajah Tidak Terdeteksi', 'Pastikan pencahayaan cukup dan wajah terlihat jelas, lalu coba lagi.', 'error');
                            $("#capture").prop('disabled', false);
                            return;
                        }

                        $.ajaxSetup({
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                        });

                        // Kirim foto
                        var photoUrl = isSelfRegister
                            ? "{{ url('/dosen/registrasi-wajah/simpan') }}"
                            : "{{ url('/pegawai/face/ajaxPhoto') }}";

                        $.ajax({
                            type: 'POST', url: photoUrl,
                            data: { image: img.src, path: username },
                            cache: false
                        });

                        // Kirim descriptor wajah
                        // Konversi Float32Array ke plain array agar bisa di-JSON.stringify dengan benar
                        var plainDescriptor = Array.from(detections.descriptor);
                        var postData = {
                            label: username,
                            descriptors: [plainDescriptor]
                        };
                        $.ajax({
                            type: 'POST',
                            url: "{{ url('/pegawai/face/ajaxDescrip') }}",
                            data: { myData: JSON.stringify(postData), user_id: {{ $karyawan->id }} },
                            cache: false,
                            success: function(resp) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: isSelfRegister ? 'Wajah Anda berhasil didaftarkan.' : 'Wajah berhasil didaftarkan.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = isSelfRegister
                                        ? "{{ url('/dashboard') }}"
                                        : "{{ url('/pegawai') }}";
                                });
                            },
                            error: function(xhr) {
                                var msg = 'Terjadi kesalahan saat menyimpan data wajah. Coba lagi.';
                                try {
                                    var resp = JSON.parse(xhr.responseText);
                                    if (resp.error) msg = resp.error;
                                } catch(e) {}
                                Swal.fire('Gagal Simpan', msg, 'error');
                                $("#capture").prop('disabled', false);
                            }
                        });

                    } catch(e) {
                        Swal.fire('Error', 'Terjadi kesalahan: ' + e.message, 'error');
                        $("#capture").prop('disabled', false);
                    }
                });
            });
        </script>
    @endpush
@endsection
