@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0 text-right d-flex align-items-center justify-content-end">
                        @php
                            $settings = App\Models\settings::first();
                        @endphp
                        @if($settings && $settings->template_lembur)
                            <a href="{{ url('/storage/'.$settings->template_lembur) }}" target="_blank" class="btn btn-info text-white"><i class="fa fa-download me-2"></i>Template Lembur</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{ url('/data-lembur') }}">
                        <div class="row">
                            <div class="col-3">
                                <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true">
                                    <option value=""selected>Pilih Pegawai</option>
                                    @foreach($user as $u)
                                        @if(request('user_id') == $u->id)
                                            <option value="{{ $u->id }}"selected>{{ $u->name }}</option>
                                        @else
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endif
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
                        <table id="mytable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Pegawai</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Lokasi Masuk</th>
                                    <th>Foto Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Lokasi Pulang</th>
                                    <th>Foto Pulang</th>
                                    <th>Total Lembur</th>
                                    <th>Notes</th>
                                    <th>User Approval</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_lembur as $key => $dl)
                                <tr>
                                    <td>{{ ($data_lembur->currentpage() - 1) * $data_lembur->perpage() + $key + 1 }}.</td>
                                    <td>{{ $dl->User->name }}</td>
                                    <td>{{ $dl->tanggal }}</td>
                                    <td>
                                        @php
                                            $jam_masuk = explode(" ", $dl->jam_masuk);
                                        @endphp
                                        <span class="badge badge-success">{{ $jam_masuk[1] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $jarak_masuk = explode(".", $dl->jarak_masuk);
                                        @endphp
                                        <a href="{{ url('/maps/'.$dl->lat_masuk.'/'.$dl->long_masuk.'/'.$dl->user_id) }}" class="btn btn-sm btn-secondary" target="_blank">lihat</a>
                                        <span class="badge badge-warning">{{ $jarak_masuk[0] }} Meter</span>
                                    </td>
                                    <td>
                                        <img src="{{ url('storage/' . $dl->foto_jam_masuk) }}" style="width: 60px">
                                    </td>
                                    <td>
                                        @if ($dl->jam_keluar == null)
                                            <span class="badge badge-warning">Belum Pulang Lembur</span>
                                        @else
                                            @php
                                                $jam_keluar = explode(" ", $dl->jam_keluar);
                                            @endphp
                                            <span class="badge badge-success">{{ $jam_keluar[1] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dl->jam_keluar == null)
                                            <span class="badge badge-warning">Belum Pulang Lembur</span>
                                        @else
                                            @php
                                                $jarak_keluar = explode(".", $dl->jarak_keluar);
                                            @endphp
                                            <a href="{{ url('/maps/'.$dl->lat_keluar.'/'.$dl->long_keluar.'/'.$dl->user_id) }}" class="btn btn-sm btn-secondary" target="_blank">lihat</a>
                                            <span class="badge badge-warning">{{ $jarak_keluar[0] }} Meter</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dl->jam_keluar == null)
                                            <span class="badge badge-warning">Belum Pulang Lembur</span>
                                        @else
                                            <img src="{{ url('storage/' . $dl->foto_jam_keluar) }}" style="width: 60px">
                                        @endif
                                    </td>
                                    <td>
                                        @if($dl->jam_keluar == null)
                                            <span class="badge badge-warning">Belum Pulang Lembur</span>
                                        @else
                                            @php
                                                $total_lembur = $dl->total_lembur;
                                                $jam   = floor($total_lembur / (60 * 60));
                                                $menit = $total_lembur - ( $jam * (60 * 60) );
                                                $menit2 = floor( $menit / 60 );
                                            @endphp
                                            <span class="badge badge-success">{{ $jam." Jam ".$menit2." Menit" }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $dl->notes }}</td>
                                    <td>
                                        <small>L1: {{ $dl->approvedBy1 ? $dl->approvedBy1->name : '-' }}</small><br>
                                        <small>L2: {{ $dl->approvedBy ? $dl->approvedBy->name : '-' }}</small>
                                    </td>
                                    <td>
                                        @if($dl->status == 'Rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($dl->status_approval_1 == 'Pending')
                                            <span class="badge bg-warning">Pending Manager</span>
                                        @elseif(($dl->status_approval_1 == 'Disetujui' || $dl->status_approval_1 == 'Dilewati') && $dl->status == 'Pending')
                                            <span class="badge bg-info">Approved Manager (Pending Superadmin)</span>
                                        @elseif($dl->status == 'Approved')
                                            <span class="badge bg-success">Approved (Final)</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $dl->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dl->jam_keluar !== null)
                                            <!-- Action Level 1 for Manager -->
                                            @if($dl->status_approval_1 == 'Pending' && auth()->user()->hasRole('kepala_cabang') && $dl->lokasi_id == auth()->user()->lokasi_id)
                                                <ul class="action">
                                                    <li>
                                                        <button class="btn btn-sm btn-info text-white" type="button" data-bs-toggle="modal" data-bs-target="#approvalModalL1{{ $dl->id }}"><i class="fa fa-check-circle me-1"></i>Approve L1</button>

                                                        <div class="modal fade" id="approvalModalL1{{ $dl->id }}" tabindex="-1" role="dialog" aria-labelledby="approvalModalL1Label{{ $dl->id }}" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="approvalModalL1Label{{ $dl->id }}">Approval Lembur Level 1 (Manager)</h5>
                                                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="{{ url('/data-lembur/approval-1/'.$dl->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="modal-body text-start">
                                                                            <div class="form-group mb-3">
                                                                                <label for="status1_{{ $dl->id }}">Status</label>
                                                                                <select name="status" id="status1_{{ $dl->id }}" class="form-control" required style="background: #ffffff !important;">
                                                                                    <option value="">Pilih Status</option>
                                                                                    <option value="Approved">Setujui (Approve)</option>
                                                                                    <option value="Rejected">Tolak (Reject)</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group mb-3">
                                                                                <label for="notes1_{{ $dl->id }}" class="col-form-label">Catatan / Notes:</label>
                                                                                <textarea class="form-control" id="notes1_{{ $dl->id }}" name="notes"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Tutup</button>
                                                                            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            @endif

                                            <!-- Action Level 2 for Superadmin (Admin/HRD) -->
                                            @if($dl->status == 'Pending' && in_array($dl->status_approval_1, ['Disetujui', 'Dilewati']) && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('hrd')))
                                                <ul class="action">
                                                    <li>
                                                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#approvalModalL2{{ $dl->id }}"><i class="fa fa-check-circle me-1"></i>Final Approve</button>

                                                        <div class="modal fade" id="approvalModalL2{{ $dl->id }}" tabindex="-1" role="dialog" aria-labelledby="approvalModalL2Label{{ $dl->id }}" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="approvalModalL2Label{{ $dl->id }}">Final Approval Lembur (Superadmin)</h5>
                                                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="{{ url('/data-lembur/approval-2/'.$dl->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="modal-body text-start">
                                                                            <div class="form-group mb-3">
                                                                                <label for="status2_{{ $dl->id }}">Status</label>
                                                                                <select name="status" id="status2_{{ $dl->id }}" class="form-control" required style="background: #ffffff !important;">
                                                                                    <option value="">Pilih Status</option>
                                                                                    <option value="Approved">Setujui (Approve)</option>
                                                                                    <option value="Rejected">Tolak (Reject)</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group mb-3">
                                                                                <label for="notes2_{{ $dl->id }}" class="col-form-label">Catatan / Notes:</label>
                                                                                <textarea class="form-control" id="notes2_{{ $dl->id }}" name="notes"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Tutup</button>
                                                                            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end me-4 mt-4">
                        {{ $data_lembur->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
