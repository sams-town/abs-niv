@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12 project-list">
        <div class="card">
            <div class="row">
                <div class="col-md-6 mt-2 p-0 d-flex">
                    <h4>{{ $title }}</h4>
                </div>
                <div class="col-md-6 p-0 text-right">
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importDosenModal">
                        <i class="fa fa-upload me-1"></i> Import Dosen
                    </button>
                    <a class="btn btn-primary btn-sm" href="{{ url('/dosen/tambah') }}">+ Tambah Dosen</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <form action="{{ url('/dosen') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama / NIDN..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>NIDN</th>
                                <th>Jabatan Akademik</th>
                                <th>Mata Kuliah</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data_user as $key => $d)
                            <tr>
                                <td>{{ ($data_user->currentpage()-1)*$data_user->perpage()+$key+1 }}.</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->nidn ?? '-' }}</td>
                                <td>{{ $d->jabatan_akademik ?? '-' }}</td>
                                <td>{{ $d->mata_kuliah ?? '-' }}</td>
                                <td>{{ $d->email ?? '-' }}</td>
                                <td>{{ $d->telepon ?? '-' }}</td>
                                <td>
                                    @if($d->status_aktif)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="position: sticky; right: 0; background-color: rgb(235, 235, 235); z-index: 1;">
                                    <ul class="action">
                                        <li class="edit me-2"><a href="{{ url('/dosen/edit/'.$d->id) }}" title="Edit Dosen"><i class="icon-pencil-alt"></i></a></li>

                                        <li class="me-2"><a href="{{ url('/pegawai/edit-password/'.$d->id) }}" title="Ganti Password"><i class="fa fa-solid fa-key" style="color: rgb(11, 18, 222)"></i></a></li>

                                        <li class="me-2"> <a href="{{ url('/pegawai/shift/'.$d->id) }}" title="Input Shift Dosen"><i style="color:coral" class="fa fa-solid fa-clock"></i></a></li>

                                        <li class="me-2"> <a href="{{ url('/pegawai/dinas-luar/'.$d->id) }}" title="Input Dinas Luar Dosen"><i style="color:rgb(43, 198, 203)" class="fa fa-solid fa-route"></i></a></li>

                                        <li class="me-2"> <a href="{{ url('/pegawai/kontrak/'.$d->id) }}" title="Kontrak Kerja"><i data-feather="trending-up"> </i></a></li>

                                        @if ($d->foto_face_recognition != null && $d->foto_face_recognition != "")
                                            <li class="me-2"><a href="{{ url('/pegawai/face/'.$d->id) }}" title="Face Recognition Terdaftar (Klik untuk ganti)"><i style="color: green" class="fa fa-solid fa-camera"></i><i class="fa fa-solid fa-check" style="color: green; font-size: 10px; margin-left: 2px;"></i></a></li>
                                        @else
                                            <li class="me-2"><a href="{{ url('/pegawai/face/'.$d->id) }}" title="Face Recognition Belum Terdaftar"><i style="color: red" class="fa fa-solid fa-camera"></i><i class="fa fa-solid fa-times" style="color: red; font-size: 10px; margin-left: 2px;"></i></a></li>
                                        @endif

                                        @if($d->status_aktif)
                                        <li class="delete">
                                            <form action="{{ url('/dosen/delete/'.$d->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button title="Nonaktifkan Dosen" class="border-0" style="background-color: transparent;" onclick="return confirm('Nonaktifkan dosen ini?')"><i class="icon-trash"></i></button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center">Tidak ada data dosen.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">{{ $data_user->links() }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Import Dosen Modal -->
<div class="modal fade" id="importDosenModal" tabindex="-1" role="dialog" aria-labelledby="importDosenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importDosenModalLabel">Import Data Dosen</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/dosen/import') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body text-start">
                    @csrf
                    <div class="mb-3">
                        <a href="{{ url('/dosen/template') }}" class="btn btn-outline-info btn-sm">
                            <i class="fa fa-download me-1"></i> Download Template Excel Dosen
                        </a>
                    </div>
                    <div class="form-group">
                        <label for="file_excel_dosen">File Excel / CSV</label>
                        <input type="file" name="file_excel" id="file_excel_dosen" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Import Dosen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
