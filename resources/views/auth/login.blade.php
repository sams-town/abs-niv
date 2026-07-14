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
        }

        body {
            min-height: 100vh !important;
            min-height: 100dvh !important;
            background: linear-gradient(145deg, #0f1535 0%, #1a2060 40%, #1e3a8a 100%) !important;
            position: relative;
            overflow: hidden;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Split Screen Layout for Desktop */
        .login-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            min-height: 100vh;
            position: relative;
            z-index: 5;
        }

        .left-panel {
            flex: 1.1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .right-panel {
            flex: 1.3;
            background: linear-gradient(135deg, rgba(30,58,138,0.2) 0%, rgba(15,23,42,0.5) 100%);
            border-left: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        /* Background animated blur orbs */
        .orb-1 {
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
            top: -150px; left: -150px;
            border-radius: 50%;
            animation: float1 12s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }
        .orb-2 {
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%);
            bottom: -100px; right: 20%;
            border-radius: 50%;
            animation: float2 15s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }
        @keyframes float1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(40px, 40px) scale(1.08); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-30px, -30px) scale(1.08); }
        }

        /* Main Card on Left Panel */
        .login-card {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 28px;
            padding: 40px 36px;
            width: 100%;
            max-width: 410px;
            box-shadow:
                0 32px 64px rgba(0,0,0,0.35),
                0 0 0 1px rgba(255,255,255,0.05) inset;
            position: relative;
            overflow: hidden;
            animation: cardIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            z-index: 10;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
        }

        /* Logo Section */
        .logo-wrap {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo-wrap .logo-img-container {
            width: 84px; height: 84px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            border: 2px solid rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
            padding: 7px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .logo-wrap .logo-img-container:hover { transform: scale(1.05) rotate(2deg); }
        .logo-wrap img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .logo-wrap h1 {
            font-size: 21px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }
        .logo-wrap p {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
        }

        /* Form Controls */
        .field-group {
            margin-bottom: 18px;
        }
        .field-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .field-group .input-wrap {
            position: relative;
        }
        .field-group input {
            width: 100%;
            padding: 13px 18px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            font-size: 14.5px;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.25s ease;
            -webkit-appearance: none;
        }
        .field-group input::placeholder { color: rgba(255,255,255,0.25); }
        .field-group input:focus {
            outline: none;
            border-color: rgba(99,102,241,0.7);
            background: rgba(99,102,241,0.1);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }
        .eye-toggle {
            position: absolute;
            right: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.35);
            cursor: pointer;
            font-size: 15px;
            transition: color 0.2s;
        }
        .eye-toggle:hover { color: rgba(255,255,255,0.75); }

        /* Login Button */
        .btn-masuk {
            width: 100%;
            padding: 14px;
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
            box-shadow: 0 8px 24px rgba(99,102,241,0.3);
            position: relative;
            overflow: hidden;
        }
        .btn-masuk::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
            transition: left 0.5s ease;
        }
        .btn-masuk:hover::before { left: 100%; }
        .btn-masuk:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(99,102,241,0.4);
        }
        .btn-masuk:active { transform: translateY(1px); }

        /* Error */
        .error-msg {
            font-size: 11.5px;
            color: #fca5a5;
            margin-top: 6px;
            padding: 6px 10px;
            background: rgba(239,68,68,0.12);
            border-radius: 8px;
            border-left: 3px solid #ef4444;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 18px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }
        .divider span {
            font-size: 10.5px;
            font-weight: 600;
            color: rgba(255,255,255,0.25);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Absen Grid */
        .absen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .btn-absen {
            padding: 13px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: all 0.25s ease;
            border: 1.5px solid transparent;
            cursor: pointer;
        }
        .btn-absen.masuk {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 6px 16px rgba(16,185,129,0.3);
        }
        .btn-absen.masuk:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(16,185,129,0.4);
            color: white;
        }
        .btn-absen.pulang {
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.75);
            border-color: rgba(255,255,255,0.12);
        }
        .btn-absen.pulang:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.25);
            transform: translateY(-1px);
            color: white;
        }

        /* Welcome content on the right */
        .welcome-content {
            max-width: 500px;
            color: #ffffff;
            text-align: left;
            animation: fadeInRight 0.8s ease both;
            z-index: 10;
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .uni-logo-glow {
            width: 106px; height: 106px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            border: 2.5px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            padding: 10px;
            margin-bottom: 24px;
            box-shadow: 
                0 0 40px rgba(99,102,241,0.22),
                0 0 0 1px rgba(255,255,255,0.05) inset;
        }
        .uni-logo-glow img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .welcome-content h2 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .welcome-content p {
            font-size: 16px;
            color: #818cf8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 32px;
        }
        .bullet-points {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .bullet {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .bullet .icon {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            color: #10b981;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800;
            flex-shrink: 0;
        }
        .bullet span:last-child {
            font-size: 14.5px;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .right-panel {
                display: none !important;
            }
            .left-panel {
                flex: 1;
                padding: 16px;
            }
            .login-wrapper {
                width: 100%;
                height: auto;
                min-height: 100vh;
            }
        }

        @media (max-width: 480px) {
            .left-panel {
                padding: 12px !important;
            }
            .login-card {
                padding: 24px 20px 20px !important;
                border-radius: 20px !important;
            }
            .logo-wrap {
                margin-bottom: 20px !important;
            }
            .logo-wrap .logo-img-container {
                width: 70px !important; height: 70px !important;
                margin-bottom: 10px !important;
                padding: 6px !important;
            }
            .logo-wrap h1 {
                font-size: 19px !important;
            }
            .logo-wrap p {
                font-size: 12px !important;
            }
            .field-group {
                margin-bottom: 12px !important;
            }
            .field-group label {
                font-size: 10.5px !important;
                margin-bottom: 6px !important;
            }
            .field-group input {
                padding: 11px 14px !important;
                font-size: 14px !important;
                border-radius: 12px !important;
            }
            .btn-masuk {
                padding: 12px !important;
                font-size: 14px !important;
                border-radius: 12px !important;
                margin-top: 4px !important;
            }
            .divider {
                margin: 16px 0 12px !important;
            }
            .btn-absen {
                padding: 11px 8px !important;
                font-size: 12px !important;
                border-radius: 12px !important;
            }
        }
    </style>

    <div class="login-wrapper">
        <!-- Floating blur background elements -->
        <div class="orb-1"></div>
        <div class="orb-2"></div>

        <!-- Left Panel: Card Form -->
        <div class="left-panel">
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
        </div>

        <!-- Right Panel: Corporate branding/info -->
        <div class="right-panel">
            <div class="welcome-content">
                <div class="uni-logo-glow">
                    <img src="{{ $logoUrl }}" alt="Logo">
                </div>
                <h2>Selamat Datang di Portal HRIS</h2>
                <p>{{ $settings->name ?? 'UNIBA HRIS' }}</p>
                
                <div class="bullet-points">
                    <div class="bullet">
                        <span class="icon">✓</span>
                        <span>Presensi Kehadiran Online Cepat & Akurat</span>
                    </div>
                    <div class="bullet">
                        <span class="icon">✓</span>
                        <span>Integrasi Slip Gaji & Penggajian Dosen/Pegawai</span>
                    </div>
                    <div class="bullet">
                        <span class="icon">✓</span>
                        <span>Sistem Informasi Kepegawaian Terpadu</span>
                    </div>
                </div>
            </div>
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
