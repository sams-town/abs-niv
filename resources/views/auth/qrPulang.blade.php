@extends('templates.login')
@section('container')
    <h1>{{ $title }}</h1>
    <div id="reader" width="600px"></div>
    <a href="{{ url('/') }}" class="tf-btn accent large"><i class="fas fa-arrow-left mr-2"></i>Back</a>
    @push('script')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function onScanSuccess(decodedText, decodedResult) {
                    let username = decodedText;
                    html5QrcodeScanner.clear().then(_ => {
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            url: "{{ url('/qr-pulang/store') }}",
                            type: 'POST',
                            data: {
                                _methode : "POST",
                                _token: CSRF_TOKEN,
                                username : username
                            },
                            success: function (response) {
                                console.log(response);
                                if(response == 'pulang'){
                                    Swal.fire('Berhasil Pulang!', '', 'success');
                                    setInterval(function() {
                                        location.reload();
                                    }, 2000);
                                } else if (response == 'selesai'){
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Anda Sudah Selesai Absen Pulang',
                                    });
                                    setInterval(function() {
                                        location.reload();
                                    }, 2000);
                                } else if (response == 'noMs'){
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Hubungi Admin Untuk Input Shift Anda',
                                    });
                                    setInterval(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data QR atau Pegawai tidak Ditemukan Di Sistem',
                                    });
                                    setInterval(function() {
                                        location.reload();
                                    }, 2000);
                                }
                            }
                        });
                    }).catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'ERROR!',
                        });
                        setInterval(function() {
                            location.reload();
                        }, 2000);
                    });
            }

            function onScanFailure(error) {
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        </script>
    @endpush
@endsection
