
@extends('templates.dashboard')

@section('isi')
    <style>
        .import-header {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            border: 1px solid #e2e8f0;
        }
        .import-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            border: 1px solid #e2e8f0;
        }
        .btn-download {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
        }
        .btn-download:hover {
            background: #bfdbfe;
        }
        .btn-import {
            background: #4f46e5;
            color: white;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
        }
        .btn-import:hover {
            background: #4338ca;
        }
        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
            background: #f8fafc;
        }
        .file-upload-area:hover {
            border-color: #93c5fd;
            background: #eff6ff;
        }
        .file-upload-icon {
            font-size: 48px;
            color: #94a3b8;
        }
        .type-selector {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }
        .type-option {
            flex: 1;
            padding: 20px;
            border-radius: 16px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }
        .type-option:hover {
            border-color: #93c5fd;
        }
        .type-option.selected {
            border-color: #4f46e5;
            background: #eff6ff;
        }
        .type-option h5 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .type-option p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
    </style>

    <div class="container-fluid">
        <div class="import-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ url('/pegawai') }}" class="btn btn-light rounded-circle p-2" style="width:40px; height:40px;">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <div>
                        <h3 style="font-weight:700; color:#0f172a; margin:0;">Import Massal</h3>
                        <p style="font-size:13px; color:#64748b; margin:0;">Tambah data pegawai dan dosen secara massal</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="import-card">
            <form action="{{ url('/pegawai/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <h5 style="font-weight:700; color:#0f172a; margin-bottom:16px;">Pilih Tipe User</h5>
                    <div class="type-selector">
                        <div class="type-option selected" onclick="selectType(this, 'pegawai')">
                            <i class="fa fa-user-tie" style="font-size:32px; color:#4f46e5; margin-bottom:8px;"></i>
                            <h5>Pegawai</h5>
                            <p>Import data pegawai kantor</p>
                            <input type="hidden" name="tipe_user" value="pegawai" id="tipe_user_input">
                        </div>
                        <div class="type-option" onclick="selectType(this, 'dosen')">
                            <i class="fa fa-chalkboard-teacher" style="font-size:32px; color:#10b981; margin-bottom:8px;"></i>
                            <h5>Dosen</h5>
                            <p>Import data dosen akademis</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <a href="{{ url('/pegawai/template') }}" id="btn-download-template" class="btn-download btn">
                        <i class="fa fa-download me-2"></i> Download Template Excel
                    </a>
                </div>
                
                <div class="mb-4">
                    <label style="font-size:13px; font-weight:700; text-transform:uppercase; color:#64748b; margin-bottom:8px; display:block;">Pilih File Excel / CSV</label>
                    <div class="file-upload-area" onclick="document.getElementById('file_excel').click()">
                        <i class="fa fa-file-excel file-upload-icon"></i>
                        <h5 style="font-weight:700; color:#475569; margin-top:16px;">Klik untuk upload file</h5>
                        <p style="font-size:13px; color:#94a3b8; margin:8px 0 0;">Format file: .xlsx, .xls, .csv (Max 20MB)</p>
                        <input type="file" name="file_excel" id="file_excel" class="d-none" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                    </div>
                    <p id="selected_file_name" style="font-size:13px; color:#475569; margin-top:12px;"></p>
                </div>
                
                <div class="d-flex gap-3">
                    <a href="{{ url('/pegawai') }}" class="btn btn-light" style="border-radius:12px; padding:10px 24px;">
                        Batal
                    </a>
                    <button type="submit" class="btn-import btn">
                        <i class="fa fa-upload me-2"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectType(element, type) {
            document.querySelectorAll('.type-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('tipe_user_input').value = type;
            
            let downloadBtn = document.getElementById('btn-download-template');
            if(type === 'pegawai') {
                downloadBtn.href = "{{ url('/pegawai/template') }}";
            } else {
                downloadBtn.href = "{{ url('/dosen/template') }}";
            }
        }
        
        function updateFileName(input) {
            if (input.files && input.files[0]) {
                document.getElementById('selected_file_name').innerHTML = 
                    '<i class="fa fa-check-circle text-success me-2"></i>' + 
                    'File terpilih: <strong>' + input.files[0].name + '</strong>';
            }
        }
    </script>
@endsection
