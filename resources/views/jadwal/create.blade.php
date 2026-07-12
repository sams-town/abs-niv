@extends('templates.dashboard')
@section('isi')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: none; border-bottom: 1px solid #f1f5f9; padding: 24px;">
                <h4 style="font-weight: 800; color: #0f172a; margin: 0;">{{ $title }}</h4>
                <a href="{{ url('/jadwal') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 600; padding: 6px 16px;">
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

                <form action="{{ url('/jadwal') }}" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="dosen_id" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Dosen Pengampu <span class="text-danger">*</span></label>
                        <select name="dosen_id" id="dosen_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Dosen Pengampu</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="mata_kuliah" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="mata_kuliah" id="mata_kuliah" class="form-control" required placeholder="Contoh: Pemrograman Web" style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                    </div>

                    <div class="form-group mb-4">
                        <label for="nama_kelas" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" required placeholder="Contoh: IF-20-A" style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="waktu_mulai" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" class="form-control" required style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="waktu_selesai" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_selesai" id="waktu_selesai" class="form-control" required style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ url('/jadwal') }}" class="btn btn-light" style="border-radius: 12px; font-weight: 600; padding: 12px 24px;">Batal</a>
                        <button type="submit" class="btn btn-primary" style="border-radius: 12px; font-weight: 600; padding: 12px 24px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize searchable select for Dosen
        makeSelectSearchable(document.getElementById('dosen_id'), 'PILIH DOSEN PENGAMPU', 'fa fa-user-tie');
    });

    // Custom Searchable Dropdown Helper
    function makeSelectSearchable(selectElement, placeholderText, iconClass) {
        if (!selectElement) return;
        
        var existingWrapper = selectElement.parentNode.querySelector('.custom-dropdown');
        if (existingWrapper) {
            existingWrapper.remove();
        }

        selectElement.style.display = 'none';

        var wrapper = document.createElement('div');
        wrapper.className = 'custom-dropdown';

        var selectBox = document.createElement('div');
        selectBox.className = 'custom-dropdown-select';
        
        var iconHtml = iconClass ? `<i class="${iconClass} dropdown-icon" style="color: #94a3b8; margin-right: 12px;"></i>` : '';
        selectBox.innerHTML = `
            <div class="d-flex align-items-center">
                ${iconHtml}
                <span class="custom-dropdown-selected-text" style="font-weight:600; color:#94a3b8; text-transform:uppercase;">${placeholderText}</span>
            </div>
            <i class="fa fa-chevron-down arrow-icon" style="color:#64748b; font-size:12px; transition:transform 0.2s;"></i>
        `;
        selectBox.style.cssText = 'border-radius:12px; padding:12px 16px; font-size:14px; border:1px solid #cbd5e1; background-color:#f8fafc; display:flex; align-items:center; justify-content:between; cursor:pointer;';
        wrapper.appendChild(selectBox);

        var menu = document.createElement('div');
        menu.className = 'custom-dropdown-menu';
        menu.style.cssText = 'position:absolute; top:calc(100% + 6px); left:0; right:0; background:#ffffff; border-radius:16px; border:1px solid #cbd5e1; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); z-index:1000; display:none; overflow:hidden;';
        
        var searchDiv = document.createElement('div');
        searchDiv.style.cssText = 'position:relative; padding:12px; border-bottom:1px solid #e2e8f0;';
        searchDiv.innerHTML = `
            <i class="fa fa-search" style="position:absolute; left:24px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
            <input type="text" placeholder="Ketik untuk mencari..." style="width:100%; border-radius:30px; padding:8px 16px 8px 36px; font-size:13px; border:1px solid #cbd5e1; outline:none;">
        `;
        menu.appendChild(searchDiv);

        var optionsDiv = document.createElement('div');
        optionsDiv.style.cssText = 'max-height:240px; overflow-y:auto; padding:8px 0;';
        
        function populateOptions() {
            optionsDiv.innerHTML = '';
            Array.from(selectElement.options).forEach(function(opt) {
                if (opt.value === "" && !opt.selected) return;
                var optDiv = document.createElement('div');
                optDiv.style.cssText = 'padding:10px 20px; font-size:13px; font-weight:500; color:#334155; cursor:pointer; transition:all 0.15s;';
                if (opt.selected) {
                    optDiv.style.fontWeight = '700';
                    optDiv.style.color = '#4f46e5';
                    optDiv.style.backgroundColor = '#eff6ff';
                }
                optDiv.innerText = opt.text;
                optDiv.setAttribute('data-value', opt.value);
                
                optDiv.addEventListener('click', function(e) {
                    e.stopPropagation();
                    Array.from(selectElement.options).forEach(o => o.selected = false);
                    opt.selected = true;
                    updateSelectedText();
                    menu.style.display = 'none';
                    selectBox.style.borderColor = '#cbd5e1';
                    var event = new Event('change', { bubbles: true });
                    selectElement.dispatchEvent(event);
                });
                
                optDiv.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f1f5f9';
                    this.style.color = '#4f46e5';
                });
                optDiv.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                    this.style.color = '#334155';
                });
                optionsDiv.appendChild(optDiv);
            });
        }

        function updateSelectedText() {
            var selectedOptions = Array.from(selectElement.options).filter(o => o.selected && o.value !== "");
            var textSpan = selectBox.querySelector('.custom-dropdown-selected-text');
            if (selectedOptions.length === 0) {
                textSpan.innerText = placeholderText;
                textSpan.style.color = '#94a3b8';
            } else {
                textSpan.innerText = selectedOptions[0].text;
                textSpan.style.color = '#0f172a';
            }
        }

        menu.appendChild(optionsDiv);
        wrapper.appendChild(menu);
        selectElement.parentNode.insertBefore(wrapper, selectElement);

        selectBox.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            selectBox.style.borderColor = isOpen ? '#cbd5e1' : '#4f46e5';
            if (!isOpen) {
                searchDiv.querySelector('input').focus();
            }
        });

        searchDiv.querySelector('input').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        searchDiv.querySelector('input').addEventListener('input', function() {
            var val = this.value.toLowerCase();
            optionsDiv.querySelectorAll('div').forEach(function(opt) {
                var text = opt.innerText.toLowerCase();
                if (text.indexOf(val) !== -1) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        });

        document.addEventListener('click', function() {
            menu.style.display = 'none';
            selectBox.style.borderColor = '#cbd5e1';
        });

        populateOptions();
        updateSelectedText();
    }
</script>
@endsection
