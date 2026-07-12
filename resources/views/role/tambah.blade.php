@extends('templates.dashboard')
@section('isi')
<div class="container-fluid py-4" style="background: #f8fafc; min-height: 100vh;">
    <form method="post" action="{{ url('/role/store') }}" id="roleForm">
        @csrf
        
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ url('/role') }}" class="btn btn-icon me-3" style="border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <i class="fa fa-arrow-left" style="color: #0f172a; font-size: 16px;"></i>
                </a>
                <div>
                    <h3 style="font-weight: 800; color: #0f172a; margin: 0;">Tambah Role</h3>
                    <p class="text-muted mb-0" style="font-size: 13px;">Atur hak akses jabatan baru</p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary d-flex align-items-center" style="border-radius: 12px; font-weight: 700; padding: 12px 28px; background: #4f46e5; border: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">
                <i class="fa fa-save me-2"></i> Simpan
            </button>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 24px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Info Panel -->
            <div class="col-lg-3">
                <div class="card shadow-sm border-0" style="border-radius: 20px; background: #ffffff; padding: 24px;">
                    <h6 class="mb-4" style="font-weight: 800; color: #1e293b; display: flex; align-items: center;">
                        <i class="fa fa-shield-alt text-primary me-2" style="font-size: 14px;"></i> Info Role
                    </h6>
                    
                    <div class="form-group mb-4">
                        <label for="name" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: hrd, finance" required style="border-radius: 12px; padding: 12px 16px; background-color: #f8fafc; border: 1px solid #cbd5e1; font-weight: 600; color: #0f172a;">
                    </div>

                    <div class="form-group">
                        <label for="guard_name" style="font-weight: 700; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Guard</label>
                        <input type="text" class="form-control" id="guard_name" name="guard_name" value="web" readonly style="border-radius: 12px; padding: 12px 16px; background-color: #f1f5f9; border: 1px solid #cbd5e1; font-weight: 600; color: #64748b;">
                    </div>
                </div>
            </div>

            <!-- Right Permissions Panel -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0" style="border-radius: 20px; background: #ffffff; padding: 24px;">
                    <h6 class="mb-4" style="font-weight: 800; color: #1e293b; display: flex; align-items: center;">
                        <i class="fa fa-key text-primary me-2" style="font-size: 14px;"></i> Hak Akses
                    </h6>

                    <div class="row g-3">
                        @foreach($permissions as $category => $items)
                            <div class="col-md-4">
                                <div class="card p-3 h-100" style="border: 1px solid #f1f5f9; border-radius: 16px; background-color: #ffffff; transition: border-color 0.2s;">
                                    <!-- Category Title & Check All -->
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-3" style="border-bottom: 1px solid #f1f5f9;">
                                        <div class="form-check">
                                            <input class="form-check-input select-all-category" type="checkbox" id="cat_{{ $category }}" data-target-class="chk_{{ $category }}">
                                            <label class="form-check-label ms-1" for="cat_{{ $category }}" style="font-weight: 800; font-size: 12px; color: #1e293b; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;">
                                                {{ $category }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Permissions list -->
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($items as $permission)
                                            @php
                                                // Extract cleaner label (e.g. "absen.create" -> "create")
                                                $parts = explode('.', $permission->name);
                                                $actionLabel = isset($parts[1]) ? $parts[1] : $permission->name;
                                            @endphp
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox chk_{{ $category }}" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                                <label class="form-check-label ms-1" for="perm_{{ $permission->id }}" style="font-size: 13px; font-weight: 500; color: #64748b; cursor: pointer;">
                                                    {{ $actionLabel }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle all checkboxes under a category card
        document.querySelectorAll('.select-all-category').forEach(function (selectAll) {
            selectAll.addEventListener('change', function () {
                var targetClass = this.getAttribute('data-target-class');
                var checkboxes = document.querySelectorAll('.' + targetClass);
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });
        });

        // Update Select All checkbox state if sub-checkboxes are changed
        document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var classList = this.className.split(' ');
                var targetClass = classList.find(c => c.startsWith('chk_'));
                if (targetClass) {
                    var categoryId = 'cat_' + targetClass.replace('chk_', '');
                    var selectAll = document.getElementById(categoryId);
                    if (selectAll) {
                        var total = document.querySelectorAll('.' + targetClass).length;
                        var checked = document.querySelectorAll('.' + targetClass + ':checked').length;
                        selectAll.checked = (total === checked);
                        selectAll.indeterminate = (checked > 0 && checked < total);
                    }
                }
            });
        });
    });
</script>
@endsection
