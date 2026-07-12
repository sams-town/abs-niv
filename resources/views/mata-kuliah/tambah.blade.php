@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 p-0 d-flex mt-2">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0">
                        <a href="{{ url('/mata-kuliah') }}" class="btn btn-danger btn-sm ms-2">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card p-4">
                <form method="post" action="{{ url('/mata-kuliah/store') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="kode_mk" class="form-label font-weight-bold">Kode Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_mk') is-invalid @enderror" id="kode_mk" name="kode_mk" placeholder="Contoh: MK-TKN-01" autofocus value="{{ old('kode_mk') }}" required>
                        @error('kode_mk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_mk" class="form-label font-weight-bold">Nama Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_mk') is-invalid @enderror" id="nama_mk" name="nama_mk" placeholder="Contoh: Pemrograman Web" value="{{ old('nama_mk') }}" required>
                        @error('nama_mk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="fakultas" class="form-label font-weight-bold">Fakultas <span class="text-danger">*</span></label>
                        <select class="form-select @error('fakultas') is-invalid @enderror" id="fakultas" name="fakultas" required>
                            <option value="" disabled selected>Pilih Fakultas</option>
                            <option value="Fakultas Kedokteran & Ilmu Kesehatan" {{ old('fakultas') == 'Fakultas Kedokteran & Ilmu Kesehatan' ? 'selected' : '' }}>Fakultas Kedokteran & Ilmu Kesehatan</option>
                            <option value="Fakultas Hukum" {{ old('fakultas') == 'Fakultas Hukum' ? 'selected' : '' }}>Fakultas Hukum</option>
                            <option value="Fakultas Ekonomi" {{ old('fakultas') == 'Fakultas Ekonomi' ? 'selected' : '' }}>Fakultas Ekonomi</option>
                            <option value="Fakultas Teknik & Sistem Informasi" {{ old('fakultas') == 'Fakultas Teknik & Sistem Informasi' ? 'selected' : '' }}>Fakultas Teknik & Sistem Informasi</option>
                        </select>
                        @error('fakultas')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label for="prodi" class="form-label font-weight-bold">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select @error('prodi') is-invalid @enderror" id="prodi" name="prodi" required>
                            <option value="" disabled selected>Pilih Program Studi</option>
                        </select>
                        @error('prodi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary float-right">Submit</button>
                </form>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            var prodiMap = {
                'Fakultas Kedokteran & Ilmu Kesehatan': [
                    'Pendidikan Dokter',
                    'Ilmu Keperawatan',
                    'Farmasi'
                ],
                'Fakultas Hukum': [
                    'Ilmu Hukum'
                ],
                'Fakultas Ekonomi': [
                    'Manajemen',
                    'Akuntansi'
                ],
                'Fakultas Teknik & Sistem Informasi': [
                    'Teknik Informatika',
                    'Sistem Informasi',
                    'Teknik Sipil'
                ]
            };

            $(document).ready(function(){
                var oldFakultas = '{{ old('fakultas') }}';
                var oldProdi = '{{ old('prodi') }}';

                function updateProdi(fakultas, selectedProdi) {
                    var prodiSelect = $('#prodi');
                    prodiSelect.html('<option value="" disabled selected>Pilih Program Studi</option>');
                    if (prodiMap[fakultas]) {
                        prodiMap[fakultas].forEach(function(p) {
                            var selected = (p === selectedProdi) ? 'selected' : '';
                            prodiSelect.append('<option value="' + p + '" ' + selected + '>' + p + '</option>');
                        });
                    }
                }

                if (oldFakultas) {
                    updateProdi(oldFakultas, oldProdi);
                }

                $('#fakultas').change(function() {
                    var fak = $(this).val();
                    updateProdi(fak, '');
                });
            });
        </script>
    @endpush
@endsection
