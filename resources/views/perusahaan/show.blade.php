<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detail perusahaan {{ $perusahaan->nama }} — review, lowongan, dan informasi lengkap di DoleKerjo.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $perusahaan->nama }} — DoleKerjo</title>
    <link rel="stylesheet" href="{{ asset('css/perusahaan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body>

<!-- ── Navbar ── -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">DoleKerjo</a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('profil') }}">Profil</a>
        <a href="{{ route('jobs') }}">List Job</a>
        <a href="{{ route('perusahaan') }}" class="active">Perusahaan</a>
        <a href="{{ route('matching') }}">Skill Matching</a>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</nav>

<!-- ── Hero Section ── -->
<div class="detail-hero">
    <div class="detail-hero-inner">

        <!-- Back link -->
        <a href="{{ route('perusahaan') }}" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Kembali ke Daftar Perusahaan
        </a>

        <!-- Company identity -->
        <div class="hero-top">
            @php $inisial = mb_strtoupper(mb_substr($perusahaan->nama, 0, 2)); @endphp

            @if($perusahaan->logo)
                <img class="hero-logo" src="{{ $perusahaan->logo }}" alt="{{ $perusahaan->nama }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="hero-logo-placeholder" style="display:none">{{ $inisial }}</div>
            @else
                <div class="hero-logo-placeholder">{{ $inisial }}</div>
            @endif

            <div class="hero-info">
                <h1>{{ $perusahaan->nama }}</h1>
                <div class="recruiter-tag">Dikelola oleh {{ $perusahaan->recruiter_nama ?? '-' }}</div>
                <div class="hero-badges">
                    @if($perusahaan->lokasi)
                        <span class="badge">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            {{ $perusahaan->lokasi }}
                        </span>
                    @endif
                    @if($perusahaan->website)
                        <a href="{{ $perusahaan->website }}" target="_blank" class="badge accent">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            Website
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="tab-nav" role="tablist">
            <button class="tab-btn active" data-tab="overview" role="tab" id="tab-overview-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/>
                    <rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>
                </svg>
                Overview
            </button>
            <button class="tab-btn" data-tab="reviews" role="tab" id="tab-reviews-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Review
                <span class="tab-count">0</span>
            </button>
            <button class="tab-btn" data-tab="lamaran" role="tab" id="tab-lamaran-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/>
                    <line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/>
                </svg>
                Lamaran
                <span class="tab-count">0</span>
            </button>
            <button class="tab-btn" data-tab="connections" role="tab" id="tab-connections-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                Connection
                <span class="tab-count">0</span>
            </button>
        </div>

    </div>
</div>

<!-- ── Tab Content ── -->
<div class="tab-content-area">

    <!-- Overview -->
    <div class="tab-panel active" id="panel-overview" role="tabpanel">
        <!-- JS akan isi konten ini -->
        <div class="skeleton-wrap">
            <div class="skeleton" style="height:200px"></div>
            <div class="skeleton" style="height:160px;opacity:.7"></div>
            <div class="skeleton" style="height:120px;opacity:.5"></div>
        </div>
    </div>

    <!-- Review -->
    <div class="tab-panel" id="panel-reviews" role="tabpanel">
        <!-- JS akan isi konten ini -->
    </div>

    <!-- Lamaran -->
    <div class="tab-panel" id="panel-lamaran" role="tabpanel">
        <!-- JS akan isi konten ini -->
    </div>

    <!-- Connection -->
    <div class="tab-panel" id="panel-connections" role="tabpanel">
        <!-- JS akan isi konten ini -->
    </div>

</div>

<!-- ── Modal Detail Job ── -->
<div id="modalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;">
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;padding:32px;max-width:520px;width:90%;max-height:80vh;overflow-y:auto;position:relative;box-shadow:0 20px 48px rgba(0,0,0,.15);color:var(--text);">
        <button onclick="tutupModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-3);line-height:1;display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;" aria-label="Tutup">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <h3 id="modalPosisi" style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;margin-bottom:4px;color:var(--text);padding-right:40px"></h3>
        <p id="modalPerusahaan" style="font-size:13.5px;color:var(--text-3);margin-bottom:20px"></p>
        <div id="modalBody" style="font-size:14px;color:var(--text-2);line-height:1.8"></div>
    </div>
</div>

<footer><p>© 2026 DoleKerjo</p></footer>

<!-- Pass data ke JS -->
<script>
    window.PERUSAHAAN_ID   = {{ $perusahaan->id }};
    window.PERUSAHAAN_NAMA = @json($perusahaan->nama);
    window.USER_ROLE       = @json($role);
    window.USER_ID         = {{ $user_id ?? 'null' }};
</script>
<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/perusahaan.js') }}"></script>

</body>
</html>
