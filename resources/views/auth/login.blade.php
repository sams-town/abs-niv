@extends('templates.login')
@section('container')
    @php
        $settings = App\Models\settings::first();
    @endphp
    <style>
        /* Premium Login Page Redesign */
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Outfit', 'Inter', sans-serif;
        }
        .login-section {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .login-container-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 45px 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }
        .login-container-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #533dea, #8a2be2, #ff4d4d);
        }
        .logo-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .logo-header img {
            height: 75px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.06));
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }
        .logo-header img:hover {
            transform: scale(1.05);
        }
        .logo-header h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: #1e293b;
            margin-top: 5px;
            font-size: 26px;
            letter-spacing: -0.5px;
        }
        .logo-header p {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }
        .form-group-custom {
            margin-bottom: 22px;
            position: relative;
            text-align: left;
        }
        .form-group-custom label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-group-custom input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-group-custom input::placeholder {
            color: #94a3b8;
        }
        .form-group-custom input:focus {
            border-color: #533dea;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(83, 61, 234, 0.15);
            outline: none;
            transform: translateY(-1px);
        }
        .btn-login-primary {
            background: linear-gradient(135deg, #533dea 0%, #3b28b5 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 16px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(83, 61, 234, 0.25);
            margin-top: 10px;
        }
        .btn-login-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(83, 61, 234, 0.35);
            background: linear-gradient(135deg, #4b36d8 0%, #322199 100%);
        }
        .btn-login-primary:active {
            transform: translateY(1px);
            box-shadow: 0 5px 10px rgba(83, 61, 234, 0.2);
        }
        .absensi-flex {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px dashed #e2e8f0;
        }
        .btn-absensi {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-absensi.masuk {
            background-color: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            border: 1px solid transparent;
        }
        .btn-absensi.masuk:hover {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
            color: white;
        }
        .btn-absensi.pulang {
            background-color: #ffffff;
            color: #475569;
            border: 1.5px solid #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn-absensi.pulang:hover {
            border-color: #94a3b8;
            color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.05);
            background-color: #f8fafc;
        }
        /* Fix the layout for mobile container */
        .tf-container {
            width: 100%;
        }
        .mt-7 {
            margin-top: 0 !important;
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
        </form>

        <div class="absensi-flex">
            <a href="{{ url('/presensi') }}" class="btn-absensi masuk"><i class="fas fa-sign-in-alt"></i> Absen Masuk</a>
            <a href="{{ url('/presensi-pulang') }}" class="btn-absensi pulang"><i class="fas fa-sign-out-alt"></i> Absen Pulang</a>
        </div>
    </div>
@endsection
