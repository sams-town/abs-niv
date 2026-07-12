@extends('templates.dashboard')
@section('isi')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ url('/data-cuti') }}" class="btn btn-lg btn-outline-dark rounded-circle p-3" style="width: 60px; height:60px;">
                        <i class="fas fa-arrow-left fs-4"></i>
                    </a>
                    <div>
                        <h1 class="fw-bold mb-1" style="color: #1e293b;">Formulir Cuti</h1>
                        <p class="text-muted mb-0">Ajukan cuti atau izin pegawai</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card" style="border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border: none;">
            <div class="card-body p-5">
                <form method="post" action="{{ url('/data-cuti/proses-tambah') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="user_id_ajax" class="form-label fw-semibold text-muted mb-2">Pegawai</label>
                            <select id="user_id_ajax" name="user_id" class="form-control selectpicker" data-live-search="true" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                                <option value="">Pilih Pegawai</option>
                                @foreach ($data_user as $du)
                                    @if(old('user_id') == $du->id)
                                        <option value="{{ $du->id }}" selected>{{ $du->name }}</option>
                                    @else
                                        <option value="{{ $du->id }}">{{ $du->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nama_cuti_ajax" class="form-label fw-semibold text-muted mb-2">Jenis Cuti</label>
                            <select name="nama_cuti" id="nama_cuti_ajax" class="form-control" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                                <option value="">Pilih Cuti</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label fw-semibold text-muted mb-2">Mulai</label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                            @error('tanggal_mulai')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="tanggal_akhir" class="form-label fw-semibold text-muted mb-2">Sampai</label>
                            <input type="date" class="form-control @error('tanggal_akhir') is-invalid @enderror" name="tanggal_akhir" id="tanggal_akhir" value="{{ old('tanggal_akhir') }}" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">
                            @error('tanggal_akhir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="foto_cuti" class="form-label fw-semibold text-muted mb-2">Unggah Foto</label>
                            <div class="border-2 border-dashed rounded-4 p-4 text-center" style="border-radius: 20px; border-color: #cbd5e1; background-color: #f8fafc;">
                                <div class="d-flex flex-column align-items-center gap-3">
                                    <div class="bg-white rounded-4 p-4 shadow-sm">
                                        <i class="fas fa-image fs-2 text-muted"></i>
                                    </div>
                                    <div class="text-muted">
                                        <p class="mb-1">Lampiran bukti (opsional)</p>
                                    </div>
                                    <input type="file" name="foto_cuti" id="foto_cuti" class="form-control-file @error('foto_cuti') is-invalid @enderror" style="max-width: 300px;">
                                </div>
                            </div>
                            @error('foto_cuti')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="alasan_cuti" class="form-label fw-semibold text-muted mb-2">Alasan</label>
                            <textarea class="form-control @error('alasan_cuti') is-invalid @enderror" id="alasan_cuti" name="alasan_cuti" rows="4" style="border-radius: 15px; border: 1px solid #e2e8f0; padding: 12px 16px;">{{ old('alasan_cuti') }}</textarea>
                            @error('alasan_cuti')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <input type="hidden" name="tanggal">
                        <input type="hidden" name="status_cuti">
                        
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <a href="{{ url('/data-cuti') }}" class="btn btn-lg btn-outline-secondary px-5" style="border-radius: 50px; border-width: 2px;">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-lg text-white px-5" style="border-radius: 50px; background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 4px 15px rgba(79,70,229,0.4);">
                                    <i class="fas fa-save me-2"></i>Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $('#nama_cuti_ajax').select2();
            $(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            })

            $(function(){
                $('#user_id_ajax').on('change', function(){
                    let user_id = $('#user_id_ajax').val();

                    $.ajax({
                        type : 'POST',
                        url : "{{ url('/data-cuti/getuserid') }}",
                        data :  {id:user_id},
                        cache : false,
                        success: function(msg){
                            $('#nama_cuti_ajax').select2('destroy');
                            $('#nama_cuti_ajax').html(msg);
                            $('#nama_cuti_ajax').select2();
                        },
                        error: function(data){
                            console.log('error:' ,data);
                        }
                    })
                })
            })
        </script>
    @endpush
@endsection
