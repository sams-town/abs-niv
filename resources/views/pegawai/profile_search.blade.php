@extends('templates.dashboard')
@section('isi')
<style>
    .search-profile-card {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .search-profile-card .bio-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 2px;
    }
    .search-profile-card .bio-value {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b !important;
        display: block;
    }
    .search-profile-card .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #334155 !important;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 10px;
        margin-bottom: 16px;
    }
    .profile-photo {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #f1f5f9;
    }
    .profile-name {
        color: #1e293b !important;
        font-weight: 700;
        margin-top: 12px;
        font-size: 15px;
    }
    .badge-dosen {
        background: #3b82f6;
        color: #fff;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-pegawai {
        background: #10b981;
        color: #fff;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .doc-table td, .doc-table th {
        color: #334155 !important;
    }
</style>
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
                <div class="card-header" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 16px 16px 0 0; padding: 20px 24px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0" style="color: #fff; font-weight: 700;">Hasil Pencarian</h5>
                            <small style="color: #94a3b8;">Kata kunci: "{{ $q }}" &mdash; {{ $users->count() }} hasil ditemukan</small>
                        </div>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger" style="border-radius: 8px;">
                            <i class="fa fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body" style="background: #f8fafc;">
                    @if($users->isEmpty())
                        <div class="alert alert-warning" style="border-radius: 12px;">
                            <i class="fa fa-search me-2"></i>
                            Karyawan / Dosen dengan nama <strong>{{ $q }}</strong> tidak ditemukan.
                        </div>
                    @else
                        @foreach($users as $user)
                        <div class="search-profile-card">
                            <div class="card-body p-4">
                                <div class="row">
                                    {{-- Foto & Identitas Singkat --}}
                                    <div class="col-md-3 text-center mb-3 mb-md-0" style="border-right: 1px solid #f1f5f9;">
                                        @if ($user->foto_karyawan && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_karyawan))
                                            <img src="{{ url('/storage/'.$user->foto_karyawan) }}" alt="Foto" class="profile-photo">
                                        @else
                                            <img src="{{ url('assets/img/foto_default.jpg') }}" alt="Foto" class="profile-photo">
                                        @endif
                                        <p class="profile-name">{{ $user->name }}</p>
                                        @if($user->tipe_user == 'dosen')
                                            <span class="badge-dosen">Dosen</span>
                                        @else
                                            <span class="badge-pegawai">Karyawan</span>
                                        @endif
                                    </div>

                                    {{-- Biodata --}}
                                    <div class="col-md-9">
                                        <p class="section-title"><i class="fa fa-user me-2"></i>Biodata Lengkap</p>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Username</span>
                                                <span class="bio-value">{{ $user->username ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">NIP / NIDN</span>
                                                <span class="bio-value">{{ $user->nip ?? '-' }} / {{ $user->nidn ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Email</span>
                                                <span class="bio-value">{{ $user->email ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Telepon / WhatsApp</span>
                                                <span class="bio-value">{{ $user->telepon ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Jabatan</span>
                                                <span class="bio-value">{{ $user->Jabatan?->nama_jabatan ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Lokasi Penempatan</span>
                                                <span class="bio-value">{{ $user->Lokasi?->nama_lokasi ?? 'Pusat' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Tanggal Lahir</span>
                                                <span class="bio-value">{{ $user->tgl_lahir ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Tanggal Bergabung</span>
                                                <span class="bio-value">{{ $user->tgl_join ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="bio-label">Jenis Kelamin</span>
                                                <span class="bio-value">{{ $user->gender == 'L' ? 'Laki-laki' : ($user->gender == 'P' ? 'Perempuan' : '-') }}</span>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <span class="bio-label">Alamat Lengkap</span>
                                                <span class="bio-value">{{ $user->alamat ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <p class="section-title mt-2"><i class="fa fa-folder-open me-2"></i>Arsip & Berkas (Dokumen)</p>
                                        @if($user->files && $user->files->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered doc-table">
                                                    <thead style="background: #f1f5f9;">
                                                        <tr>
                                                            <th style="color: #334155;">Jenis Dokumen</th>
                                                            <th class="text-center" style="width: 120px; color: #334155;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($user->files as $file)
                                                            <tr>
                                                                <td class="align-middle" style="color: #334155;">{{ $file->jenis_file }}</td>
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
                                            <p style="color: #94a3b8; font-size: 13px;">Belum ada berkas dokumen yang diupload.</p>
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
