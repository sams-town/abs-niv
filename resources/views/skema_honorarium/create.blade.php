@extends('templates.dashboard')
@section('isi')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: none; border-bottom: 1px solid #f1f5f9; padding: 24px;">
                <h4 style="font-weight: 800; color: #0f172a; margin: 0;">{{ $title }}</h4>
                <a href="{{ url('/skema-honorarium') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 600; padding: 6px 16px;">
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

                <form action="{{ url('/skema-honorarium') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="nama_skema" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Skema <span class="text-danger">*</span></label>
                        <input type="text" name="nama_skema" id="nama_skema" class="form-control" required placeholder="Contoh: Honor Asisten Ahli S1" style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                    </div>

                    <div class="form-group mb-4">
                        <label for="nominal_per_unit" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nominal Per Unit <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 12px 0 0 12px; font-weight: 700; color: #475569;">Rp</span>
                            <input type="text" name="nominal_per_unit" id="nominal_per_unit" class="form-control format-rupiah" required placeholder="Contoh: 150.000" style="border-radius: 0 12px 12px 0; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="deskripsi" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Deskripsi / Keterangan</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control" placeholder="Tuliskan keterangan detail skema honorarium..." style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ url('/skema-honorarium') }}" class="btn btn-light" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">Batal</a>
                        <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">Simpan Skema</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('nominal_per_unit');
        input.addEventListener('keyup', function(e) {
            this.value = formatRupiah(this.value);
        });
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>
@endsection
