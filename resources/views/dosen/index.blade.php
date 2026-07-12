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
                                <td>
                                    <a href="{{ url('/dosen/edit/'.$d->id) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                    @if($d->status_aktif)
                                    <form action="{{ url('/dosen/delete/'.$d->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs" onclick="return confirm('Nonaktifkan dosen ini?')"><i class="fas fa-ban"></i></button>
                                    </form>
                                    @endif
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
@endsection
