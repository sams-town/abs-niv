@extends('templates.dashboard')
@section('isi')
    <style>
        .folder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .folder-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            text-decoration: none !important;
            color: inherit;
        }
        .folder-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(83, 61, 234, 0.15);
            border-color: #cbd5e1;
        }
        .folder-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #533dea, #3b28b5);
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .folder-card:hover::before {
            opacity: 1;
        }
        .folder-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .folder-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(83, 61, 234, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #533dea;
            font-size: 24px;
            transition: transform 0.2s ease;
        }
        .folder-card:hover .folder-icon-wrapper {
            transform: scale(1.05) rotate(-5deg);
            background: #533dea;
            color: #ffffff;
        }
        .user-avatar-mini {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .folder-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .folder-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 12px;
        }
        .folder-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
            color: #475569;
        }
        .file-count-badge {
            background-color: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }
        .folder-card:hover .file-count-badge {
            background-color: rgba(83, 61, 234, 0.1);
            color: #533dea;
        }
    </style>

    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0">
                        <a href="{{ url('/dokumen/tambah') }}" class="btn btn-primary">+ Tambah Dokumen</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <form action="{{ url('/dokumen') }}">
                        <div class="row mb-2">
                            <div class="col-md-4 col-8">
                                <div class="input-group">
                                    <input type="text" placeholder="Cari nama pegawai atau dosen..." class="form-control" value="{{ request('search') }}" name="search">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            @if(request('search'))
                                <div class="col-md-2 col-4">
                                    <a href="{{ url('/dokumen') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    @if($data_user->count() > 0)
                        <div class="folder-grid">
                            @foreach ($data_user as $du)
                                <a href="{{ url('/dokumen/user/'.$du->id) }}" class="folder-card">
                                    <div>
                                        <div class="folder-header">
                                            <div class="folder-icon-wrapper">
                                                <i class="fa fa-folder-open"></i>
                                            </div>
                                            @if($du->foto_karyawan)
                                                <img class="user-avatar-mini" src="{{ url('storage/'.$du->foto_karyawan) }}" alt="Avatar">
                                            @else
                                                <img class="user-avatar-mini" src="{{ url('assets/img/foto_default.jpg') }}" alt="Avatar">
                                            @endif
                                        </div>
                                        <div class="folder-title">{{ $du->name }}</div>
                                        <div class="folder-subtitle">
                                            <span class="badge {{ $du->tipe_user == 'dosen' ? 'bg-info' : 'bg-secondary' }}">
                                                {{ ucfirst($du->tipe_user ?? 'Pegawai') }}
                                            </span>
                                            &bull; {{ $du->Jabatan->nama_jabatan ?? 'Staf' }}
                                        </div>
                                    </div>
                                    <div class="folder-footer">
                                        <span>Total Berkas</span>
                                        <span class="file-count-badge">{{ $du->sip_count + $du->files_count }} file</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h5 class="text-muted">Tidak ada data pegawai atau dosen ditemukan.</h5>
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mt-4">
                        {{ $data_user->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
