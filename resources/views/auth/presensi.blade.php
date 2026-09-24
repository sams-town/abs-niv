@extends('templates.login')
@section('container')
@push('style')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

    * { box-sizing: border-box; }
    html, body {
        height: 100%; width: 100%;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #0a0f23;
        overflow: hidden;
    }
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        min-height: 100dvh;
    }
    .login-section, .tf-container, .mt-7 {
        width: 100% !important; padding: 0 !important; margin: 0 !important;
    }

    /* Camera Container */
    #cam-wrapper {
        position: relative;
        width: 100%;
        max-width: 440px;
        margin: 0 12px;
        border-radius: 28px;
        overflow: hidden;
        background: #0a0f23;
        box-shadow: 0 32px 80px rgba(0,0,0,0.6);
        border: 1px solid rgba(255,255,255,0.08);
    }

    /* Header */
    #cam-header {
        position: absolute;
        top: 0; left: 0; right: 0;
        z-index: 30;
        padding: 16px 20px 14px;
        background: linear-gradient(to bottom, rgba(10,15,35,0.95) 0%, transparent 100%);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    #back-btn {
        width: 38px; height: 38px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
        color: white;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    #back-btn:hover { background: rgba(255,255,255,0.2); color: white; }
    #cam-title-wrap { flex: 1; }
    #cam-title-wrap h2 {
        font-size: 16px; font-weight: 800;
        color: #fff; margin: 0; line-height: 1.2;
    }
    #cam-title-wrap p {
        font-size: 11px; color: rgba(255,255,255,0.5);
        margin: 0; margin-top: 2px;
    }
    #status-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #6b7280;
        box-shadow: 0 0 0 3px rgba(107,114,128,0.2);
        transition: all 0.4s;
        flex-shrink: 0;
    }
    #status-dot.loading { background: #f59e0b; box-shadow: 0 0 0 4px rgba(245,158,11,0.25); animation: pulse 1s infinite; }
    #status-dot.scanning { background: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,0.3); animation: pulse 0.7s infinite; }
    #status-dot.success  { background: #22c55e; box-shadow: 0 0 12px rgba(34,197,94,0.6); animation: none; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.5} }

    /* Video */
    #video {
        display: block;
        width: 100%;
        min-height: 320px;
        max-height: 60vh;
        object-fit: cover;
        background: #0a0f23;
    }
    @media (max-width: 480px) {
        #video { min-height: 280px; max-height: 55vh; }
    }

    /* Canvas overlay */
    #overlay-canvas {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        pointer-events: none;
        z-index: 10;
    }

    /* Scan frame */
    #scan-frame {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 180px; height: 180px;
        z-index: 20;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.5s;
    }
    #scan-frame.show { opacity: 1; }
    #scan-frame .corner {
        position: absolute;
        width: 24px; height: 24px;
        border-color: #10b981;
        border-style: solid;
    }
    #scan-frame .tl { top:0; left:0; border-width: 3px 0 0 3px; border-radius: 6px 0 0 0; }
    #scan-frame .tr { top:0; right:0; border-width: 3px 3px 0 0; border-radius: 0 6px 0 0; }
    #scan-frame .bl { bottom:0; left:0; border-width: 0 0 3px 3px; border-radius: 0 0 0 6px; }
    #scan-frame .br { bottom:0; right:0; border-width: 0 3px 3px 0; border-radius: 0 0 6px 0; }
    .scan-line {
        position: absolute;
        left: 3px; right: 3px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #10b981, transparent);
        top: 3px;
        animation: scanDown 2s ease-in-out infinite;
        border-radius: 2px;
    }
    @keyframes scanDown {
        0% { top: 3px; opacity: 1; }
        100% { top: calc(100% - 5px); opacity: 0.5; }
    }

    /* Status Bar */
    #status-bar {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        z-index: 30;
        padding: 16px 20px 20px;
        background: linear-gradient(to top, rgba(10,15,35,0.98) 0%, transparent 100%);
    }
    #status-label {
        font-size: 13px;
        font-weight: 600;
        color: rgba(255,255,255,0.7);
        text-align: center;
        min-height: 20px;
        transition: all 0.3s;
    }

    /* Progress bar */
    #progress-wrap {
        margin-top: 10px;
        background: rgba(255,255,255,0.08);
        border-radius: 100px;
        height: 4px;
        overflow: hidden;
    }
    #progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #6366f1, #10b981);
        border-radius: 100px;
        transition: width 0.5s ease;
    }

    /* Face matched overlay */
    #matched-overlay {
        position: absolute;
        inset: 0;
        z-index: 40;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(16,185,129,0.15);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
        backdrop-filter: blur(2px);
    }
    #matched-overlay.show { opacity: 1; }
    #matched-overlay .check {
        width: 72px; height: 72px;
        background: #10b981;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes popIn {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

<div id="cam-wrapper">
    <!-- Header -->
    <div id="cam-header">
        <a href="{{ url('/') }}" id="back-btn">
            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div id="cam-title-wrap">
            <h2>Absen Masuk</h2>
            <p>Arahkan wajah ke kamera</p>
        </div>
        <div id="status-dot" class="loading"></div>
    </div>

    <!-- Video -->
    <video id="video" autoplay playsinline muted></video>
    <canvas id="overlay-canvas"></canvas>

    <!-- Scan Frame -->
    <div id="scan-frame">
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>
        <div class="scan-line"></div>
    </div>

    <!-- Matched Overlay -->
    <div id="matched-overlay">
        <div class="check">
            <svg width="36" height="36" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
    </div>

    <!-- Status Bar -->
    <div id="status-bar">
        <div id="status-label">Memuat model pengenalan wajah...</div>
        <div id="progress-wrap"><div id="progress-bar"></div></div>
    </div>
</div>

<input type="hidden" id="lat">
<input type="hidden" id="long">

@push('script')
<script src="{{ url('/face/dist/face-api.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ─────────────────────────────────────────────────────────
    // SECURITY CONSTANTS
    // ─────────────────────────────────────────────────────────
    const MATCH_THRESHOLD   = 0.40;   // max euclidean distance (0.40 ≈ 85% confidence)
    const CONFIRM_FRAMES    = 3;      // consecutive matching frames before submitting
    const EXPECTED_USERNAME = "{{ Auth::check() ? Auth::user()->username : '' }}";
    const CSRF_TOKEN        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ─────────────────────────────────────────────────────────
    // DOM REFS
    // ─────────────────────────────────────────────────────────
    const video          = document.getElementById('video');
    const canvas         = document.getElementById('overlay-canvas');
    const ctx            = canvas.getContext('2d');
    const dot            = document.getElementById('status-dot');
    const label          = document.getElementById('status-label');
    const progress       = document.getElementById('progress-bar');
    const frame          = document.getElementById('scan-frame');
    const matchedOverlay = document.getElementById('matched-overlay');

    // ─────────────────────────────────────────────────────────
    // STATE
    // ─────────────────────────────────────────────────────────
    let userDescriptors  = null;    // Float32Array[] – ONLY logged-in user's vectors
    let isSubmitting     = false;
    let detectionActive  = false;
    let isDetecting      = false;
    let confirmedCount   = 0;
    let retryTimer       = null;

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────
    function setProgress(p) { progress.style.width = p + '%'; }
    function setLabel(t, color) {
        label.textContent = t;
        label.style.color = color || 'rgba(255,255,255,0.7)';
    }

    // ─────────────────────────────────────────────────────────
    // NETWORK MONITOR
    // ─────────────────────────────────────────────────────────
    window.addEventListener('offline', () => {
        setLabel('⚠️ Koneksi terputus. Menunggu jaringan...', '#fbbf24');
        dot.className   = 'loading';
        detectionActive = false;
    });
    window.addEventListener('online', () => {
        if (userDescriptors) {
            detectionActive = true;
            confirmedCount  = 0;
            dot.className   = 'scanning';
            setLabel('✅ Koneksi kembali. Siap scan...');
            detectLoop();
        } else {
            init();
        }
    });

    // ─────────────────────────────────────────────────────────
    // GEOLOCATION – non-blocking, refreshed every 8 s
    // ─────────────────────────────────────────────────────────
    function getLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(p => {
            document.getElementById('lat').value  = p.coords.latitude;
            document.getElementById('long').value = p.coords.longitude;
        }, () => {}, { enableHighAccuracy: false, timeout: 8000 });
    }
    getLocation();
    setInterval(getLocation, 8000);

    // ─────────────────────────────────────────────────────────
    // OCCLUSION GUARD – landmark-based mask/helmet detection
    // ─────────────────────────────────────────────────────────
    function isFaceOccluded(landmarks) {
        try {
            const leftEye  = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            const nose     = landmarks.getNose();
            const mouth    = landmarks.getMouth();
            const pts      = landmarks.positions;

            if (!leftEye?.length || !rightEye?.length || !nose?.length || !mouth?.length) return true;

            const avgY = arr => arr.reduce((s, p) => s + p.y, 0) / arr.length;
            const eyeY   = (avgY(leftEye) + avgY(rightEye)) / 2;
            const noseY  = avgY(nose);
            const mouthY = avgY(mouth);
            const faceH  = pts[8].y - pts[19].y; // chin to brow

            if (faceH <= 0) return true;

            const eyeToNose   = (noseY  - eyeY)  / faceH;
            const noseToMouth = (mouthY - noseY)  / faceH;

            // If gaps are too small → face regions collapsed (mask/helmet)
            return (eyeToNose < 0.05 || noseToMouth < 0.05);
        } catch (e) {
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────
    // CAMERA
    // ─────────────────────────────────────────────────────────
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = stream;
            return true;
        } catch (e) {
            setLabel('❌ Tidak bisa akses kamera. Izinkan akses kamera di browser.', '#f87171');
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────
    // LOAD MODELS (async, parallel with camera start)
    // ─────────────────────────────────────────────────────────
    async function loadModels() {
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
            ]);
        } catch(e) {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceLandmark68Net.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // INIT – parallel camera + model load, then fetch only
    //        the current user's descriptor (1-to-1 mode)
    // ─────────────────────────────────────────────────────────
    async function init() {
        if (!navigator.onLine) {
            setLabel('⚠️ Tidak ada koneksi internet. Hubungkan jaringan.', '#fbbf24');
            return;
        }

        setLabel('Memuat model AI...'); setProgress(10); dot.className = 'loading';

        // Start camera and load models in parallel
        const [camOk] = await Promise.all([startCamera(), loadModels()]);
        if (!camOk) return;

        setProgress(55);
        setLabel('Mengambil data wajah Anda...');

        // ── CRITICAL: Fetch only the logged-in user's neural data ──
        try {
            const resp = await fetch("{{ url('/ajaxGetNeural') }}", {
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const raw = await resp.text();

            if (!raw || raw.length < 3) {
                setLabel('❌ Wajah Anda belum terdaftar. Hubungi admin.', '#f87171'); return;
            }

            const content = JSON.parse('{"parent":' + raw + '}');
            // Find ONLY this user's record — strict 1-to-1 identity lock
            const myEntry = content.parent.find(c => c.label === EXPECTED_USERNAME);

            if (!myEntry || !myEntry.descriptors?.length) {
                setLabel('❌ Data wajah akun ini tidak ditemukan. Hubungi admin.', '#f87171'); return;
            }

            // Store ONLY this user's descriptors
            userDescriptors = myEntry.descriptors.map(d => new Float32Array(Object.values(d)));

        } catch (e) {
            setLabel('❌ Gagal memuat data wajah: ' + (e.message || 'timeout'), '#f87171');
            // Auto-retry in 5 s
            clearTimeout(retryTimer);
            retryTimer = setTimeout(init, 5000);
            return;
        }

        setProgress(100);
        setLabel('✅ Siap! Arahkan wajah Anda ke kamera...');
        dot.className = 'scanning';
        frame.classList.add('show');
        setTimeout(() => setProgress(0), 800);

        video.addEventListener('loadedmetadata', () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
        });
        detectionActive = true;
        detectLoop();
    }

    // ─────────────────────────────────────────────────────────
    // DETECTION LOOP
    // ─────────────────────────────────────────────────────────
    async function detectLoop() {
        if (!detectionActive || isSubmitting) { setTimeout(detectLoop, 300); return; }
        if (isDetecting) { setTimeout(detectLoop, 100); return; }
        if (!video.videoWidth) { setTimeout(detectLoop, 300); return; }

        isDetecting   = true;
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;

        let detections;
        try {
            const useTiny = !!faceapi.nets.tinyFaceDetector.params;
            const opts    = useTiny
                ? new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                : new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 });

            detections = await faceapi.detectAllFaces(video, opts)
                .withFaceLandmarks(useTiny)
                .withFaceDescriptors();
        } catch(e) {
            isDetecting = false;
            setTimeout(detectLoop, 500);
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // ── Guard 1: No face ──
        if (!detections || detections.length === 0) {
            confirmedCount = 0;
            setLabel('Wajah tidak terdeteksi. Pastikan pencahayaan cukup...');
            dot.className  = 'scanning';

        // ── Guard 2: Multiple faces – STRICT REJECT ──
        } else if (detections.length > 1) {
            confirmedCount = 0;
            setLabel('⚠️ Pastikan hanya ada 1 wajah dalam frame kamera.', '#fbbf24');
            dot.className  = 'scanning';
            const resAll = faceapi.resizeResults(detections, { width: canvas.width, height: canvas.height });
            resAll.forEach(d => {
                ctx.strokeStyle = '#ef4444'; ctx.lineWidth = 2.5;
                ctx.strokeRect(d.detection.box.x, d.detection.box.y, d.detection.box.width, d.detection.box.height);
            });

        } else {
            const det       = detections[0];
            const resized   = faceapi.resizeResults([det], { width: canvas.width, height: canvas.height })[0];
            const box       = resized.detection.box;
            const landmarks = resized.landmarks;

            // ── Guard 3: Occlusion (mask/helmet/sunglasses) ──
            if (isFaceOccluded(landmarks)) {
                confirmedCount = 0;
                setLabel('⚠️ Wajah terhalang (masker/helm/kacamata). Lepaskan penutup wajah.', '#fbbf24');
                dot.className = 'scanning';
                ctx.strokeStyle = '#f59e0b'; ctx.lineWidth = 2.5;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
                isDetecting = false;
                setTimeout(detectLoop, 400);
                return;
            }

            // ── Guard 4: 1-to-1 strict identity verification ──
            if (!userDescriptors || !EXPECTED_USERNAME) {
                setLabel('❌ Sesi tidak valid. Silakan login ulang.', '#f87171');
                isDetecting = false; return;
            }

            // Compute minimum euclidean distance across all stored samples
            let minDistance = Infinity;
            for (const stored of userDescriptors) {
                const dist = faceapi.euclideanDistance(det.descriptor, stored);
                if (dist < minDistance) minDistance = dist;
            }

            const isMatch = minDistance <= MATCH_THRESHOLD;
            const confPct = Math.round((1 - minDistance) * 100);

            // Draw bounding box
            ctx.strokeStyle = isMatch ? '#10b981' : '#ef4444';
            ctx.lineWidth   = 2.5;
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Confidence label
            ctx.fillStyle = isMatch ? '#10b981' : '#ef4444';
            ctx.font      = 'bold 12px sans-serif';
            const labelText = isMatch ? `✓ ${confPct}%` : `✗ Tidak cocok`;
            ctx.fillText(labelText, box.x + 4, box.y > 18 ? box.y - 5 : box.y + box.height + 14);

            if (isMatch && !isSubmitting) {
                confirmedCount++;
                setLabel(`Mengenali wajah... (${confirmedCount}/${CONFIRM_FRAMES})`);

                if (confirmedCount >= CONFIRM_FRAMES) {
                    confirmedCount = 0;
                    isDetecting    = false;
                    submitAbsen(EXPECTED_USERNAME);
                    return;
                }
            } else if (!isMatch) {
                confirmedCount = 0;
                setLabel('⚠️ Wajah tidak cocok dengan akun yang sedang login.', '#fbbf24');
            }
        }

        isDetecting = false;
        setTimeout(detectLoop, 400);
    }

    // ─────────────────────────────────────────────────────────
    // SUBMIT with network guard + timeout retry
    // ─────────────────────────────────────────────────────────
    function submitAbsen(username) {
        if (isSubmitting) return;

        // Final offline guard
        if (!navigator.onLine) {
            setLabel('⚠️ Tidak ada koneksi. Absensi ditunda...', '#fbbf24');
            const retry = () => { window.removeEventListener('online', retry); submitAbsen(username); };
            window.addEventListener('online', retry);
            return;
        }

        isSubmitting    = true;
        detectionActive = false;
        dot.className   = 'success';
        matchedOverlay.classList.add('show');
        setLabel('✅ Wajah dikenali! Menyimpan absensi...');
        setProgress(80);

        const cap = document.createElement('canvas');
        cap.width  = 480; cap.height = 480;
        cap.getContext('2d').drawImage(video, 0, 0, 480, 480);
        const imgData = cap.toDataURL('image/jpeg', 0.75);

        const lat  = document.getElementById('lat').value;
        const long = document.getElementById('long').value;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });
        $.ajax({
            type: 'POST', url: "{{ url('/presensi/store') }}",
            data: { username, image: imgData, lat, long },
            timeout: 20000,
            success: function(msg) {
                setProgress(100);
                matchedOverlay.classList.remove('show');
                let text, icon;
                switch (msg) {
                    case 'masuk':       text = '✅ Absen Masuk Berhasil!';              icon = 'success'; break;
                    case 'outlocation': text = '⚠️ Anda di luar radius kantor';          icon = 'warning'; break;
                    case 'selesai':     text = 'ℹ️ Sudah absen masuk hari ini';          icon = 'info';    break;
                    case 'noMs':        text = '⚠️ Shift belum diatur. Hubungi admin';   icon = 'warning'; break;
                    default:            text = '❌ Data pengguna tidak ditemukan';       icon = 'error';
                }
                Swal.fire({ title: text, icon, confirmButtonColor: '#6366f1', timer: 3000, timerProgressBar: true })
                    .then(() => { window.location.href = "{{ url('/') }}"; });
            },
            error: function(xhr, status) {
                isSubmitting    = false;
                detectionActive = true;
                matchedOverlay.classList.remove('show');
                setLabel(status === 'timeout' ? '⚠️ Koneksi lambat. Coba lagi...' : '❌ Gagal menyimpan. Coba lagi...', '#f87171');
                setProgress(0);
                dot.className = 'scanning';
                setTimeout(detectLoop, 500);
            }
        });
    }

    // Boot
    init().catch(e => { setLabel('❌ Error: ' + e.message, '#f87171'); });
</script>
@endpush
@endsection
