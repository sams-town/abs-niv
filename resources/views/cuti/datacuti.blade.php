@extends('templates.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="fw-bold mb-1" style="color: #1e293b;">Data Cuti Karyawan</h1>
                        <p class="text-muted mb-0">Review and manage employee leave requests and medical certificates.</p>
                    </div>
                    <div class="d-flex align-items-center">
                        @php
                            $settings = App\Models\settings::first();
                        @endphp
                        @if($settings && $settings->template_cuti)
                            <a class="btn btn-lg btn-info text-white mr-2" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(23,162,184,0.4);" href="{{ url('/storage/'.$settings->template_cuti) }}" target="_blank">
                                <i class="fas fa-download me-2"></i>Template Cuti
                            </a>
                        @endif
                        <a class="btn btn-lg btn-primary text-white" style="border-radius: 50px; background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 4px 15px rgba(79,70,229,0.4);" href="{{ url('/data-cuti/tambah') }}">
                            <i class="fas fa-plus me-2"></i>Tambah
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4" style="border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border: none;">
            <div class="card-body">
                <form action="{{ url('/data-cuti') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Pilih Pegawai</label>
                            <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                                <option value=""selected>Pilih Pegawai</option>
                                @foreach($users as $u)
                                    @if(request('user_id') == $u->id)
                                        <option value="{{ $u->id }}"selected>{{ $u->name }}</option>
                                    @else
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="mulai" placeholder="Tanggal Mulai" id="mulai" value="{{ request('mulai') }}" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="akhir" placeholder="Tanggal Akhir" id="akhir" value="{{ request('akhir') }}" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark btn-lg w-100" style="border-radius: 15px;">
                                <i class="fas fa-search me-2"></i>Cari Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card" style="border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border: none;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="mytable" style="border-collapse: separate; border-spacing: 0 12px;">
                        <thead class="text-muted">
                            <tr>
                                <th class="fw-semibold border-0">No.</th>
                                <th class="fw-semibold border-0">Nama Pegawai</th>
                                <th class="fw-semibold border-0">Lokasi</th>
                                <th class="fw-semibold border-0">Jenis</th>
                                <th class="fw-semibold border-0">Tanggal</th>
                                <th class="fw-semibold border-0">Alasan</th>
                                <th class="fw-semibold border-0">Foto</th>
                                <th class="fw-semibold border-0">Status</th>
                                <th class="fw-semibold border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_cuti as $key => $dc)
                            <tr style="background: white; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                                <td class="border-0 align-middle fw-semibold text-muted">{{ ($data_cuti->currentpage() - 1) * $data_cuti->perpage() + $key + 1 }}.</td>
                                <td class="border-0 align-middle fw-semibold">{{ $dc->User->name ?? '-' }}</td>
                                <td class="border-0 align-middle text-muted">{{ $dc->lokasi->nama_lokasi ?? '-' }}</td>
                                <td class="border-0 align-middle">{{ $dc->nama_cuti ?? '-' }}</td>
                                <td class="border-0 align-middle text-muted">{{ $dc->tanggal ?? '-' }}</td>
                                <td class="border-0 align-middle text-muted">{{ $dc->alasan_cuti ?? '-' }}</td>
                                <td class="border-0 align-middle">
                                    @if ($dc->foto_cuti)
                                        <img src="{{ url('storage/'.$dc->foto_cuti) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px; border: 2px solid #e2e8f0;" alt="">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height:60px; border-radius:10px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="border-0 align-middle">
                                    @php $sa1 = $dc->status_approval_1 ?? 'Pending'; @endphp
                                    @if(in_array($dc->status_cuti, ['Diterima','Ditolak']))
                                        <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: {{ $dc->status_cuti == 'Diterima' ? '#dcfce7' : '#fee2e2' }}; color: {{ $dc->status_cuti == 'Diterima' ? '#166534' : '#991b1b' }};">
                                            <i class="fas fa-check-circle me-1"></i>{{ $dc->status_cuti }}
                                        </span>
                                    @elseif($sa1 == 'Disetujui')
                                        <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fef3c7; color: #92400e;">
                                            <i class="fas fa-clock me-1"></i>Disetujui Manager
                                        </span>
                                    @elseif($sa1 == 'Ditolak')
                                        <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fee2e2; color: #991b1b;">
                                            <i class="fas fa-times-circle me-1"></i>Ditolak Manager
                                        </span>
                                    @else
                                        <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fef3c7; color: #92400e;">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="border-0 align-middle">
                                    <div class="d-flex gap-2">
                                        @if(in_array($dc->status_cuti, ['Diterima','Ditolak']))
                                            <button class="btn btn-outline-success btn-sm rounded-pill disabled" style="cursor: default;">
                                                <i class="fas fa-check-circle me-1"></i>Approved
                                            </button>
                                        @elseif(in_array($sa1, ['Disetujui','Dilewati']) && $dc->status_cuti == 'Pending')
                                            @canany(['admin','hrd'])
                                            <form action="{{ url('/data-cuti/approval-2/'.$dc->id) }}" method="post" class="d-inline">
                                                @csrf <input type="hidden" name="action" value="setujui">
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill" onclick="return confirm('Setujui?')">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>
                                            @endcanany
                                        @elseif($sa1 == 'Pending')
                                            @if(auth()->user()->hasRole('kepala_cabang') && auth()->user()->lokasi_id == optional($dc->User)->lokasi_id)
                                            <form action="{{ url('/data-cuti/approval-1/'.$dc->id) }}" method="post" class="d-inline">
                                                @csrf <input type="hidden" name="action" value="setujui">
                                                <button type="submit" class="btn btn-success btn-sm rounded-pill" onclick="return confirm('Setujui?')">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                        
                                        @if(!in_array($dc->status_cuti, ['Diterima','Ditolak']))
                                            <a href="{{ url('/data-cuti/edit/'.$dc->id) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ url('/data-cuti/delete/'.$dc->id) }}" method="post" class="d-inline">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" onClick="return confirm('Are You Sure?')">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    {{ $data_cuti->links() }}
                </div>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $(document).ready(function() {
                $('#mulai').change(function(){
                    var mulai = $(this).val();
                    $('#akhir').val(mulai);
                });
            });
        </script>
    @endpush
@endsection
