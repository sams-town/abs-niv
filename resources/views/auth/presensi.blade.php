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
    // Geolocation
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(p => {
                document.getElementById('lat').value = p.coords.latitude;
                document.getElementById('long').value = p.coords.longitude;
            });
        }
    }
    getLocation();
    setInterval(getLocation, 5000);

    // DOM refs
    const video   = document.getElementById('video');
    const canvas  = document.getElementById('overlay-canvas');
    const ctx     = canvas.getContext('2d');
    const dot     = document.getElementById('status-dot');
    const label   = document.getElementById('status-label');
    const progress = document.getElementById('progress-bar');
    const frame   = document.getElementById('scan-frame');
    const matchedOverlay = document.getElementById('matched-overlay');

    let faceMatcher = null;
    let isSubmitting = false;
    let detectionActive = false;

    function setProgress(p) { progress.style.width = p + '%'; }
    function setLabel(t) { label.textContent = t; }

    // Start camera
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = stream;
        } catch (e) {
            setLabel('❌ Tidak bisa mengakses kamera. Izinkan akses kamera.');
            dot.className = 'status-dot';
        }
    }

    // Load models + face data
    async function init() {
        setLabel('Memuat model AI...');
        setProgress(10);

        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
            ]);
        } catch(e) {
            // Fallback ke model SSD jika tiny tidak ada
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceLandmark68Net.loadFromUri("{{ url('/face/weights') }}"),
                faceapi.nets.faceRecognitionNet.loadFromUri("{{ url('/face/weights') }}")
            ]);
        }

        setProgress(50);
        setLabel('Memuat data wajah pegawai...');

        await startCamera();

        // Fetch neural data
        const resp = await fetch("{{ url('/ajaxGetNeural') }}", {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await resp.text();

        setProgress(80);

        if (data && data.length > 2) {
            try {
                const content = JSON.parse('{"parent":' + data + '}');
                for (let x = 0; x < content.parent.length; x++) {
                    for (let y = 0; y < content.parent[x].descriptors.length; y++) {
                        content.parent[x].descriptors[y] = new Float32Array(Object.values(content.parent[x].descriptors[y]));
                    }
                }
                const labeled = content.parent.map(c => new faceapi.LabeledFaceDescriptors(
                    c.label,
                    c.descriptors.map(d => new Float32Array(d))
                ));
                faceMatcher = new faceapi.FaceMatcher(labeled, 0.5);
            } catch(e) {
                setLabel('⚠️ Gagal memuat data wajah. Coba refresh halaman.');
                return;
            }
        } else {
            setLabel('⚠️ Belum ada data wajah terdaftar. Hubungi admin.');
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

    // Detection loop - fast (500ms interval)
    async function detectLoop() {
        if (!detectionActive || isSubmitting) {
            setTimeout(detectLoop, 300);
            return;
        }
        if (!video.videoWidth) { setTimeout(detectLoop, 300); return; }

        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;

        let detections;
        try {
            const opts = faceapi.nets.tinyFaceDetector.params
                ? new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                : new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 });

            detections = await faceapi.detectAllFaces(video, opts)
                .withFaceLandmarks(faceapi.nets.faceLandmark68TinyNet.params ? true : false)
                .withFaceDescriptors();
        } catch(e) {
            setTimeout(detectLoop, 500);
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!detections || detections.length === 0) {
            setLabel('Wajah tidak terdeteksi. Pastikan pencahayaan cukup...');
            dot.className = 'scanning';
        } else {
            const resized = faceapi.resizeResults(detections, { width: canvas.width, height: canvas.height });

            resized.forEach((det, i) => {
                if (!faceMatcher) return;
                const match = faceMatcher.findBestMatch(det.descriptor);

                // Draw box
                const box = det.detection.box;
                const isKnown = match.label !== 'unknown' && match.distance < 0.5;

                ctx.strokeStyle = isKnown ? '#10b981' : '#f59e0b';
                ctx.lineWidth = 2.5;
                ctx.strokeRect(box.x, box.y, box.width, box.height);

                if (isKnown && !isSubmitting) {
                    submitAbsen(match.label);
                } else {
                    setLabel('Mengenali wajah... ' + (match.label !== 'unknown' ? match.label : ''));
                }
            });
        }

        setTimeout(detectLoop, 400);
    }

    function submitAbsen(username) {
        if (isSubmitting) return;
        isSubmitting = true;
        detectionActive = false;

        dot.className = 'success';
        matchedOverlay.classList.add('show');
        setLabel('✅ Wajah dikenali! Menyimpan absensi...');
        setProgress(80);

        // Capture frame
        const cap = document.createElement('canvas');
        cap.width = 480; cap.height = 480;
        cap.getContext('2d').drawImage(video, 0, 0, 480, 480);
        const imgData = cap.toDataURL('image/jpeg', 0.75);

        const lat  = document.getElementById('lat').value;
        const long = document.getElementById('long').value;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: "{{ url('/presensi/store') }}",
            data: { username, image: imgData, lat, long },
            success: function(msg) {
                setProgress(100);
                matchedOverlay.classList.remove('show');
                let text, icon;
                switch (msg) {
                    case 'masuk':    text = '✅ Absen Masuk Berhasil!'; icon = 'success'; break;
                    case 'outlocation': text = '⚠️ Anda di luar radius kantor'; icon = 'warning'; break;
                    case 'selesai': text = 'ℹ️ Sudah absen masuk hari ini'; icon = 'info'; break;
                    case 'noMs':    text = '⚠️ Shift belum diatur. Hubungi admin'; icon = 'warning'; break;
                    default:        text = '❌ Data pengguna tidak ditemukan'; icon = 'error';
                }
                Swal.fire({ title: text, icon, confirmButtonColor: '#6366f1', timer: 3000, timerProgressBar: true })
                    .then(() => { window.location.href = "{{ url('/') }}"; });
            },
            error: function() {
                isSubmitting = false;
                detectionActive = true;
                matchedOverlay.classList.remove('show');
                setLabel('❌ Gagal menyimpan. Coba lagi...');
                setProgress(0);
                dot.className = 'scanning';
                setTimeout(detectLoop, 500);
            }
        });
    }

    // Start
    init().catch(e => {
        setLabel('❌ Error: ' + e.message);
    });
</script>
@endpush
@endsection
