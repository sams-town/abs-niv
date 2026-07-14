@extends('templates.login')
@section('container')
    @php
        $settings = App\Models\settings::first();
        $logoUrl = $settings && $settings->logo ? url('/storage/'.$settings->logo) : url('/assets/img/logo.png');
        $logoUrl = $logoUrl . '?v=' . ($settings ? strtotime($settings->updated_at) : time());
    @endphp
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100% !important;
            width: 100% !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #090d16 !important;
        }

        body {
            min-height: 100vh !important;
            min-height: 100dvh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative;
            overflow: hidden;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Beautiful glowing background mesh */
        .bg-glow-1 {
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 70%);
            top: -200px; left: -100px;
            border-radius: 50%;
            pointer-events: none;
            animation: pulseGlow 10s ease-in-out infinite alternate;
        }
        .bg-glow-2 {
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%);
            bottom: -150px; right: -50px;
            border-radius: 50%;
            pointer-events: none;
            animation: pulseGlow 12s ease-in-out infinite alternate-reverse;
        }
        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.8; }
            100% { transform: scale(1.1) translate(30px, 20px); opacity: 1; }
        }

        /* Overrides to force template centering */
        .login-section {
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: transparent !important;
        }
        .tf-container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: transparent !important;
        }
        .mt-7 { margin-top: 0 !important; }

        /* Sleek Centered Login Card */
        .login-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 44px 40px;
            width: 100%;
            max-width: 450px;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.03) inset;
            position: relative;
            z-index: 10;
            animation: cardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
        }

        /* Logo Area */
        .logo-wrap {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-wrap .logo-img-container {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            border: 2px solid rgba(255, 255, 255, 0.12);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            padding: 6px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .logo-wrap img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .logo-wrap h1 {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .logo-wrap p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.45);
            font-weight: 500;
        }

        /* Inputs */
        .field-group {
            margin-bottom: 20px;
        }
        .field-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .field-group .input-wrap {
            position: relative;
        }
        .field-group input {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            font-size: 14.5px;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s ease;
            -webkit-appearance: none;
        }
        .field-group input::placeholder { color: rgba(255, 255, 255, 0.25); }
        .field-group input:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 0.8);
            background: rgba(99, 102, 241, 0.05);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .eye-toggle {
            position: absolute;
            right: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.35);
            cursor: pointer;
            font-size: 16px;
        }
        .eye-toggle:hover { color: rgba(255, 255, 255, 0.7); }

        /* Login Button */
        .btn-masuk {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            margin-top: 6px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(99,102,241,0.25);
        }
        .btn-masuk:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
        }
        .btn-masuk:active { transform: translateY(1px); }

        /* Error Message */
        .error-msg {
            font-size: 11.5px;
            color: #fca5a5;
            margin-top: 6px;
            padding: 6px 10px;
            background: rgba(239, 68, 68, 0.1);
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
            background: rgba(255, 255, 255, 0.08);
        }
        .divider span {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Absen Buttons */
        .absen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .btn-absen {
            padding: 14px 12px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .btn-absen.masuk {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 4px 14px rgba(16,185,129,0.25);
        }
        .btn-absen.masuk:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.35);
            color: white;
        }
        .btn-absen.pulang {
            background: rgba(255, 255, 255, 0.03);
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .btn-absen.pulang:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            body {
                padding: 16px !important;
            }
            .login-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
            .logo-wrap h1 {
                font-size: 21px;
            }
            .absen-grid {
                gap: 10px;
            }
            .btn-absen {
                font-size: 12px;
                padding: 12px 8px;
            }
        }
    </style>

    <!-- Glowing Backgrounds -->
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

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
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Absen Masuk
            </a>
            <a href="{{ url('/presensi-pulang') }}" class="btn-absen pulang">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
