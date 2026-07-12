@extends('templates.app')
@section('container')
    <div id="app-wrap" class="style1">
        <div class="tf-container">
            <div class="tf-tab">
                <ul class="menu-tabs tabs-food-news">
                    <li class="nav-tab active">Informasi</li>
                    <li class="nav-tab">Cuti</li>
                    <li class="nav-tab">+ Gaji</li>
                    <li class="nav-tab">- Gaji</li>
                </ul>
                <div class="content-tab pt-tab-space mb-5">
                    <div id="tab-gift-item-1 app-wrap" class="app-wrap active-content">
                        <div class="bill-content">
                            <ul class="mb-5">
                                <div class="card-secton transfer-section mt-2">
                                    <div class="tf-container">
                                        <div class="tf-balance-box">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="inner-left d-flex justify-content-between align-items-center">
                                                    @if(auth()->user()->foto_karyawan == null)
                                                        <img src="{{ url('assets/img/foto_default.jpg') }}" alt="image">
                                                    @else
                                                        <img src="{{ url('/storage/'.auth()->user()->foto_karyawan) }}" alt="image">
                                                    @endif
                                                    <p class="fw_7 on_surface_color">{{ auth()->user()->name }}</p>
                                                </div>
                                                <p class="fw_7 on_surface_color">{{ auth()->user()->Jabatan->nama_jabatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-spacing-20"></div>
                                <form class="tf-form" action="{{ url('/my-profile/update/'.auth()->user()->id) }}" enctype="multipart/form-data" method="POST">
                                    @method('PUT')
                                    @csrf
                                    <div class="tf-container">
                                        <h3>Informasi Pegawai</h3>
                                        <br>
                                            <div class="group-input">
                                                <label for="name">Nama Pegawai</label>
                                                <input type="text" class="@error('name') is-invalid @enderror" id="name" name="name" autofocus value="{{ old('name', $karyawan->name) }}">
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <label for="foto_karyawan" class="form-label">Foto Pegawai</label>
                                            <div class="group-input">
                                                <input class="form-control @error('foto_karyawan') is-invalid @enderror" type="file" id="foto_karyawan" name="foto_karyawan">
                                                @error('foto_karyawan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <input type="hidden" name="foto_karyawan_lama" value="{{ $karyawan->foto_karyawan }}">
                                            <div class="group-input">
                                                <label for="email">Email</label>
                                                <input type="email" class="@error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $karyawan->email) }}">
                                                @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="telepon">Nomor Handphone</label>
                                                <input type="text" class="@error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $karyawan->telepon) }}">
                                                @error('telepon')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="username">Username</label>
                                                <input type="text" class="@error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $karyawan->username) }}">
                                                @error('username')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="mb-6">
                                                <label for="lokasi_id">Lokasi Kantor</label>
                                                <select name="lokasi_id" id="lokasi_id" class="form-control @error('lokasi_id') is-invalid @enderror selectpicker" data-live-search="true" disabled>
                                                    @foreach ($data_lokasi as $dl)
                                                        @if(old('lokasi_id', $karyawan->lokasi_id) == $dl->id)
                                                        <option value="{{ $dl->id }}" selected>{{ $dl->nama_lokasi }}</option>
                                                        @else
                                                        <option value="{{ $dl->id }}">{{ $dl->nama_lokasi }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('lokasi_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="group-input">
                                                <label for="tgl_lahir">Tanggal Lahir</label>
                                                <input type="datetime" class="@error('tgl_lahir') is-invalid @enderror" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $karyawan->tgl_lahir) }}">
                                                @error('tgl_lahir')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="alamat">Alamat</label>
                                                <textarea name="alamat" id="alamat" class="@error('alamat') is-invalid @enderror">{{ old('alamat', $karyawan->alamat) }}</textarea>
                                                @error('alamat')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="tgl_join">Tanggal Masuk Perusahaan</label>
                                                <input type="datetime" class="@error('tgl_join') is-invalid @enderror" id="tgl_join" name="tgl_join" value="{{ old('tgl_join', $karyawan->tgl_join) }}" disabled>
                                                @error('tgl_join')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            @php
                                                if ($karyawan->tgl_join) {
                                                    $startDate = Carbon\Carbon::createFromFormat('Y-m-d', $karyawan->tgl_join, 'Asia/Jakarta');
                                                    $currentDate = Carbon\Carbon::now('Asia/Jakarta');
                                                    if ($startDate->greaterThan($currentDate)) {
                                                        $masa_kerja = "0 Tahun, 0 Bulan, 0 Hari.";
                                                    } else {
                                                        $employmentDuration = $currentDate->diff($startDate);
                                                        $masa_kerja = "{$employmentDuration->y} Tahun, {$employmentDuration->m} Bulan, {$employmentDuration->d} Hari.";
                                                    }
                                                } else {
                                                    $masa_kerja = '';
                                                }
                                            @endphp

                                            <div class="group-input">
                                                <label for="masa_kerja">Masa Kerja</label>
                                                <input type="text" class="@error('masa_kerja') is-invalid @enderror" id="masa_kerja" name="masa_kerja" value="{{ old('masa_kerja', $masa_kerja) }}" disabled>
                                                @error('masa_kerja')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="mb-6">
                                                <label for="role" class="float-left">Role</label>
                                                <select disabled class="selectpicker @error('role') is-invaliform-control d @enderror" id="role" name="role[]" multiple>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->name }}" {{ (is_array(old('role', $user_roles)) && in_array($role->name, old('role', $user_roles))) ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-6">
                                                <?php $gender = array(
                                                [
                                                    "gender" => "Laki-Laki"
                                                ],
                                                [
                                                    "gender" => "Perempuan"
                                                ]);
                                                ?>
                                                <label for="gender">Gender</label>
                                                <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror selectpicker" data-live-search="true">
                                                    <option value="">Pilih Gender</option>
                                                    @foreach ($gender as $g)
                                                        @if(old('gender', $karyawan->gender) == $g["gender"])
                                                        <option value="{{ $g["gender"] }}" selected>{{ $g["gender"] }}</option>
                                                        @else
                                                        <option value="{{ $g["gender"] }}">{{ $g["gender"] }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('gender')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="mb-6">
                                                <?php $is_admin = array(
                                                [
                                                    "is_admin" => "admin"
                                                ],
                                                [
                                                    "is_admin" => "user"
                                                ]);
                                                ?>
                                                <label for="is_admin">Dashboard</label>
                                                <select name="is_admin" id="is_admin" class="form-control @error('is_admin') is-invalid @enderror selectpicker" data-live-search="true" disabled>
                                                    <option value="">Pilih Dashboard</option>
                                                    @foreach ($is_admin as $a)
                                                        @if(old('is_admin', $karyawan->is_admin) == $a["is_admin"])
                                                        <option value="{{ $a["is_admin"] }}" selected>{{ $a["is_admin"] }}</option>
                                                        @else
                                                        <option value="{{ $a["is_admin"] }}">{{ $a["is_admin"] }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('is_admin')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="mb-6">
                                                <label for="status_pajak_id">Status Pajak</label>
                                                <select name="status_pajak_id" id="status_pajak_id" class="form-control @error('status_pajak_id') is-invalid @enderror selectpicker" data-live-search="true" disabled>
                                                    <option value="">Pilih Status</option>
                                                    @foreach ($status_pajak as $pajak)
                                                        @if(old('status_pajak_id', $karyawan->status_pajak_id) == $pajak->id)
                                                            <option value="{{ $pajak->id }}" selected>{{ $pajak->name }}</option>
                                                        @else
                                                            <option value="{{ $pajak->id }}">{{ $pajak->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('status_pajak_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="mb-6">
                                                <label for="jabatan_id">Divisi</label>
                                                <select name="jabatan_id" id="jabatan_id" class="form-control @error('jabatan_id') is-invalid @enderror selectpicker" data-live-search="true" disabled>
                                                    <option value="">Pilih Divisi</option>
                                                    @foreach ($data_jabatan as $dj)
                                                        @if(old('jabatan_id', $karyawan->jabatan_id) == $dj->id)
                                                        <option value="{{ $dj->id }}" selected>{{ $dj->nama_jabatan }}</option>
                                                        @else
                                                        <option value="{{ $dj->id }}">{{ $dj->nama_jabatan }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('jabatan_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="ktp">Nomor KTP</label>
                                                <input type="number" class="@error('ktp') is-invalid @enderror" id="ktp" name="ktp" value="{{ old('ktp', $karyawan->ktp) }}">
                                                @error('ktp')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="kartu_keluarga">Nomor Kartu Keluarga</label>
                                                <input type="number" class="@error('kartu_keluarga') is-invalid @enderror" id="kartu_keluarga" name="kartu_keluarga" value="{{ old('kartu_keluarga', $karyawan->kartu_keluarga) }}">
                                                @error('kartu_keluarga')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="bpjs_kesehatan">Nomor BPJS Kesehatan</label>
                                                <input type="number" class="@error('bpjs_kesehatan') is-invalid @enderror" id="bpjs_kesehatan" name="bpjs_kesehatan" value="{{ old('bpjs_kesehatan', $karyawan->bpjs_kesehatan) }}">
                                                @error('bpjs_kesehatan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="bpjs_ketenagakerjaan">Nomor BPJS Ketenagakerjaan</label>
                                                <input type="number" class="@error('bpjs_ketenagakerjaan') is-invalid @enderror" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" value="{{ old('bpjs_ketenagakerjaan', $karyawan->bpjs_ketenagakerjaan) }}">
                                                @error('bpjs_ketenagakerjaan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="npwp">Nomor NPWP</label>
                                                <input type="number" class="@error('npwp') is-invalid @enderror" id="npwp" name="npwp" value="{{ old('npwp', $karyawan->npwp) }}">
                                                @error('npwp')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="sim">Nomor SIM</label>
                                                <input type="number" class="@error('sim') is-invalid @enderror" id="sim" name="sim" value="{{ old('sim', $karyawan->sim) }}">
                                                @error('sim')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="no_pkwt">Nomor PKWT</label>
                                                <input type="number" class="@error('no_pkwt') is-invalid @enderror" id="no_pkwt" name="no_pkwt" value="{{ old('no_pkwt', $karyawan->no_pkwt) }}">
                                                @error('no_pkwt')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="no_kontrak">Nomor Kontrak</label>
                                                <input type="number" class="@error('no_kontrak') is-invalid @enderror" id="no_kontrak" name="no_kontrak" value="{{ old('no_kontrak', $karyawan->no_kontrak) }}">
                                                @error('no_kontrak')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="tanggal_mulai_pkwt">Tanggal Mulai PKWT</label>
                                                <input type="datetime" class="@error('tanggal_mulai_pkwt') is-invalid @enderror" id="tanggal_mulai_pkwt" name="tanggal_mulai_pkwt" value="{{ old('tanggal_mulai_pkwt', $karyawan->tanggal_mulai_pkwt) }}" disabled>
                                                @error('tanggal_mulai_pkwt')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="group-input">
                                                <label for="tanggal_berakhir_pkwt">Tanggal Berakhir PKWT</label>
                                                <input type="datetime" class="@error('tanggal_berakhir_pkwt') is-invalid @enderror" id="tanggal_berakhir_pkwt" name="tanggal_berakhir_pkwt" value="{{ old('tanggal_berakhir_pkwt', $karyawan->tanggal_berakhir_pkwt) }}" disabled>
                                                @error('tanggal_berakhir_pkwt')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        <div class="group-input">
                                            <label for="rekening">Nomor Rekening</label>
                                            <input type="number" class="@error('rekening') is-invalid @enderror" id="rekening" name="rekening" value="{{ old('rekening', $karyawan->rekening) }}">
                                            @error('rekening')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="group-input">
                                            <label for="nama_rekening">Nama Pemilik Rekening</label>
                                            <input type="text" class="@error('nama_rekening') is-invalid @enderror" id="nama_rekening" name="nama_rekening" value="{{ old('nama_rekening', $karyawan->nama_rekening) }}">
                                            @error('nama_rekening')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="group-input">
                                            <label for="masa_berlaku">Masa Berlaku</label>
                                            <input type="datetime" class="@error('masa_berlaku') is-invalid @enderror" id="masa_berlaku" name="masa_berlaku" value="{{ old('masa_berlaku', $karyawan->masa_berlaku) }}" disabled>
                                            @error('masa_berlaku')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="tf-btn accent large">Save</button>
                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                </form>
                            </ul>
                        </div>
                    </div>

                    <div id="tab-gift-item-2 app-wrap" class="app-wrap">
                        <div class="bill-content">
                            <ul class="mt-3 mb-5">
                                <div class="card-secton transfer-section mt-2">
                                    <div class="tf-container">
                                        <div class="tf-balance-box">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="inner-left d-flex justify-content-between align-items-center">
                                                    @if(auth()->user()->foto_karyawan == null)
                                                        <img src="{{ url('assets/img/foto_default.jpg') }}" alt="image">
                                                    @else
                                                        <img src="{{ url('/storage/'.auth()->user()->foto_karyawan) }}" alt="image">
                                                    @endif
                                                    <p class="fw_7 on_surface_color">{{ auth()->user()->name }}</p>
                                                </div>
                                                <p class="fw_7 on_surface_color">{{ auth()->user()->Jabatan->nama_jabatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-spacing-20"></div>
                                <form class="tf-form" action="{{ url('/my-profile/update/'.auth()->user()->id) }}" enctype="multipart/form-data" method="POST">
                                    @method('PUT')
                                    @csrf
                                    <div class="tf-container">
                                        <h3>Cuti & Izin</h3>
                                        <br>
                                        <div class="group-input">
                                            <label>Cuti</label>
                                            <input type="number" class="@error('izin_cuti') is-invalid @enderror" name="izin_cuti" value="{{ old('izin_cuti', auth()->user()->izin_cuti) }}" readonly />
                                            @error('izin_cuti')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="group-input">
                                            <label>Izin Masuk</label>
                                            <input type="number" class="@error('izin_lainnya') is-invalid @enderror" name="izin_lainnya" value="{{ old('izin_lainnya', auth()->user()->izin_lainnya) }}" readonly />
                                            @error('izin_lainnya')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="group-input">
                                            <label>Izin Telat</label>
                                            <input type="number" class="@error('izin_telat') is-invalid @enderror" name="izin_telat" value="{{ old('izin_telat', auth()->user()->izin_telat) }}" readonly />
                                            @error('izin_telat')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="group-input">
                                            <label>Izin Pulang Cepat</label>
                                            <input type="number" class="@error('izin_pulang_cepat') is-invalid @enderror" name="izin_pulang_cepat" value="{{ old('izin_pulang_cepat', auth()->user()->izin_pulang_cepat) }}" readonly />
                                            @error('izin_pulang_cepat')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                    </div>
                                </form>
                            </ul>
                        </div>
                    </div>

                    <div id="tab-gift-item-3 app-wrap" class="app-wrap">
                        <div class="bill-content">
                            <ul class="mt-3 mb-5">
                                <div class="card-secton transfer-section mt-2">
                                    <div class="tf-container">
                                        <div class="tf-balance-box">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="inner-left d-flex justify-content-between align-items-center">
                                                    @if(auth()->user()->foto_karyawan == null)
                                                        <img src="{{ url('assets/img/foto_default.jpg') }}" alt="image">
                                                    @else
                                                        <img src="{{ url('/storage/'.auth()->user()->foto_karyawan) }}" alt="image">
                                                    @endif
                                                    <p class="fw_7 on_surface_color">{{ auth()->user()->name }}</p>
                                                </div>
                                                <p class="fw_7 on_surface_color">{{ auth()->user()->Jabatan->nama_jabatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-spacing-20"></div>
                                <form class="tf-form" action="{{ url('/my-profile/update/'.auth()->user()->id) }}" enctype="multipart/form-data" method="POST">
                                    @method('PUT')
                                    @csrf
                                    <div class="tf-container">
                                        <h3>Penjumlahan Gaji</h3>
                                        <br>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="gaji_pokok">Gaji Pokok</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('gaji_pokok') is-invalid @enderror" name="gaji_pokok" value="{{ old('gaji_pokok', $karyawan->gaji_pokok) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('gaji_pokok')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="tunjangan_makan">Tunjangan Makan</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('tunjangan_makan') is-invalid @enderror" name="tunjangan_makan" value="{{ old('tunjangan_makan', $karyawan->tunjangan_makan) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Hari</span>
                                                    </div>
                                                    @error('tunjangan_makan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="tunjangan_transport">Tunjangan Transport</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('tunjangan_transport') is-invalid @enderror" name="tunjangan_transport" value="{{ old('tunjangan_transport', $karyawan->tunjangan_transport) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Hari</span>
                                                    </div>
                                                    @error('tunjangan_transport')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="tunjangan_bpjs_kesehatan">Tunjangan BPJS Kesehatan</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('tunjangan_bpjs_kesehatan') is-invalid @enderror" name="tunjangan_bpjs_kesehatan" value="{{ old('tunjangan_bpjs_kesehatan', $karyawan->tunjangan_bpjs_kesehatan) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('tunjangan_bpjs_kesehatan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="tunjangan_bpjs_ketenagakerjaan">Tunjangan BPJS Ketenagakerjaan</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('tunjangan_bpjs_ketenagakerjaan') is-invalid @enderror" name="tunjangan_bpjs_ketenagakerjaan" value="{{ old('tunjangan_bpjs_ketenagakerjaan', $karyawan->tunjangan_bpjs_ketenagakerjaan) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('tunjangan_bpjs_ketenagakerjaan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="lembur">Lembur</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('lembur') is-invalid @enderror" name="lembur" value="{{ old('lembur', $karyawan->lembur) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Jam</span>
                                                    </div>
                                                    @error('lembur')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="kehadiran">100% Kehadiran</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('kehadiran') is-invalid @enderror" name="kehadiran" value="{{ old('kehadiran', $karyawan->kehadiran) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('kehadiran')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="thr">THR</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('thr') is-invalid @enderror" name="thr" value="{{ old('thr', $karyawan->thr) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('thr')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="bonus_pribadi">Bonus Pribadi</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('bonus_pribadi') is-invalid @enderror" name="bonus_pribadi" value="{{ old('bonus_pribadi', $karyawan->bonus_pribadi) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('bonus_pribadi')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="bonus_team">Bonus Team</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('bonus_team') is-invalid @enderror" name="bonus_team" value="{{ old('bonus_team', $karyawan->bonus_team) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('bonus_team')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="bonus_jackpot">Bonus Jackpot</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('bonus_jackpot') is-invalid @enderror" name="bonus_jackpot" value="{{ old('bonus_jackpot', $karyawan->bonus_jackpot) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('bonus_jackpot')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                    </div>
                                </form>
                                <br>
                                <br>
                                <br>
                            </ul>
                        </div>
                    </div>

                    <div id="tab-gift-item-4 app-wrap" class="app-wrap">
                        <div class="bill-content">
                            <ul class="mt-3 mb-5">
                                <div class="card-secton transfer-section mt-2">
                                    <div class="tf-container">
                                        <div class="tf-balance-box">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="inner-left d-flex justify-content-between align-items-center">
                                                    @if(auth()->user()->foto_karyawan == null)
                                                        <img src="{{ url('assets/img/foto_default.jpg') }}" alt="image">
                                                    @else
                                                        <img src="{{ url('/storage/'.auth()->user()->foto_karyawan) }}" alt="image">
                                                    @endif
                                                    <p class="fw_7 on_surface_color">{{ auth()->user()->name }}</p>
                                                </div>
                                                <p class="fw_7 on_surface_color">{{ auth()->user()->Jabatan->nama_jabatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf-spacing-20"></div>
                                <form class="tf-form" action="{{ url('/my-profile/update/'.auth()->user()->id) }}" enctype="multipart/form-data" method="POST">
                                    @method('PUT')
                                    @csrf
                                    <div class="tf-container">
                                        <h3>Pengurangan Gaji</h3>
                                        <br>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="izin">Izin</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('izin') is-invalid @enderror" name="izin" value="{{ old('izin', $karyawan->izin) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ hari</span>
                                                    </div>
                                                    @error('izin')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="terlambat">Terlambat</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('terlambat') is-invalid @enderror" name="terlambat" value="{{ old('terlambat', $karyawan->terlambat) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ hari</span>
                                                    </div>
                                                    @error('terlambat')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="mangkir">Mangkir</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('mangkir') is-invalid @enderror" name="mangkir" value="{{ old('mangkir', $karyawan->mangkir) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ hari</span>
                                                    </div>
                                                    @error('mangkir')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="saldo_kasbon">Saldo Kasbon</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('saldo_kasbon') is-invalid @enderror" name="saldo_kasbon" value="{{ old('saldo_kasbon', $karyawan->saldo_kasbon) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Tahun</span>
                                                    </div>
                                                    @error('saldo_kasbon')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="potongan_bpjs_kesehatan">Potongan BPJS Kesehatan</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('potongan_bpjs_kesehatan') is-invalid @enderror" name="potongan_bpjs_kesehatan" value="{{ old('potongan_bpjs_kesehatan', $karyawan->potongan_bpjs_kesehatan) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('potongan_bpjs_kesehatan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="group-input">
                                                <label style="z-index: 1000" for="potongan_bpjs_ketenagakerjaan">Potongan BPJS Ketenagakerjaan</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control money @error('potongan_bpjs_ketenagakerjaan') is-invalid @enderror" name="potongan_bpjs_ketenagakerjaan" value="{{ old('potongan_bpjs_ketenagakerjaan', $karyawan->potongan_bpjs_ketenagakerjaan) }}" readonly style="background-color:white">
                                                    <div class="input-group-text">
                                                        <span>/ Bulan</span>
                                                    </div>
                                                    @error('potongan_bpjs_ketenagakerjaan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                </form>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>


        </div>
    </div>
    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
        <script>
            $('.money').mask('000,000,000,000,000', {
                reverse: true
            });
            $('select').select2();
        </script>
    @endpush
@endsection

