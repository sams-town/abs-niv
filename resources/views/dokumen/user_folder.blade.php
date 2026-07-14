@extends('templates.dashboard')
@section('isi')
    <style>
        .profile-folder-header {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .profile-folder-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .profile-folder-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f1f5f9;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        .profile-folder-details h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
            font-size: 22px;
        }
        .profile-folder-details p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
        .profile-folder-badge {
            display: inline-block;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .doc-section-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .doc-section-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: #0f172a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }
        .doc-section-title i {
            color: #533dea;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <a href="{{ url('/dokumen') }}" class="btn btn-light btn-sm mb-4"><i class="fa fa-arrow-left me-2"></i> Kembali ke Daftar Folder</a>
            
            <div class="profile-folder-header">
                <div class="profile-folder-info">
                    @if($user->foto_karyawan)
                        <img class="profile-folder-avatar" src="{{ url('storage/'.$user->foto_karyawan) }}" alt="Profile Photo">
                    @else
                        <img class="profile-folder-avatar" src="{{ url('assets/img/foto_default.jpg') }}" alt="Profile Photo">
                    @endif
                    <div class="profile-folder-details">
                        <h3>{{ $user->name }}</h3>
                        <p><i class="fa fa-envelope me-2"></i> {{ $user->email }} &bull; <i class="fa fa-phone me-2"></i> {{ $user->telepon }}</p>
                        <p><i class="fa fa-briefcase me-2"></i> Divisi: {{ $user->Jabatan->nama_jabatan ?? 'Staf' }} &bull; Lokasi: {{ $user->Lokasi->nama_lokasi ?? '-' }}</p>
                        <span class="badge {{ $user->tipe_user == 'dosen' ? 'bg-info' : 'bg-secondary' }} profile-folder-badge">
                            {{ $user->tipe_user ?? 'Pegawai' }}
                        </span>
                    </div>
                </div>
                <div>
                    <a href="{{ url('/dokumen/tambah?user_id='.$user->id) }}" class="btn btn-primary"><i class="fa fa-plus me-2"></i> Tambah Dokumen Kepegawaian</a>
                </div>
            </div>
        </div>

        <!-- Dokumen Kepegawaian (SIP) -->
        <div class="col-md-12">
            <div class="doc-section-card">
                <div class="doc-section-title">
                    <i class="fa fa-folder-open"></i>
                    <span>Dokumen Kepegawaian (Masa Berlaku / SIP)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No.</th>
                                <th>Nama Dokumen</th>
                                <th>Tanggal Upload / Berlaku</th>
                                <th style="width: 150px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($sip_dokumen->count() > 0)
                                @foreach ($sip_dokumen as $key => $sd)
                                    <tr>
                                        <td>{{ $key + 1 }}.</td>
                                        <td><strong>{{ $sd->nama_dokumen }}</strong></td>
                                        <td>{{ $sd->tanggal_berakhir }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if($sd->file)
                                                    <a href="{{ url('storage/'.$sd->file) }}" class="btn btn-sm btn-info btn-circle" target="_blank" title="Download"><i class="fa fa-solid fa-download"></i></a>
                                                @endif
                                                <a href="{{ url('/dokumen/edit/'.$sd->id) }}" class="btn btn-sm btn-warning btn-circle" title="Edit"><i class="fa fa-solid fa-edit"></i></a>
                                                <form action="{{ url('/dokumen/delete/'.$sd->id) }}" method="post" class="d-inline">
                                                    @method('delete')
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger btn-circle" title="Delete" onClick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')"><i class="fa fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada dokumen kepegawaian untuk user ini.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dokumen Tambahan (File Upload) -->
        <div class="col-md-12">
            <div class="doc-section-card">
                <div class="doc-section-title">
                    <i class="fa fa-file-alt"></i>
                    <span>Dokumen Tambahan & Unggahan Profil</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No.</th>
                                <th>Jenis File / Dokumen</th>
                                <th>Waktu Upload</th>
                                <th style="width: 150px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($file_dokumen->count() > 0)
                                @foreach ($file_dokumen as $key => $fd)
                                    <tr>
                                        <td>{{ $key + 1 }}.</td>
                                        <td><strong>{{ $fd->jenis_file }}</strong></td>
                                        <td>{{ $fd->created_at ? $fd->created_at->format('d M Y H:i') : '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if($fd->fileUpload)
                                                    <a href="{{ url('storage/'.$fd->fileUpload) }}" class="btn btn-sm btn-info btn-circle" target="_blank" title="Download"><i class="fa fa-solid fa-download"></i></a>
                                                @endif
                                                <a href="{{ url('/file/edit/'.$fd->id) }}" class="btn btn-sm btn-warning btn-circle" title="Edit"><i class="fa fa-solid fa-edit"></i></a>
                                                <form action="{{ url('/file/delete/'.$fd->id) }}" method="post" class="d-inline">
                                                    @method('delete')
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger btn-circle" title="Delete" onClick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')"><i class="fa fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada dokumen tambahan untuk user ini.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
