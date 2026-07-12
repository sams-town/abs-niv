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
                        <a class="btn btn-primary btn-sm" href="{{ url('/data-cuti/tambah') }}">+ Tambah</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{ url('/data-cuti') }}">
                            <div class="row">
                                <div class="col-3">
                                    <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true">
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
                                <div class="col-3">
                                    <select name="status_approval_1" class="form-control">
                                        <option value="">Approval Manager (Semua)</option>
                                        @foreach(['Pending','Disetujui','Ditolak','Dilewati'] as $s)
                                            <option value="{{ $s }}" {{ request('status_approval_1')==$s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <select name="status_cuti_filter" class="form-control">
                                        <option value="">Status Final (Semua)</option>
                                        @foreach(['Pending','Diterima','Ditolak'] as $s)
                                            <option value="{{ $s }}" {{ request('status_cuti_filter')==$s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="datetime" class="form-control" name="mulai" placeholder="Tanggal Mulai" id="mulai" value="{{ request('mulai') }}">
                                </div>
                                <div class="col-3">
                                    <input type="datetime" class="form-control" name="akhir" placeholder="Tanggal Akhir" id="akhir" value="{{ request('akhir') }}">
                                </div>
                                <div class="col-3">
                                    <button type="submit" id="search"class="border-0 mt-3" style="background-color: transparent;"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-center" id="mytable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pegawai</th>
                                    <th>Lokasi Pegawai</th>
                                    <th>Nama Cuti</th>
                                    <th>Tanggal</th>
                                    <th>Alasan Cuti</th>
                                    <th>Foto Cuti</th>
                                    <th>Status Cuti</th>
                                    <th>User Approval</th>
                                    <th>Approval Manager</th>
                                    <th>Aksi Approval</th>
                                    <th>Catatan</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_cuti as $key => $dc)
                                <tr>
                                    <td>{{ ($data_cuti->currentpage() - 1) * $data_cuti->perpage() + $key + 1 }}.</td>
                                    <td>{{ $dc->User->name ?? '-' }}</td>
                                    <td>{{ $dc->lokasi->nama_lokasi ?? '-' }}</td>
                                    <td>{{ $dc->nama_cuti ?? '-' }}</td>
                                    <td>{{ $dc->tanggal ?? '-' }}</td>
                                    <td>{{ $dc->alasan_cuti ?? '-' }}</td>
                                    <td>
                                        @if ($dc->foto_cuti)
                                            <img src="{{ url('storage/'.$dc->foto_cuti) }}" style="width: 70px" alt="">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($dc->status_cuti == "Diterima")
                                            <span class="badge badge-success">{{ $dc->status_cuti ?? '-' }}</span>
                                        @elseif($dc->status_cuti == "Ditolak")
                                            <span class="badge badge-danger">{{ $dc->status_cuti ?? '-' }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $dc->status_cuti ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $dc->ua->name ?? '-' }}</td>
                                    <td>
                                        @php $sa1 = $dc->status_approval_1 ?? 'Pending'; @endphp
                                        @if($sa1 == 'Disetujui') <span class="badge badge-success">Disetujui</span>
                                        @elseif($sa1 == 'Ditolak') <span class="badge badge-danger">Ditolak</span>
                                        @elseif($sa1 == 'Dilewati') <span class="badge badge-secondary">Dilewati</span>
                                        @else <span class="badge badge-warning">Pending</span>
                                        @endif
                                        @if($sa1 != 'Pending') <br><small>{{ optional($dc->approver1)->name }}</small> @endif
                                    </td>
                                    <td>
                                        @if(in_array($dc->status_cuti, ['Diterima','Ditolak']))
                                            <span class="badge badge-{{ $dc->status_cuti=='Diterima'?'success':'danger' }}">Final: {{ $dc->status_cuti }}</span>
                                        @elseif(in_array($sa1, ['Disetujui','Dilewati']) && $dc->status_cuti == 'Pending')
                                            @canany(['admin','hrd'])
                                            <form action="{{ url('/data-cuti/approval-2/'.$dc->id) }}" method="post" class="d-inline">
                                                @csrf <input type="hidden" name="action" value="setujui">
                                                <button class="btn btn-success btn-xs" onclick="return confirm('Setujui?')">✓ Final</button>
                                            </form>
                                            <form action="{{ url('/data-cuti/approval-2/'.$dc->id) }}" method="post" class="d-inline">
                                                @csrf <input type="hidden" name="action" value="tolak">
                                                <button class="btn btn-danger btn-xs" onclick="return confirm('Tolak?')">✗ Tolak</button>
                                            </form>
                                            @endcanany
                                        @elseif($sa1 == 'Pending')
                                            @if(auth()->user()->hasRole('kepala_cabang') && auth()->user()->lokasi_id == optional($dc->User)->lokasi_id)
                                            <form action="{{ url('/data-cuti/approval-1/'.$dc->id) }}" method="post" class="d-inline">
                                                @csrf <input type="hidden" name="action" value="setujui">
                                                <button class="btn btn-success btn-xs" onclick="return confirm('Setujui?')">✓ Setujui</button>
                                            </form>
                                            <form action="{{ url('/data-cuti/approval-1/'.$dc->id) }}" method="post" class="d-inline">
                                                @csrf <input type="hidden" name="action" value="tolak">
                                                <button class="btn btn-danger btn-xs" onclick="return confirm('Tolak?')">✗ Tolak</button>
                                            </form>
                                            @else
                                            <span class="badge badge-warning">Menunggu Manager</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $dc->catatan ?? '-' }}</td>
                                    <td>
                                        <ul class="action">
                                            @if($dc->status_cuti == "Diterima")
                                                <li class="me-2">
                                                    <span class="badge badge-success">Sudah Approve</span>
                                                </li>
                                            @else
                                                <li>
                                                    <a href="{{ url('/data-cuti/edit/'.$dc->id) }}"><i style="color: blue" class="fas fa-edit"></i></a>
                                                </li>

                                                <li class="delete">
                                                    <form action="{{ url('/data-cuti/delete/'.$dc->id) }}" method="post" class="d-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <button class="border-0" style="background-color: transparent" onClick="return confirm('Are You Sure')"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mr-4">
                        {{ $data_cuti->links() }}
                    </div>
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
