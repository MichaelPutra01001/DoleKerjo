<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar perusahaan terdaftar di platform DoleKerjo. Temukan insight, review, dan lowongan dari berbagai perusahaan.">
    <title>Perusahaan — DoleKerjo</title>
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

<!-- ── Page Header ── -->
<div class="page-header">
    <h1>Direktori <span>Perusahaan</span></h1>
    <p>{{ count($perusahaan) }} perusahaan terdaftar di platform DoleKerjo</p>
</div>

<!-- ── Search ── -->
<div class="search-wrap">
    <div class="search-box">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="searchInput" placeholder="Cari perusahaan atau lokasi…" autocomplete="off">
    </div>
</div>

<!-- ── Company Grid ── -->
<div class="company-grid" id="companyGrid">

    @forelse($perusahaan as $p)
    @php
        $inisial = mb_strtoupper(mb_substr($p->nama, 0, 1));
        $avg = $p->avg_rating ? round($p->avg_rating, 1) : null;
    @endphp

    <a
        href="{{ route('perusahaan.show', $p->id) }}"
        class="company-card reveal"
        data-nama="{{ strtolower($p->nama) }}"
        data-lokasi="{{ strtolower($p->lokasi ?? '') }}"
        id="card-company-{{ $p->id }}"
    >
        <!-- Arrow icon -->
        <div class="card-arrow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>

        <!-- Top: Logo + Info -->
        <div class="card-top">
            @if($p->logo)
                <img class="company-logo" src="{{ $p->logo }}" alt="{{ $p->nama }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="logo-placeholder" style="display:none">{{ $inisial }}</div>
            @else
                <div class="logo-placeholder">{{ $inisial }}</div>
            @endif

            <div class="card-info">
                <h3>{{ $p->nama }}</h3>
                <div class="lokasi">
                    @if($p->lokasi)
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        {{ $p->lokasi }}
                    @else
                        <span>Lokasi belum diisi</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rating -->
        <div class="card-rating">
            @if($avg)
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($avg >= $i)
                            <span class="star filled"><svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
                        @else
                            <span class="star"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
                        @endif
                    @endfor
                </div>
                <span class="rating-num">{{ $avg }}</span>
                <span class="rating-count">({{ $p->total_reviews ?? 0 }} review)</span>
            @else
                <span style="font-size:12px;color:var(--text-muted)">Belum ada review</span>
            @endif
        </div>

        <!-- Stats -->
        <div class="card-stats">
            <div class="stat-item">
                <span class="val">{{ $p->total_jobs ?? 0 }}</span>
                <span class="lbl">Lowongan</span>
            </div>
            @if($p->total_reviews)
            <div class="stat-item">
                <span class="val">{{ $p->total_reviews }}</span>
                <span class="lbl">Review</span>
            </div>
            @endif
            @if($p->website)
            <div class="stat-item">
                <span class="val" style="font-size:13px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                </span>
                <span class="lbl">Website</span>
            </div>
            @endif
        </div>
    </a>

    @empty
    <div class="empty-state" id="emptyState">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/>
        </svg>
        <p>Belum ada perusahaan terdaftar.</p>
    </div>
    @endforelse

    <!-- Empty state saat search tidak ada hasil -->
    @if(count($perusahaan) > 0)
    <div class="empty-state" id="emptyState" style="display:none">
        <p>Tidak ada perusahaan yang cocok dengan pencarianmu.</p>
    </div>
    @endif
</div>

<footer><p>© 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/perusahaan.js') }}"></script>
</body>
</html>
