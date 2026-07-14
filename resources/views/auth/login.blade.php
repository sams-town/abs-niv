@extends('templates.login')
@section('container')
    @php
        $settings = App\Models\settings::first();
        $logoUrl = $settings && $settings->logo ? url('/storage/'.$settings->logo) : url('/assets/img/logo.png');
        $logoUrl = $logoUrl . '?v=' . ($settings ? strtotime($settings->updated_at) : time());
        $bgUrl = url('/assets/img/login_bg.png') . '?v=' . ($settings ? strtotime($settings->updated_at) : time());
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
            background-image: url('{{ $bgUrl }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            position: relative;
            overflow: hidden;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Overlay to keep background clean and high-contrast */
        body::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(241, 245, 249, 0.4);
            pointer-events: none;
            z-index: 1;
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

        /* Double Pane Layout Card - Center-Aligned Bulletproof */
        .login-container {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            display: flex;
            width: 860px;
            height: 560px;
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 
                0 30px 60px -15px rgba(15, 23, 42, 0.15),
                0 0 0 1px rgba(15, 23, 42, 0.05);
            z-index: 1000 !important;
            animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translate(-50%, -48%) scale(0.98); }
            to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        /* Left Pane: Login Form */
        .form-pane {
            flex: 1.1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        /* Right Pane: Brand Banner */
        .brand-pane {
            flex: 0.9;
            background: linear-gradient(135deg, #1b358f 0%, #2563eb 100%);
            padding: 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-align: center;
            position: relative;
        }
        .brand-pane::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.06) 1.5px, transparent 1.5px),
                              radial-gradient(circle at 80% 70%, rgba(255,255,255,0.06) 1.5px, transparent 1.5px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        /* Logo Area */
        .logo-wrap {
            margin-bottom: 24px;
        }
        .logo-wrap .logo-img-container {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .logo-wrap img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .form-pane h2 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .form-pane p.subtitle {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 28px;
        }

        /* Brand Pane Content */
        .brand-logo-container {
            width: 110px; height: 110px;
            border-radius: 50%;
            background: #ffffff;
            display: flex; align-items: center; justify-content: center;
            padding: 8px;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 3px solid rgba(255,255,255,0.2);
            animation: bounceLogo 4s ease-in-out infinite alternate;
        }
        @keyframes bounceLogo {
            0% { transform: translateY(0); }
            100% { transform: translateY(-8px); }
        }
        .brand-logo-container img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .brand-pane h2 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            color: #ffffff;
        }
        .brand-pane p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
            max-width: 280px;
        }

        /* Inputs */
        .field-group {
            margin-bottom: 18px;
        }
        .field-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .field-group .input-wrap {
            position: relative;
        }
        .field-group input {
            width: 100%;
            padding: 12px 16px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 14px;
            color: #0f172a;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s ease;
            -webkit-appearance: none;
        }
        .field-group input::placeholder { color: #94a3b8; }
        .field-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .eye-toggle {
            position: absolute;
            right: 16px; top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            cursor: pointer;
            font-size: 16px;
        }
        .eye-toggle:hover { color: #0f172a; }

        /* Login Button */
        .btn-masuk {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 14.5px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            margin-top: 4px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
        .btn-masuk:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.3);
        }
        .btn-masuk:active { transform: translateY(1px); }

        /* Error Message */
        .error-msg {
            font-size: 11px;
            color: #b91c1c;
            margin-top: 6px;
            padding: 6px 10px;
            background: #fee2e2;
            border-radius: 8px;
            border-left: 3px solid #ef4444;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0 16px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .divider span {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        /* Absen Buttons */
        .absen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .btn-absen {
            padding: 12px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .btn-absen.masuk {
            background: #10b981;
            color: #fff;
            box-shadow: 0 4px 12px rgba(16,185,129,0.15);
        }
        .btn-absen.masuk:hover {
            background: #059669;
            box-shadow: 0 6px 16px rgba(16,185,129,0.25);
            color: white;
        }
        .btn-absen.pulang {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-absen.pulang:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* Responsive Breakpoints */
        @media (max-width: 890px) {
            .login-container {
                width: 780px;
                height: 520px;
            }
        }

        @media (max-width: 767px) {
            .login-container {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: calc(100% - 32px) !important;
                max-width: 420px !important;
                height: auto !important;
                margin: 0 !important;
                flex-direction: column;
                border-radius: 20px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            }
            .brand-pane {
                display: none !important;
            }
            .form-pane {
                padding: 36px 24px;
            }
            .logo-wrap {
                display: block !important;
                margin-bottom: 20px;
            }
        }
    </style>

    <!-- Double Pane Layout Container -->
    <div class="login-container">
        <!-- Left Pane: Form -->
        <div class="form-pane">
            <!-- Logo inside form only visible/needed for spacing and layout on mobile -->
            <div class="logo-wrap">
                <div class="logo-img-container">
                    <img src="{{ $logoUrl }}" alt="Logo">
                </div>
            </div>
            
            <h2>Masuk ke Akun</h2>
            <p class="subtitle">Silakan masukkan akun Anda untuk melanjutkan ke dashboard.</p>

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
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Absen Masuk
                </a>
                <a href="{{ url('/presensi-pulang') }}" class="btn-absen pulang">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Absen Pulang
                </a>
            </div>
        </div>

        <!-- Right Pane: Brand Banner -->
        <div class="brand-pane">
            <div class="brand-logo-container">
                <img src="{{ $logoUrl }}" alt="Logo">
            </div>
            <h2>{{ $settings->name ?? 'UNIBA HRIS' }}</h2>
            <p>Sistem Informasi Kehadiran Terintegrasi Dosen & Karyawan Universitas Batam.</p>
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
