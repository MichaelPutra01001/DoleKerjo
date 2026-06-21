<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - DoleKerjo</title>
    <link rel="stylesheet" href="{{ asset('css/regis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/regis_success.css') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body>

<div class="top-bar">
    <a href="{{ route('login') }}" class="brand">DoleKerjo</a>
    <p>Portal Registrasi Perusahaan Mitra &amp; Rekruter</p>
    <div style="margin-left: auto;">
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>
</div>

<section class="register-container">
    <div class="register-card">

        <!-- Progress Steps Bar (All steps done) -->
        <div class="steps-progress-bar">
            <div class="step completed">
                <div class="step-num">✓</div>
                <div class="step-label">Akun Rekruter</div>
            </div>
            <div class="step-line completed"></div>
            <div class="step completed">
                <div class="step-num">✓</div>
                <div class="step-label">Info Perusahaan</div>
            </div>
            <div class="step-line completed"></div>
            <div class="step active">
                <div class="step-num">3</div>
                <div class="step-label">Verifikasi</div>
            </div>
        </div>

        <!-- Success Icon -->
        <div class="success-ring">
            <!-- Hourglass / Pending SVG -->
            <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 8h22M11 36h22" stroke="#0A66C2" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M13 8c0 8 9 12 9 14s-9 6-9 14M31 8c0 8-9 12-9 14s9 6 9 14" stroke="#0A66C2" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 30.5c2-1.5 4-2 6-2s4 .5 6 2" stroke="#0A66C2" stroke-width="2" stroke-linecap="round"/>
                <circle cx="22" cy="19" r="2.5" fill="#0A66C2" opacity=".35"/>
            </svg>
        </div>

        <!-- Headline -->
        <h2 class="success-title">Pendaftaran Berhasil Dikirim!</h2>
        <p class="success-sub">
            Data rekruter dan perusahaan Anda telah kami terima.<br>
            Akun Anda sedang menunggu tinjauan dari tim Administrator.
        </p>

        <!-- Status Badge -->
        <div class="status-badge">
            <span class="status-dot"></span>
            Menunggu Verifikasi Admin
        </div>

        <!-- Info Cards -->
        <div class="info-cards">
            <div class="info-card">
                <div class="info-card-icon blue">📋</div>
                <div class="info-card-text">
                    <strong>Proses Tinjauan</strong>
                    <span>Admin akan meninjau data perusahaan Anda dalam 1–3 hari kerja.</span>
                </div>
            </div>
            <div class="info-card">
                <div class="info-card-icon green">📬</div>
                <div class="info-card-text">
                    <strong>Notifikasi Akses</strong>
                    <span>Notifikasi akan dikirim ke Email yang terdaftar.</span>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- CTA -->
        <a href="{{ route('login') }}" class="btn-login-return">
            Kembali ke Halaman Login
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>

    </div>
</section>

<footer><p>© 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
</body>
</html>
