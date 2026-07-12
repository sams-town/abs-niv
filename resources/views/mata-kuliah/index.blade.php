@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0">
                        <a href="{{ url('/mata-kuliah/tambah') }}" class="btn btn-primary">+ Tambah</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{ url('/mata-kuliah') }}">
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <input type="text" placeholder="Search (Nama, Kode, Prodi, Fakultas)..." class="form-control" value="{{ request('search') }}" name="search">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" id="search" class="btn btn-primary" style="padding: 10px 15px;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mytable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode MK</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>Program Studi</th>
                                    <th>Fakultas</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $key => $mk)
                                    <tr>
                                        <td>{{ ($data->currentpage() - 1) * $data->perpage() + $key + 1 }}.</td>
                                        <td><span class="badge badge-info" style="font-size: 12px; font-weight: bold; background-color: #4f46e5; color: white;">{{ $mk->kode_mk }}</span></td>
                                        <td><strong>{{ $mk->nama_mk }}</strong></td>
                                        <td>{{ $mk->prodi }}</td>
                                        <td><span class="badge badge-light" style="color: #475569; border: 1px solid #cbd5e1;">{{ $mk->fakultas }}</span></td>
                                        <td>
                                            <ul class="action">
                                                <li class="edit me-2">
                                                    <a href="{{ url('/mata-kuliah/edit/'.$mk->id) }}" title="Edit Mata Kuliah"><i class="fa fa-solid fa-edit" style="color: #f59e0b;"></i></a>
                                                </li>
                                                <li class="delete">
                                                    <form action="{{ url('/mata-kuliah/delete/'.$mk->id) }}" method="post" class="d-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <button class="border-0" style="background-color: transparent;" onClick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')" title="Hapus Mata Kuliah"><i class="fa fa-solid fa-trash" style="color: #ef4444;"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data mata kuliah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection
