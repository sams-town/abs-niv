@extends('templates.login')
@section('container')
    @php
        $settings = App\Models\settings::first();
    @endphp
    <style>
        .login-container-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #f1f5f9;
        }
        .logo-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-header img {
            height: 70px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.06));
        }
        .logo-header h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #0f172a;
            margin-top: 12px;
            font-size: 22px;
            letter-spacing: -0.5px;
        }
        .logo-header p {
            font-size: 14px;
            color: #64748b;
            margin-top: 2px;
        }
        .form-group-custom {
            margin-bottom: 20px;
            position: relative;
            text-align: left;
        }
        .form-group-custom label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }
        .form-group-custom input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }
        .form-group-custom input:focus {
            border-color: #533dea;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(83, 61, 234, 0.1);
            outline: none;
        }
        .btn-login-primary {
            background: linear-gradient(135deg, #533dea 0%, #3b28b5 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(83, 61, 234, 0.2);
        }
        .btn-login-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(83, 61, 234, 0.3);
            color: #ffffff;
        }
        .forgot-link {
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .forgot-link:hover {
            color: #533dea;
        }
        .divider-custom {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0 20px 0;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .divider-custom::before, .divider-custom::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .divider-custom:not(:empty)::before {
            margin-right: .8em;
        }
        .divider-custom:not(:empty)::after {
            margin-left: .8em;
        }
        .absensi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .absensi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 14px;
            text-align: center;
            transition: all 0.2s ease;
        }
        .absensi-card:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.02);
        }
        .absensi-card-title {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-top: 8px;
            margin-bottom: 12px;
        }
        .absensi-card-icon {
            width: 44px;
            height: 44px;
            background: rgba(83, 61, 234, 0.08);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #533dea;
            font-size: 20px;
        }
        .absensi-card.qr .absensi-card-icon {
            background: rgba(16, 185, 129, 0.08);
            color: #10b981;
        }
        .btn-absensi {
            display: block;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            text-align: center;
        }
        .btn-absensi:last-child {
            margin-bottom: 0;
        }
        .btn-absensi.masuk {
            background-color: #533dea;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(83, 61, 234, 0.15);
        }
        .btn-absensi.masuk:hover {
            background-color: #3b28b5;
            color: #ffffff;
        }
        .btn-absensi.pulang {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-absensi.pulang:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
        .btn-absensi.qr-masuk {
            background-color: #10b981;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
        }
        .btn-absensi.qr-masuk:hover {
            background-color: #059669;
            color: #ffffff;
        }
    </style>

    <div class="login-container-card">
        <div class="logo-header">
            <img src="{{ $settings && $settings->logo ? url('/storage/'.$settings->logo) : url('/assets/img/logo.png') }}" alt="Logo">
            <h2>{{ $settings->name ?? 'UNIBA HRIS' }}</h2>
            <p>Sistem Informasi Kehadiran Terintegrasi</p>
        </div>

        <form class="tf-form" action="{{ url('/login-proses') }}" method="POST">
            @csrf
            
            <div class="form-group-custom">
                <label>Username</label>
                <input type="text" placeholder="Masukkan Username" class="@error('username') is-invalid @enderror" value="{{ old('username') }}" name="username" required>
                @error('username')
                  <div class="invalid-feedback" style="display: block; margin-top: 4px;">
                      {{ $message }}
                  </div>
                @enderror
            </div>
            
            <div class="form-group-custom auth-pass-input last">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" class="password-input @error('password') is-invalid @enderror" placeholder="Masukkan Password" name="password" required style="padding-right: 45px;">
                    <a class="icon-eye password-addon" id="password-addon" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;"></a>
                </div>
                @error('password')
                  <div class="invalid-feedback" style="display: block; margin-top: 4px;">
                      {{ $message }}
                  </div>
                @enderror
            </div>

            <button type="submit" class="btn-login-primary mb-3">Masuk ke Akun</button>
            
            <div class="d-flex justify-content-between align-items-center mt-2">
                <a href="{{ url('/forgot-password') }}" class="forgot-link">Lupa Password? <i class="fa fa-key"></i></a>
                <span class="forgot-link">Belum punya akun? <a href="{{ url('/register') }}" style="color: #533dea; font-weight: 700;">Daftar</a></span>
            </div>
        </form>

        <div class="divider-custom">Atau Absensi Cepat</div>

        <div class="absensi-grid">
            <!-- Card Face Recognition -->
            <div class="absensi-card">
                <div class="absensi-card-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="absensi-card-title">Face Recognition</div>
                <a href="{{ url('/presensi') }}" class="btn-absensi masuk">Absen Masuk</a>
                <a href="{{ url('/presensi-pulang') }}" class="btn-absensi pulang">Absen Pulang</a>
            </div>

            <!-- Card QR Code -->
            <div class="absensi-card qr">
                <div class="absensi-card-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="absensi-card-title">QR Code</div>
                <a href="{{ url('/qr-masuk') }}" class="btn-absensi qr-masuk">Absen Masuk</a>
                <a href="{{ url('/qr-pulang') }}" class="btn-absensi pulang">Absen Pulang</a>
            </div>
        </div>
    </div>
@endsection
