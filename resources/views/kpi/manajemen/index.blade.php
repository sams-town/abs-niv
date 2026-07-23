@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ $title }}</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="fa fa-plus"></i> Tambah KPI
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="mytable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Nama KPI</th>
                                <th>Satuan / Unit</th>
                                <th>Tipe</th>
                                <th>Target</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kpis as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->category->name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>
                                    @if($item->type == 'Higher is Better')
                                        <span class="badge bg-success">Lebih Tinggi Lebih Baik</span>
                                    @else
                                        <span class="badge bg-danger">Lebih Rendah Lebih Baik</span>
                                    @endif
                                </td>
                                <td>{{ number_format($item->target_value, 2) }}</td>
                                <td>
                                    <a href="{{ url('/kpi/manajemen/'.$item->id.'/assign') }}" class="btn btn-sm btn-info" title="Assign KPI"><i class="fa fa-users"></i> Assign</a>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="Edit"><i class="fa fa-edit"></i></button>
                                    <form action="{{ url('/kpi/manajemen/'.$item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus KPI ini?')" title="Hapus"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ url('/kpi/manajemen/'.$item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit KPI</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group mb-3">
                                                    <label>Kategori</label>
                                                    <select class="form-control selectpicker" name="category_id" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Nama KPI</label>
                                                    <input type="text" class="form-control" name="name" value="{{ $item->name }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Satuan (Unit)</label>
                                                    <input type="text" class="form-control" name="unit" value="{{ $item->unit }}" placeholder="Misal: %, Rupiah, Kasus" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Tipe</label>
                                                    <select class="form-control" name="type" required>
                                                        <option value="Higher is Better" {{ $item->type == 'Higher is Better' ? 'selected' : '' }}>Higher is Better (Makin Tinggi Makin Baik)</option>
                                                        <option value="Lower is Better" {{ $item->type == 'Lower is Better' ? 'selected' : '' }}>Lower is Better (Makin Rendah Makin Baik)</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Target Standar</label>
                                                    <input type="number" step="0.01" class="form-control" name="target_value" value="{{ $item->target_value }}" required>
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
            <form action="{{ url('/kpi/manajemen') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah KPI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Kategori</label>
                        <select class="form-control selectpicker" name="category_id" required style="width: 100%">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama KPI</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Satuan (Unit)</label>
                        <input type="text" class="form-control" name="unit" placeholder="Misal: %, Rupiah, Kasus" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Tipe</label>
                        <select class="form-control" name="type" required>
                            <option value="Higher is Better">Higher is Better (Makin Tinggi Makin Baik)</option>
                            <option value="Lower is Better">Lower is Better (Makin Rendah Makin Baik)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Target Standar</label>
                        <input type="number" step="0.01" class="form-control" name="target_value" required>
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
