@extends('templates.dashboard')
@section('isi')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: none; border-bottom: 1px solid #f1f5f9; padding: 24px;">
                <h4 style="font-weight: 800; color: #0f172a; margin: 0;">{{ $title }}</h4>
                <a href="{{ url('/dosen') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 600; padding: 6px 16px;">
                    <i class="fa fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body" style="padding: 24px;">
                @if($errors->any())
                    <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 20px;">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info mb-4" style="border-radius: 12px; border: none; background-color: #eff6ff; color: #1e40af;">
                    <h6 class="mb-2" style="font-weight: 700;"><i class="fa fa-info-circle me-1"></i> Detail Jadwal Mengajar</h6>
                    <table class="table table-borderless table-sm mb-0" style="color: #1e40af;">
                        <tr>
                            <td style="font-weight: 600; width: 120px; padding: 2px 0;">Mata Kuliah:</td>
                            <td style="padding: 2px 0;">{{ $jadwal->mata_kuliah }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 2px 0;">Nama Kelas:</td>
                            <td style="padding: 2px 0;">{{ $jadwal->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; padding: 2px 0;">Waktu:</td>
                            <td style="padding: 2px 0;">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('d M Y, H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</td>
                        </tr>
                    </table>
                </div>

                <form action="{{ url('/sesi-daring') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

                    <div class="form-group mb-4">
                        <label for="meeting_url" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Meeting URL (Zoom/GMeet/Teams) <span class="text-danger">*</span></label>
                        <input type="text" name="meeting_url" id="meeting_url" class="form-control" required placeholder="https://zoom.us/j/..." style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="meeting_id" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Meeting ID <span class="text-danger">*</span></label>
                                <input type="text" name="meeting_id" id="meeting_id" class="form-control" required placeholder="Contoh: 812 345 6789" style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="passcode" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Passcode / Password <span class="text-danger">*</span></label>
                                <input type="text" name="passcode" id="passcode" class="form-control" required placeholder="Contoh: 123456" style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="catatan" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan Tambahan</label>
                        <textarea name="catatan" id="catatan" rows="4" class="form-control" placeholder="Tuliskan catatan opsional..." style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ url('/dosen') }}" class="btn btn-light" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">Batal</a>
                        <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">Jadwalkan Sesi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
