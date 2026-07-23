@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Detail KPI</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $kpi->category->name }}</td>
                    </tr>
                    <tr>
                        <th>Nama KPI</th>
                        <td>{{ $kpi->name }}</td>
                    </tr>
                    <tr>
                        <th>Target</th>
                        <td>{{ $kpi->target_value }} {{ $kpi->unit }}</td>
                    </tr>
                    <tr>
                        <th>Tipe</th>
                        <td>{{ $kpi->type }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h5 class="text-white mb-0">Tambah Assign</h5>
            </div>
            <div class="card-body">
                <form action="{{ url('/kpi/manajemen/'.$kpi->id.'/assign') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Tipe Assign</label>
                        <select class="form-control" name="assign_type" id="assign_type" required>
                            <option value="jabatan">Per Jabatan / Divisi</option>
                            <option value="user">Per Individu (Karyawan)</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3" id="jabatan_container">
                        <label>Pilih Jabatan</label>
                        <select class="form-control selectpicker" name="jabatan_id" data-live-search="true">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group mb-3" id="user_container" style="display:none;">
                        <label>Pilih Karyawan</label>
                        <select class="form-control selectpicker" name="user_id" data-live-search="true">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->Jabatan ? $u->Jabatan->nama_jabatan : '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Bobot KPI (%)</label>
                        <input type="number" step="0.01" class="form-control" name="weight" placeholder="Misal: 25" required>
                        <small class="text-muted">Total bobot semua KPI untuk satu karyawan/jabatan idealnya 100%.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Assign KPI</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Daftar Assign KPI Ini</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="mytable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tipe</th>
                                <th>Assign Kepada</th>
                                <th>Bobot</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignments as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($item->user_id)
                                        <span class="badge bg-info">Individu</span>
                                    @else
                                        <span class="badge bg-secondary">Jabatan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->user_id)
                                        {{ $item->user->name }}
                                    @else
                                        {{ $item->jabatan->nama_jabatan }}
                                    @endif
                                </td>
                                <td>{{ number_format($item->weight, 2) }}%</td>
                                <td>
                                    <form action="{{ url('/kpi/manajemen/assign/'.$item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin menghapus assign ini?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            @if($assignments->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">Belum ada assign KPI.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(function(){
        $('#assign_type').on('change', function(){
            var type = $(this).val();
            if(type == 'jabatan') {
                $('#jabatan_container').show();
                $('#user_container').hide();
            } else {
                $('#jabatan_container').hide();
                $('#user_container').show();
            }
        });
    });
</script>
@endpush
