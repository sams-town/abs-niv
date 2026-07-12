@extends('templates.dashboard')
@section('isi')
<div class="row">
    <div class="col-md-12">
        <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: none; border-bottom: 1px solid #f1f5f9; padding: 24px;">
                <h4 style="font-weight: 800; color: #0f172a; margin: 0;">{{ $title }}</h4>
                <a href="{{ url('/skema-honorarium/create') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 600; padding: 10px 20px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">
                    <i class="fa fa-plus me-2"></i> Tambah Skema
                </a>
            </div>
            <div class="card-body" style="padding: 24px;">
                @if(session('success'))
                    <div class="alert alert-success" style="border-radius: 12px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover" id="skemaTable" style="vertical-align: middle;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">No</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Nama Skema</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Nominal Per Unit</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px;">Deskripsi</th>
                                <th style="font-weight: 700; color: #475569; text-transform: uppercase; font-size: 11px; padding: 16px; width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skemas as $index => $skema)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 16px; font-weight: 600; color: #64748b;">{{ $index + 1 }}</td>
                                    <td style="padding: 16px; font-weight: 700; color: #0f172a;">{{ $skema->nama_skema }}</td>
                                    <td style="padding: 16px; font-weight: 800; color: #4f46e5;">Rp {{ number_format($skema->nominal_per_unit, 0, ',', '.') }}</td>
                                    <td style="padding: 16px; color: #64748b; font-size: 13px;">{{ $skema->deskripsi ?? '-' }}</td>
                                    <td style="padding: 16px;">
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('/skema-honorarium/'.$skema->id.'/edit') }}" class="btn btn-sm btn-outline-warning" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ url('/skema-honorarium/'.$skema->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skema ini?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 6px 12px; font-weight: 600;">
                                                    <i class="fa fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center" style="padding: 32px; color: #94a3b8; font-weight: 500;">
                                        <i class="fa fa-info-circle me-2"></i> Belum ada skema honorarium yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
