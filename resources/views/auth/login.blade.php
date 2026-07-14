@extends('templates.login')
@section('container')
    @php
        $settings = App\Models\settings::first();
        $logoUrl = $settings && $settings->logo ? url('/storage/'.$settings->logo) : url('/assets/img/logo.png');
    @endphp
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #0f1535 0%, #1a2060 40%, #1e3a8a 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        body::before {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%);
            top: -100px; left: -100px;
            border-radius: 50%;
            animation: float1 8s ease-in-out infinite;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(16,185,129,0.2) 0%, transparent 70%);
            bottom: -80px; right: -80px;
            border-radius: 50%;
            animation: float2 10s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes float1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(30px, 30px) scale(1.1); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-20px, -20px) scale(1.1); }
        }

        /* Reset layout from template */
        .login-section { width: 100%; padding: 0; margin: 0; }
        .tf-container { width: 100%; padding: 0; margin: 0; }
        .mt-7 { margin-top: 0 !important; }

        /* Main Card */
        .login-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 28px;
            padding: 40px 36px 36px;
            width: 100%;
            max-width: 420px;
            margin: 16px;
            box-shadow:
                0 32px 64px rgba(0,0,0,0.4),
                0 0 0 1px rgba(255,255,255,0.05) inset;
            position: relative;
            overflow: hidden;
            animation: cardIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        /* Logo Section */
        .logo-wrap {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-wrap .logo-img-container {
            width: 88px; height: 88px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            padding: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .logo-wrap .logo-img-container:hover { transform: scale(1.05) rotate(3deg); }
        .logo-wrap img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .logo-wrap h1 {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }
        .logo-wrap p {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }

        /* Form */
        .field-group {
            margin-bottom: 18px;
        }
        .field-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .field-group .input-wrap {
            position: relative;
        }
        .field-group input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            font-size: 15px;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.25s ease;
            -webkit-appearance: none;
        }
        .field-group input::placeholder { color: rgba(255,255,255,0.3); }
        .field-group input:focus {
            outline: none;
            border-color: rgba(99,102,241,0.8);
            background: rgba(99,102,241,0.12);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.15);
        }
        .eye-toggle {
            position: absolute;
            right: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s;
        }
        .eye-toggle:hover { color: rgba(255,255,255,0.8); }

        /* Login Button */
        .btn-masuk {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
            position: relative;
            overflow: hidden;
        }
        .btn-masuk::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }
        .btn-masuk:hover::before { left: 100%; }
        .btn-masuk:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(99,102,241,0.5);
        }
        .btn-masuk:active { transform: translateY(1px); }

        /* Error */
        .error-msg {
            font-size: 12px;
            color: #fca5a5;
            margin-top: 6px;
            padding: 6px 10px;
            background: rgba(239,68,68,0.15);
            border-radius: 8px;
            border-left: 3px solid #ef4444;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 20px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.12);
        }
        .divider span {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Absen Buttons */
        .absen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .btn-absen {
            padding: 14px 10px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: all 0.3s ease;
            border: 1.5px solid transparent;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .btn-absen.masuk {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 6px 18px rgba(16,185,129,0.35);
        }
        .btn-absen.masuk:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(16,185,129,0.45);
            color: white;
        }
        .btn-absen.pulang {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.8);
            border-color: rgba(255,255,255,0.15);
        }
        .btn-absen.pulang:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            color: white;
        }

        /* Mobile perfect fit */
        @media (max-width: 480px) {
            body {
                align-items: center;
                padding: 0;
            }
            .login-card {
                margin: 12px;
                padding: 32px 24px 28px;
                border-radius: 24px;
                max-width: 100%;
            }
            .logo-wrap h1 { font-size: 20px; }
            .btn-absen { font-size: 12px; padding: 13px 8px; }
        }
    </style>

    <div class="login-card">
        <!-- Logo -->
        <div class="logo-wrap">
            <div class="logo-img-container">
                <img src="{{ $logoUrl }}" alt="Logo">
            </div>
            <h1>{{ $settings->name ?? 'UNIBA HRIS' }}</h1>
            <p>Sistem Informasi Kehadiran Terintegrasi</p>
        </div>

        <!-- Form Login -->
        <form action="{{ url('/login-proses') }}" method="POST">
            @csrf
            <div class="field-group">
                <label>Username</label>
                <div class="input-wrap">
                    <input type="text" name="username" placeholder="Masukkan username" value="{{ old('username') }}" required autocomplete="username">
                </div>
                @error('username')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label>Password</label>
                <div class="input-wrap">
                    <input type="password" id="pw-field" name="password" placeholder="Masukkan password" required autocomplete="current-password" style="padding-right: 46px;">
                    <span class="eye-toggle" onclick="togglePw()" id="eye-icon">👁</span>
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-masuk">Masuk ke Akun</button>
        </form>

        <!-- Absen Quick -->
        <div class="divider"><span>Atau Absen Langsung</span></div>
        <div class="absen-grid">
            <a href="{{ url('/presensi') }}" class="btn-absen masuk">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Absen Masuk
            </a>
            <a href="{{ url('/presensi-pulang') }}" class="btn-absen pulang">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Absen Pulang
            </a>
        </div>
    </div>

    <script>
        function togglePw() {
            const f = document.getElementById('pw-field');
            const i = document.getElementById('eye-icon');
            if (f.type === 'password') {
                f.type = 'text';
                i.textContent = '🙈';
            } else {
                f.type = 'password';
                i.textContent = '👁';
            }
        }
    </script>
@endsection
