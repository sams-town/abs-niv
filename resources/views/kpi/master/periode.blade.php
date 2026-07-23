@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ $title }}</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="fa fa-plus"></i> Tambah Periode
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="mytable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Periode</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periode as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}"><i class="fa fa-edit"></i></button>
                                    <form action="{{ url('/kpi/periode/'.$item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus periode ini?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ url('/kpi/periode/'.$item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Periode KPI</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group mb-3">
                                                    <label>Nama Periode</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $item->name }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Tanggal Mulai</label>
                                                    <input type="date" class="form-control" name="start_date" value="{{ $item->start_date }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Tanggal Selesai</label>
                                                    <input type="date" class="form-control" name="end_date" value="{{ $item->end_date }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Status</label>
                                                    <select class="form-control" name="is_active">
                                                        <option value="1" {{ $item->is_active ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ !$item->is_active ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/kpi/periode') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Periode KPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Nama Periode</label>
                        <input type="text" class="form-control" name="name" placeholder="Misal: Q1 2024" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Tanggal Selesai</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
