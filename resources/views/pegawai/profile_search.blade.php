@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Hasil Pencarian: "{{ $q }}"</h5>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($users->isEmpty())
                        <div class="alert alert-warning">
                            Karyawan / Dosen dengan nama <strong>{{ $q }}</strong> tidak ditemukan.
                        </div>
                    @else
                        @foreach($users as $user)
                        <div class="card mb-4 shadow-sm border-0 bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        @if ($user->foto_karyawan && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_karyawan))
                                            <img src="{{ url('/storage/'.$user->foto_karyawan) }}" alt="Foto Profile" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                        @else
                                            <img src="{{ url('assets/img/foto_default.jpg') }}" alt="Foto Profile" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                        @endif
                                        <h6 class="mt-3 font-weight-bold">{{ $user->name }}</h6>
                                        <span class="badge {{ $user->tipe_user == 'dosen' ? 'badge-primary' : 'badge-success' }}">{{ ucfirst($user->tipe_user) }}</span>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="border-bottom pb-2 mb-3">Biodata Lengkap</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Username / NIP / NIDN</small>
                                                <strong>{{ $user->username }} / {{ $user->nip ?? '-' }} / {{ $user->nidn ?? '-' }}</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Email & Telepon</small>
                                                <strong>{{ $user->email ?? '-' }} | {{ $user->telepon ?? '-' }}</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Jabatan</small>
                                                <strong>{{ $user->Jabatan?->nama_jabatan ?? '-' }}</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Lokasi Penempatan</small>
                                                <strong>{{ $user->Lokasi?->nama_lokasi ?? 'Pusat' }}</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Tanggal Lahir & Bergabung</small>
                                                <strong>{{ $user->tgl_lahir ?? '-' }} | {{ $user->tgl_join ?? '-' }}</strong>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block">Jenis Kelamin</small>
                                                <strong>{{ $user->gender == 'L' ? 'Laki-laki' : ($user->gender == 'P' ? 'Perempuan' : '-') }}</strong>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <small class="text-muted d-block">Alamat Lengkap</small>
                                                <strong>{{ $user->alamat ?? '-' }}</strong>
                                            </div>
                                        </div>

                                        <h5 class="border-bottom pb-2 mt-4 mb-3">Arsip & Berkas (Dokumen)</h5>
                                        @if($user->files && $user->files->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="bg-white">
                                                        <tr>
                                                            <th>Jenis Dokumen</th>
                                                            <th class="text-center" style="width: 150px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($user->files as $file)
                                                            <tr>
                                                                <td class="align-middle">{{ $file->jenis_file }}</td>
                                                                <td class="text-center align-middle">
                                                                    <a href="{{ url('/storage/'.$file->fileUpload) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                                                        <i class="fa fa-eye"></i> Lihat
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted small">Belum ada berkas dokumen yang diupload.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
