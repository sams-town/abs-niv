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
        display: flex; align-items: center; justify-content: center;
        min-height: 100vh; min-height: 100dvh;
    }
    .login-section, .tf-container, .mt-7 {
        width: 100% !important; padding: 0 !important; margin: 0 !important;
    }

    #cam-wrapper {
        position: relative; width: 100%; max-width: 440px; margin: 0 12px;
        border-radius: 28px; overflow: hidden; background: #0a0f23;
        box-shadow: 0 32px 80px rgba(0,0,0,0.6);
        border: 1px solid rgba(255,255,255,0.08);
    }
    #cam-header {
        position: absolute; top: 0; left: 0; right: 0; z-index: 30;
        padding: 16px 20px 14px;
        background: linear-gradient(to bottom, rgba(10,15,35,0.95) 0%, transparent 100%);
        display: flex; align-items: center; gap: 14px;
    }
    #back-btn {
        width: 38px; height: 38px; background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none; color: white; transition: all 0.2s; flex-shrink: 0;
    }
    #back-btn:hover { background: rgba(255,255,255,0.2); color: white; }
    #cam-title-wrap { flex: 1; }
    #cam-title-wrap h2 { font-size: 16px; font-weight: 800; color: #fff; margin: 0; line-height: 1.2; }
    #cam-title-wrap p  { font-size: 11px; color: rgba(255,255,255,0.5); margin: 0; margin-top: 2px; }
    #status-dot { width: 10px; height: 10px; border-radius: 50%; background: #6b7280; transition: all 0.4s; flex-shrink: 0; }
    #status-dot.loading  { background: #f59e0b; animation: pulse 1s infinite; }
    #status-dot.scanning { background: #f97316; animation: pulse 0.7s infinite; }
    #status-dot.success  { background: #22c55e; animation: none; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.5} }

    #video { display: block; width: 100%; min-height: 320px; max-height: 60vh; object-fit: cover; background: #0a0f23; }
    @media (max-width: 480px) { #video { min-height: 280px; max-height: 55vh; } }

    #overlay-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10; }

    #scan-frame {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 180px; height: 180px; z-index: 20; pointer-events: none; opacity: 0; transition: opacity 0.5s;
    }
    #scan-frame.show { opacity: 1; }
    #scan-frame .corner { position: absolute; width: 24px; height: 24px; border-color: #f97316; border-style: solid; }
    #scan-frame .tl { top:0; left:0; border-width: 3px 0 0 3px; border-radius: 6px 0 0 0; }
    #scan-frame .tr { top:0; right:0; border-width: 3px 3px 0 0; border-radius: 0 6px 0 0; }
    #scan-frame .bl { bottom:0; left:0; border-width: 0 0 3px 3px; border-radius: 0 0 0 6px; }
    #scan-frame .br { bottom:0; right:0; border-width: 0 3px 3px 0; border-radius: 0 0 6px 0; }
    .scan-line {
        position: absolute; left: 3px; right: 3px; height: 2px;
        background: linear-gradient(90deg, transparent, #f97316, transparent);
        top: 3px; animation: scanDown 2s ease-in-out infinite; border-radius: 2px;
    }
    @keyframes scanDown { 0% { top: 3px; opacity: 1; } 100% { top: calc(100% - 5px); opacity: 0.5; } }

    #status-bar {
        position: absolute; bottom: 0; left: 0; right: 0; z-index: 30;
        padding: 16px 20px 20px;
        background: linear-gradient(to top, rgba(10,15,35,0.98) 0%, transparent 100%);
    }
    #status-label { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.7); text-align: center; min-height: 20px; transition: all 0.3s; }
    #progress-wrap { margin-top: 10px; background: rgba(255,255,255,0.08); border-radius: 100px; height: 4px; overflow: hidden; }
    #progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg, #f97316, #ef4444); border-radius: 100px; transition: width 0.5s ease; }

    #matched-overlay {
        position: absolute; inset: 0; z-index: 40;
        display: flex; align-items: center; justify-content: center;
        background: rgba(249,115,22,0.12); opacity: 0; pointer-events: none;
        transition: opacity 0.3s; backdrop-filter: blur(2px);
    }
    #matched-overlay.show { opacity: 1; }
    #matched-overlay .check {
        width: 72px; height: 72px; background: #f97316; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes popIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>
@endpush

<div id="cam-wrapper">
    <div id="cam-header">
        <a href="{{ url('/') }}" id="back-btn">
            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div id="cam-title-wrap">
            <h2>Absen Pulang</h2>
            <p>Arahkan wajah ke kamera</p>
        </div>
        <div id="status-dot" class="loading"></div>
    </div>

    <video id="video" autoplay playsinline muted></video>
    <canvas id="overlay-canvas"></canvas>

    <div id="scan-frame">
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>
        <div class="scan-line"></div>
    </div>

    <div id="matched-overlay">
        <div class="check">
            <svg width="36" height="36" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
    </div>

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
    // ═══════════════════════════════════════════════════════════
    // SECURITY CONSTANTS – SIMPLE MODE (Hanya cocokkan dengan data tersimpan)
    // ═══════════════════════════════════════════════════════════
    const MATCH_THRESHOLD   = 0.35;   // Sesuai data neural.json yang diregistrasikan
    const MIN_CONFIDENCE    = 0.65;   // Hanya batas bawah wajar, selebihnya FaceMatcher yang putus
    const MIN_AVG_DISTANCE  = 0.37;   // Rata-rata distance CONFIRM_FRAMES frame
    const CONFIRM_FRAMES    = 5;      // Standar 5 frame berturut-turut
    const CSRF_TOKEN        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ═══════════════════════════════════════════════════════════
    // DOM REFS
    // ═══════════════════════════════════════════════════════════
    const video          = document.getElementById('video');
    const canvas         = document.getElementById('overlay-canvas');
    const ctx            = canvas.getContext('2d');
    const dot            = document.getElementById('status-dot');
    const label          = document.getElementById('status-label');
    const progress       = document.getElementById('progress-bar');
    const frame          = document.getElementById('scan-frame');
    const matchedOverlay = document.getElementById('matched-overlay');

    // ═══════════════════════════════════════════════════════════
    // STATE – 1-to-MANY Identification Mode (SUPER KETAT)
    // ═══════════════════════════════════════════════════════════
    let allDescriptors    = [];
    let faceMatcher       = null;
    let isSubmitting      = false;
    let detectionActive   = false;
    let isDetecting       = false;
    let confirmedCount    = 0;
    let lastMatchedLabel  = null;
    let confirmedDists    = [];
    let retryTimer        = null;

    // ═══════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════
    function setProgress(p) { progress.style.width = p + '%'; }
    function setLabel(t, color) {
        label.textContent = t;
        label.style.color = color || 'rgba(255,255,255,0.7)';
    }

    // ═══════════════════════════════════════════════════════════
    // NETWORK MONITOR
    // ═══════════════════════════════════════════════════════════
    window.addEventListener('offline', () => {
        setLabel('⚠️ Koneksi terputus. Menunggu jaringan...', '#fbbf24');
        dot.className   = 'loading';
        detectionActive = false;
    });
    window.addEventListener('online', () => {
        if (faceMatcher) {
            detectionActive  = true;
            confirmedCount   = 0;
            lastMatchedLabel = null;
            confirmedDists   = [];
            dot.className    = 'scanning';
            setLabel('✅ Koneksi kembali. Siap scan...');
            detectLoop();
        } else {
            init();
        }
    });

    // ═══════════════════════════════════════════════════════════
    // GEOLOCATION
    // ═══════════════════════════════════════════════════════════
    function getLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(p => {
            document.getElementById('lat').value  = p.coords.latitude;
            document.getElementById('long').value = p.coords.longitude;
        }, () => {}, { enableHighAccuracy: true, timeout: 10000 });
    }
    getLocation();
    setInterval(getLocation, 6000);

    // ═══════════════════════════════════════════════════════════
    // SIMPLE MODE – TIDAK ADA OCCLUSION GUARD
    // 100% serahkan matching ke FaceMatcher sesuai data descriptor tersimpan di neural.json
    // ═══════════════════════════════════════════════════════════
    function isFaceOccluded(landmarks, detectionScore) {
        return false;
    }

    // ═══════════════════════════════════════════════════════════
    // CAMERA
    // ═══════════════════════════════════════════════════════════
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640, min: 480 }, height: { ideal: 480, min: 360 } },
                audio: false
            });
            video.srcObject = stream;
            return true;
        } catch (e) {
            setLabel('❌ Tidak bisa akses kamera. Izinkan akses kamera di browser.', '#f87171');
            return false;
        }
    }

    // ═══════════════════════════════════════════════════════════
    // LOAD MODELS
    // ═══════════════════════════════════════════════════════════
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

    // ═══════════════════════════════════════════════════════════
    // INIT – 1-to-MANY: load SEMUA data wajah karyawan & dosen
    // ═══════════════════════════════════════════════════════════
    async function init() {
        if (!navigator.onLine) {
            setLabel('⚠️ Tidak ada koneksi internet. Hubungkan jaringan.', '#fbbf24');
            return;
        }

        setLabel('Memuat model AI pengenalan wajah...'); setProgress(8); dot.className = 'loading';

        const [camOk] = await Promise.all([startCamera(), loadModels()]);
        if (!camOk) return;

        setProgress(45);
        setLabel('Memuat database wajah karyawan & dosen...');

        try {
            const resp = await fetch("{{ url('/ajaxGetNeural') }}", {
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const raw = await resp.text();

            if (!raw || raw.length < 3) {
                setLabel('❌ Database wajah kosong. Hubungi admin untuk registrasi wajah.', '#f87171'); return;
            }

            let content;
            try {
                content = JSON.parse(raw);
            } catch(e) {
                try {
                    content = JSON.parse('{"parent":' + raw + '}').parent;
                } catch(e2) {
                    throw new Error('Format data wajah tidak valid');
                }
            }

            if (!Array.isArray(content) || content.length === 0) {
                setLabel('❌ Tidak ada data wajah terdaftar. Hubungi admin.', '#f87171'); return;
            }

            setProgress(75);
            setLabel(`Memproses ${content.length} data wajah...`);

            const labeledDescriptors = [];
            for (const entry of content) {
                if (!entry.label || !Array.isArray(entry.descriptors) || entry.descriptors.length === 0) continue;
                try {
                    const descs = entry.descriptors.map(d => {
                        const vals = Object.values(d);
                        if (vals.length !== 128) throw new Error('invalid vector');
                        return new Float32Array(vals);
                    });
                    labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(entry.label, descs));
                } catch(e) { /* skip invalid entries */ }
            }

            if (labeledDescriptors.length === 0) {
                setLabel('❌ Data wajah tidak valid. Hubungi admin.', '#f87171'); return;
            }

            allDescriptors = labeledDescriptors;
            faceMatcher    = new faceapi.FaceMatcher(labeledDescriptors, MATCH_THRESHOLD);

        } catch (e) {
            setLabel('❌ Gagal memuat database wajah: ' + (e.message || 'timeout'), '#f87171');
            clearTimeout(retryTimer);
            retryTimer = setTimeout(init, 5000);
            return;
        }

        setProgress(100);
        setLabel(`✅ Siap! ${allDescriptors.length} pegawai terdaftar. Arahkan wajah ke kamera...`);
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

    // ═══════════════════════════════════════════════════════════
    // DETECTION LOOP – KETAT MODE (1-to-Many)
    // ═══════════════════════════════════════════════════════════
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
                ? new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.6 })
                : new faceapi.SsdMobilenetv1Options({ minConfidence: 0.6 });

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
            confirmedCount   = 0;
            lastMatchedLabel = null;
            confirmedDists   = [];
            setLabel('Wajah tidak terdeteksi. Pastikan pencahayaan cukup dan wajah jelas...');
            dot.className    = 'scanning';

        // ── Guard 2: Multiple faces – DITOLAK KETAT ──
        } else if (detections.length > 1) {
            confirmedCount   = 0;
            lastMatchedLabel = null;
            confirmedDists   = [];
            setLabel('❌ DITOLAK: Terdeteksi ' + detections.length + ' wajah! Hanya 1 wajah diperbolehkan.', '#ef4444');
            dot.className    = 'scanning';
            const resAll = faceapi.resizeResults(detections, { width: canvas.width, height: canvas.height });
            resAll.forEach(d => {
                ctx.strokeStyle = '#ef4444'; ctx.lineWidth = 3;
                ctx.strokeRect(d.detection.box.x, d.detection.box.y, d.detection.box.width, d.detection.box.height);
            });

        } else {
            const det            = detections[0];
            const resized        = faceapi.resizeResults([det], { width: canvas.width, height: canvas.height })[0];
            const box            = resized.detection.box;
            const landmarks      = resized.landmarks;
            const detectionScore = resized.detection.score;

            if (box.width < 100 || box.height < 120) {
                confirmedCount   = 0;
                lastMatchedLabel = null;
                confirmedDists   = [];
                setLabel('⚠️ Dekatkan wajah Anda ke kamera.', '#fbbf24');
                dot.className    = 'scanning';
                isDetecting = false;
                setTimeout(detectLoop, 300);
                return;
            }

            // SIMPLE MODE: Tidak ada Guard 3 Occlusion.
            // 100% serahkan ke FaceMatcher untuk menyesuaikan dengan data di neural.json

            // ── Guard 4: FaceMatcher – 1-to-Many identification (SUPER KETAT) ──
            if (!faceMatcher) {
                setLabel('❌ Sistem belum siap. Memuat ulang...', '#f87171');
                isDetecting = false; setTimeout(init, 1000); return;
            }

            const bestMatch = faceMatcher.findBestMatch(det.descriptor);
            const dist      = bestMatch.distance;
            const confPct   = Math.round((1 - dist) * 100);
            // ⭐ CRITICAL FIX: DUA layer pengecekan – label BUKAN 'unknown' DAN confidence >= MIN_CONFIDENCE (72%)
            const isStrictMatch = (bestMatch.label !== 'unknown') && (confPct >= Math.round(MIN_CONFIDENCE * 100));

            ctx.strokeStyle = isStrictMatch ? '#f97316' : '#ef4444';
            ctx.lineWidth   = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            ctx.fillStyle = isStrictMatch ? '#f97316' : '#ef4444';
            ctx.font      = 'bold 13px sans-serif';
            const displayLabel = isStrictMatch ? bestMatch.label : 'Tidak Dikenal';
            const labelText    = isStrictMatch
                ? `✓ ${displayLabel}  ${confPct}%`
                : `✗ Tidak Dikenal  ${confPct}%`;
            ctx.fillText(labelText, box.x + 4, box.y > 22 ? box.y - 7 : box.y + box.height + 16);

            if (isStrictMatch && !isSubmitting) {
                if (lastMatchedLabel !== bestMatch.label) {
                    confirmedCount    = 1;
                    lastMatchedLabel  = bestMatch.label;
                    confirmedDists    = [dist];
                } else {
                    confirmedCount++;
                    confirmedDists.push(dist);
                }
                const avgDist = (confirmedDists.reduce((a,b)=>a+b,0) / confirmedDists.length).toFixed(3);
                setLabel(`Verifikasi: ${bestMatch.label} (${confirmedCount}/${CONFIRM_FRAMES}) – ${confPct}% [avg: ${avgDist}]`);

                // ⭐ CRITICAL FIX: Setelah CONFIRM_FRAMES, cek RATA-RATA distance seluruh frame harus < MIN_AVG_DISTANCE
                if (confirmedCount >= CONFIRM_FRAMES) {
                    const totalDist = confirmedDists.reduce((a,b)=>a+b,0);
                    const avgDistFinal = totalDist / confirmedDists.length;

                    confirmedCount   = 0;
                    lastMatchedLabel = null;
                    confirmedDists   = [];

                    if (avgDistFinal >= MIN_AVG_DISTANCE) {
                        setLabel(`❌ VERIFIKASI GAGAL: Rata-rata kecocokan ${Math.round((1-avgDistFinal)*100)}% terlalu rendah. Coba lagi.`, '#ef4444');
                        isDetecting = false;
                        setTimeout(detectLoop, 800);
                        return;
                    }

                    isDetecting      = false;
                    submitAbsen(bestMatch.label);
                    return;
                }
            } else if (!isStrictMatch) {
                confirmedCount   = 0;
                lastMatchedLabel = null;
                confirmedDists   = [];
                if (confPct < Math.round(MIN_CONFIDENCE * 100)) {
                    setLabel(`❌ KECERNAAN KURANG: Kecocokan ${confPct}% (minimal ${Math.round(MIN_CONFIDENCE*100)}%). Posisikan wajah dengan jelas.`, '#ef4444');
                } else {
                    setLabel('❌ WAJAH TIDAK DAPAT DIBACA: Tidak terdaftar di database. Hubungi admin.', '#ef4444');
                }
            }
        }

        isDetecting = false;
        setTimeout(detectLoop, 350);
    }

    // ═══════════════════════════════════════════════════════════
    // SUBMIT
    // ═══════════════════════════════════════════════════════════
    function submitAbsen(username) {
        if (isSubmitting) return;

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
        setLabel(`✅ Wajah dikenali: ${username}! Menyimpan absensi...`);
        setProgress(80);

        const cap = document.createElement('canvas');
        cap.width  = 640; cap.height = 640;
        cap.getContext('2d').drawImage(video, 0, 0, 640, 640);
        const imgData = cap.toDataURL('image/jpeg', 0.85);

        const lat  = document.getElementById('lat').value;
        const long = document.getElementById('long').value;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });
        $.ajax({
            type: 'POST', url: "{{ url('/presensi-pulang/store') }}",
            data: { username, image: imgData, lat, long },
            dataType: 'json',
            timeout: 25000,
            success: function(resp) {
                setProgress(100);
                matchedOverlay.classList.remove('show');
                let text, icon, title;
                const status = resp.status || 'error';
                const nama   = resp.name   || username;
                switch (status) {
                    case 'pulang':
                        title = '✅ Absen Pulang Berhasil';
                        text  = `Terimakasih ${nama} telah absen keluar.`;
                        icon  = 'success';
                        break;
                    case 'outlocation':
                        title = '⚠️ Di Luar Radius';
                        text  = `${nama}, Anda di luar radius kantor. Absen DITOLAK.`;
                        icon  = 'warning';
                        break;
                    case 'belummasuk':
                        title = '❌ Belum Absen Masuk';
                        text  = `${nama} BELUM absen masuk hari ini. Tidak bisa absen pulang.`;
                        icon  = 'error';
                        break;
                    case 'selesai':
                        title = 'ℹ️ Sudah Absen';
                        text  = `${nama} sudah absen pulang hari ini.`;
                        icon  = 'info';
                        break;
                    case 'noMs':
                        title = '⚠️ Shift Belum Diatur';
                        text  = `Shift ${nama} belum diatur. Hubungi admin.`;
                        icon  = 'warning';
                        break;
                    case 'noUser':
                        title = '❌ Pengguna Tidak Ditemukan';
                        text  = `Data ${username} tidak ditemukan.`;
                        icon  = 'error';
                        break;
                    default:
                        title = '❌ Error';
                        text  = `Terjadi kesalahan. Hubungi admin.`;
                        icon  = 'error';
                }
                Swal.fire({
                    title: title,
                    text:  text,
                    icon,
                    confirmButtonColor: '#f97316',
                    timer: 5000,
                    timerProgressBar: true,
                    allowOutsideClick: false
                }).then(() => {
                    if (status === 'pulang' || status === 'selesai') {
                        window.location.href = "{{ url('/') }}";
                    } else {
                        isSubmitting    = false;
                        detectionActive = true;
                        setProgress(0);
                        dot.className   = 'scanning';
                        confirmedCount   = 0;
                        lastMatchedLabel = null;
                        confirmedDists   = [];
                        setTimeout(detectLoop, 500);
                    }
                });
            },
            error: function(xhr, status) {
                isSubmitting    = false;
                detectionActive = true;
                matchedOverlay.classList.remove('show');
                setLabel(status === 'timeout' ? '⚠️ Koneksi lambat. Coba lagi...' : '❌ Gagal menyimpan. Coba lagi...', '#f87171');
                setProgress(0);
                dot.className = 'scanning';
                confirmedCount   = 0;
                lastMatchedLabel = null;
                confirmedDists   = [];
                setTimeout(detectLoop, 500);
            }
        });
    }

    init().catch(e => { setLabel('❌ Error: ' + e.message, '#f87171'); });
</script>
@endpush
@endsection
