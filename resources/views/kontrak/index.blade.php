@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0 text-end">
                        @if($isAdmin)
                            <a href="{{ url('/kontrak/tambah') }}" class="btn btn-primary ms-2">+ Tambah</a>
                            <a href="{{ url('/kontrak/export') }}{{ $_GET?'?'.$_SERVER['QUERY_STRING']: '' }}" class="btn btn-success">Export</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($expiringCount > 0)
            <div class="col-md-12">
                <div class="alert alert-danger d-flex align-items-center" role="alert" style="border-radius: 12px; background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 16px; margin-bottom: 24px;">
                    <i class="fa fa-exclamation-triangle me-3" style="font-size: 20px;"></i>
                    <div>
                        <strong style="font-weight: 800;">Pemberitahuan Kontrak Kerja:</strong> 
                        Ada {{ $expiringCount }} kontrak kerja yang akan/telah habis dalam waktu dekat (kurang dari 30 hari). Baris kontrak ditandai dengan warna merah di bawah.
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-12">
            <div class="card">
                @if($isAdmin)
                    <div class="card-header">
                        <form action="{{ url('/kontrak') }}">
                            <div class="row mb-2">
                                <div class="col-5">
                                    <input type="text" class="form-control" name="nama" placeholder="Nama Pegawai" id="nama" value="{{ request('nama') }}">
                                </div>
                                <div class="col-3">
                                    <input type="date" class="form-control" name="mulai" placeholder="Tanggal Mulai" id="mulai" value="{{ request('mulai') }}">
                                </div>
                                <div class="col-3">
                                    <input type="date" class="form-control" name="akhir" placeholder="Tanggal Akhir" id="akhir" value="{{ request('akhir') }}">
                                </div>
                                <div class="col-1 text-center">
                                    <button type="submit" id="search" class="border-0 mt-2" style="background-color: transparent; font-size: 18px; color: #4f46e5;"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                <div class="card-body">
                    <div class="table-responsive" style="border-radius: 10px">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr style="background-color: #f8fafc;">
                                    <th class="text-center" style="position: sticky; left: 0; background-color: rgb(215, 215, 215); z-index: 2; width: 60px;">No.</th>
                                    <th style="position: sticky; left: 60px; background-color: rgb(215, 215, 215); z-index: 2; min-width: 200px;" class="text-center">Nama Pegawai</th>
                                    <th style="min-width: 140px; background-color:rgb(243, 243, 243);" class="text-center">Tanggal Input</th>
                                    <th style="min-width: 250px; background-color:rgb(243, 243, 243);" class="text-center">Jenis Kontrak</th>
                                    <th style="min-width: 140px; background-color:rgb(243, 243, 243);" class="text-center">Tanggal Mulai</th>
                                    <th style="min-width: 140px; background-color:rgb(243, 243, 243);" class="text-center">Tanggal Selesai</th>
                                    <th style="min-width: 300px; background-color:rgb(243, 243, 243);" class="text-center">Keterangan</th>
                                    <th style="min-width: 180px; background-color:rgb(243, 243, 243);" class="text-center">Berkas Kontrak</th>
                                    @if($isAdmin)
                                        <th class="text-center" style="position: sticky; right: 0; background-color: rgb(215, 215, 215); z-index: 2; width: 120px;">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($kontraks) <= 0)
                                    <tr>
                                        <td colspan="{{ $isAdmin ? 9 : 8 }}" class="text-center py-4 text-muted">
                                            <i class="fa fa-info-circle me-2"></i> Tidak Ada Data Kontrak Kerja
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($kontraks as $key => $kontrak)
                                        @php
                                            $isExpiring = false;
                                            if ($kontrak->tanggal_selesai) {
                                                $endDate = \Carbon\Carbon::parse($kontrak->tanggal_selesai);
                                                $isExpiring = $endDate->isPast() || $endDate->diffInDays(\Carbon\Carbon::now()) <= 30;
                                            }
                                        @endphp
                                        <tr style="{{ $isExpiring ? 'background-color: #fef2f2; color: #991b1b;' : '' }}">
                                            <td class="text-center" style="position: sticky; left: 0; background-color: {{ $isExpiring ? '#fee2e2' : '#f8fafc' }}; z-index: 1;">{{ ($kontraks->currentpage() - 1) * $kontraks->perpage() + $key + 1 }}.</td>
                                            <td style="position: sticky; left: 60px; background-color: {{ $isExpiring ? '#fee2e2' : '#f8fafc' }}; z-index: 1; font-weight: 700;">{{ $kontrak->user->name ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($kontrak->tanggal)
                                                    @php
                                                        Carbon\Carbon::setLocale('id');
                                                        $tanggal = Carbon\Carbon::parse($kontrak->tanggal);
                                                        $new_tanggal = $tanggal->translatedFormat('d F Y');
                                                    @endphp
                                                    {{ $new_tanggal }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td style="font-weight: 600;">{{ $kontrak->jenis_kontrak ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($kontrak->tanggal_mulai)
                                                    @php
                                                        Carbon\Carbon::setLocale('id');
                                                        $tanggal_mulai = Carbon\Carbon::parse($kontrak->tanggal_mulai);
                                                        $new_tanggal_mulai = $tanggal_mulai->translatedFormat('d F Y');
                                                    @endphp
                                                    {{ $new_tanggal_mulai }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center" style="{{ $isExpiring ? 'font-weight: 800; color: #dc2626;' : '' }}">
                                                @if ($kontrak->tanggal_selesai)
                                                    @php
                                                        Carbon\Carbon::setLocale('id');
                                                        $tanggal_selesai = Carbon\Carbon::parse($kontrak->tanggal_selesai);
                                                        $new_tanggal_selesai = $tanggal_selesai->translatedFormat('d F Y');
                                                    @endphp
                                                    {{ $new_tanggal_selesai }}
                                                    @if($isExpiring)
                                                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 4px;">
                                                            <i class="fa fa-exclamation-circle"></i> {{ $tanggal_selesai->isPast() ? 'Habis/Expired' : 'Akan Habis' }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-muted" style="font-size: 12px; font-weight: 500;">Permanen (PKWTT)</span>
                                                @endif
                                            </td>
                                            <td>{!! $kontrak->keterangan ? nl2br(e($kontrak->keterangan)) : '-' !!}</td>
                                            <td>
                                                @if ($kontrak->kontrak_file_path)
                                                    <a href="{{ url('/storage/'.$kontrak->kontrak_file_path) }}" class="btn btn-xs btn-outline-danger" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;" target="_blank">
                                                        <i class="fa fa-download me-1"></i> Unduh Berkas
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            @if($isAdmin)
                                                <td style="position: sticky; right: 0; background-color: {{ $isExpiring ? '#fee2e2' : '#f8fafc' }}; z-index: 1;">
                                                    <ul class="action d-flex justify-content-center gap-2 list-unstyled mb-0">
                                                        <li class="edit">
                                                            <a href="{{ url('/kontrak/edit/'.$kontrak->id) }}" class="btn btn-sm btn-warning text-white" style="border-radius: 6px; padding: 4px 8px;"><i class="fa fa-edit"></i></a>
                                                        </li>
                                                        <li class="delete">
                                                            <form action="{{ url('/kontrak/delete/'.$kontrak->id) }}" method="post" class="d-inline">
                                                                @method('delete')
                                                                @csrf
                                                                <button class="btn btn-sm btn-danger" style="border-radius: 6px; padding: 4px 8px;" onClick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"><i class="fa fa-trash"></i></button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $kontraks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection
