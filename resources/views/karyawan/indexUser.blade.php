@extends('templates.app')
@section('container')
    <div class="card-secton transfer-section">
        <div class="tf-container">
            <div class="tf-balance-box">
                <div class="tf-spacing-16"></div>
                <div class="bill-content">
                    <h3 class="fw_6 text-center">Informasi Profil Pegawai</h3>
                </div>
                <div class="tf-spacing-16"></div>
            </div>
        </div>
    </div>
    
    <div id="app-wrap">
        <div class="bill-content">
            <div class="tf-container">
                <ul class="mt-3 mb-5">
                    @foreach ($data_user as $du)
                        <li class="list-card-invoice tf-topbar d-flex justify-content-between align-items-center">
                            <div class="user-info">
                                @if($du->foto_karyawan == null)
                                    <img src="{{ url('/assets/img/foto_default.jpg') }}" alt="image">
                                @else
                                    <img src="{{ url('/storage/'.$du->foto_karyawan) }}" alt="image">
                                @endif
                            </div>
                            <div class="content-right">
                                <h4>
                                    <a href="{{ url('/pegawai/show/'.$du->id) }}">{{ $du->name }} 
                                        <span class="primary_color">View</span>
                                    </a>
                                </h4>
                                <p>
                                    {{ $du->Jabatan->nama_jabatan ?? '-' }} <br> 
                                    <a href="https://wa.me/{{ $du->whatsapp($du->telepon) }}">{{ $du->telepon }}</a>
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection