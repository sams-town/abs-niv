@extends('templates.dashboard')
@section('isi')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .nav-pills-custom .btn-toggle {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 16px;
        transition: all 0.3s;
    }
    .nav-pills-custom .btn-toggle.active {
        background-color: #4361ee;
        color: white;
        border-color: #4361ee;
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
    }
    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: none;
    }
    #map-preview {
        height: 520px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
</style>

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ url('/lokasi-kantor') }}" class="btn btn-light btn-sm me-3" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $title }}</h4>
                        <small class="text-muted">Perbaiki titik koordinat pada peta atau gunakan otomatis dari GPS perangkat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-4 nav-pills-custom gap-2">
                <button type="button" class="btn btn-toggle active w-50 me-1" id="btn-manual">
                    <i class="fa fa-hand-pointer me-1"></i> MANUAL / PETA
                </button>
                <button type="button" class="btn btn-light btn-toggle w-50 ms-1" id="btn-otomatis">
                    <i class="fa fa-crosshairs me-1"></i> OTOMATIS (GPS)
                </button>
            </div>

            <form method="post" action="{{ url('/lokasi-kantor/update/'.$lokasi->id) }}" id="form-lokasi">
                @method('put')
                @csrf
                <div class="form-group mb-3">
                    <label for="nama_lokasi" class="fw-bold text-muted mb-1">NAMA LOKASI</label>
                    <input type="text" class="form-control @error('nama_lokasi') is-invalid @enderror" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}" required autofocus>
                    @error('nama_lokasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lat_kantor" class="fw-bold text-muted mb-1">LAT (LATITUDE)</label>
                            <input type="text" class="form-control @error('lat_kantor') is-invalid @enderror" id="lat_kantor" name="lat_kantor" value="{{ old('lat_kantor', $lokasi->lat_kantor) }}" required>
                            @error('lat_kantor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="long_kantor" class="fw-bold text-muted mb-1">LONG (LONGITUDE)</label>
                            <input type="text" class="form-control @error('long_kantor') is-invalid @enderror" id="long_kantor" name="long_kantor" value="{{ old('long_kantor', $lokasi->long_kantor) }}" required>
                            @error('long_kantor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="radius" class="fw-bold text-muted mb-1">RADIUS (METER)</label>
                    <input type="number" class="form-control @error('radius') is-invalid @enderror" id="radius" name="radius" value="{{ old('radius', $lokasi->radius) }}" required min="5">
                    @error('radius')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="keterangan" class="fw-bold text-muted mb-1">KETERANGAN / JENIS</label>
                    <select name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror selectpicker" data-live-search="true" required>
                        <option value="">-- Pilih Keterangan --</option>
                        <option value="Office" {{ 'Office' == old('keterangan', $lokasi->keterangan) ? 'selected="selected"' : '' }}>Office</option>
                        <option value="Patroli" {{ 'Patroli' == old('keterangan', $lokasi->keterangan) ? 'selected="selected"' : '' }}>Patroli</option>
                    </select>
                    @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius: 8px; font-size: 16px;">
                    <i class="fa fa-save me-2"></i> Simpan Perubahan Lokasi
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-3">
            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center" style="border-radius: 8px; font-size: 13px;">
                <i class="fa fa-info-circle me-2 fs-5"></i>
                <span><b>Tips:</b> Klik sembarang titik pada peta atau geser penanda biru untuk mengubah koordinat. Lingkungan berwarna biru menunjukkan radius absensi saat ini.</span>
            </div>
            <div id="map-preview"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var defaultLat = parseFloat(document.getElementById('lat_kantor').value) || 1.108750;
        var defaultLng = parseFloat(document.getElementById('long_kantor').value) || 104.082806;
        var defaultRadius = parseInt(document.getElementById('radius').value) || 100;

        var map = L.map('map-preview').setView([defaultLat, defaultLng], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
        var circle = L.circle([defaultLat, defaultLng], {
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.2,
            radius: defaultRadius
        }).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('lat_kantor').value = lat.toFixed(8);
            document.getElementById('long_kantor').value = lng.toFixed(8);
        }

        marker.on('dragend', function (e) {
            var position = marker.getLatLng();
            circle.setLatLng(position);
            updateInputs(position.lat, position.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            circle.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });

        document.getElementById('radius').addEventListener('input', function() {
            var r = parseInt(this.value) || 0;
            circle.setRadius(r);
        });

        document.getElementById('lat_kantor').addEventListener('input', function() {
            var lat = parseFloat(this.value);
            var lng = parseFloat(document.getElementById('long_kantor').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                var newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                circle.setLatLng(newLatLng);
                map.setView(newLatLng);
            }
        });

        document.getElementById('long_kantor').addEventListener('input', function() {
            var lat = parseFloat(document.getElementById('lat_kantor').value);
            var lng = parseFloat(this.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                var newLatLng = new L.LatLng(lat, lng);
                marker.setLatLng(newLatLng);
                circle.setLatLng(newLatLng);
                map.setView(newLatLng);
            }
        });

        var btnManual = document.getElementById('btn-manual');
        var btnOtomatis = document.getElementById('btn-otomatis');

        btnManual.addEventListener('click', function() {
            btnOtomatis.classList.remove('active');
            btnOtomatis.classList.add('btn-light');
            btnManual.classList.remove('btn-light');
            btnManual.classList.add('active');
        });

        btnOtomatis.addEventListener('click', function() {
            if (navigator.geolocation) {
                var oldHtml = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> MENCARI LOKASI...';
                var btn = this;
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    var newLatLng = new L.LatLng(lat, lng);
                    marker.setLatLng(newLatLng);
                    circle.setLatLng(newLatLng);
                    map.setView(newLatLng, 18);
                    updateInputs(lat, lng);

                    btn.innerHTML = '<i class="fa fa-crosshairs me-1"></i> OTOMATIS (GPS)';
                    btnManual.classList.remove('active');
                    btnManual.classList.add('btn-light');
                    btn.classList.remove('btn-light');
                    btn.classList.add('active');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi Ditemukan!',
                            text: 'Koordinat telah diubah ke posisi perangkat Anda saat ini.',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                }, function(error) {
                    btn.innerHTML = '<i class="fa fa-crosshairs me-1"></i> OTOMATIS (GPS)';
                    alert('Gagal mendeteksi lokasi GPS. Pastikan fitur lokasi dan izin akses GPS diaktifkan di browser Anda.');
                }, { enableHighAccuracy: true });
            } else {
                alert('Geolocation tidak didukung oleh browser Anda.');
            }
        });
    });
</script>
@endsection
