<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - DoleKerjo</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">DoleKerjo</a>
    <div class="nav-links">
        <a href="{{ route('home') }}" class="active">Home</a>
        <a href="/profil">Profil</a>
        <a href="/jobs">List Job</a>
        <a href="{{ route('perusahaan') }}">Perusahaan</a>
        <a href="/matching">Skill Matching</a>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        {{-- Logout pakai form POST, bukan link biasa --}}
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:14px;font-weight:500;color:#8A9099;padding:7px 14px;border-radius:6px;">
                Logout
            </button>
        </form>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <h2 class="reveal">
            Mulai Kariermu dengan Tepat
            <svg style="display:inline-block;vertical-align:middle;margin-left:6px" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
        </h2>
        <p class="reveal">DoleKerjo membantu fresh graduate menemukan pekerjaan yang sesuai dengan skill dan kompetensi mereka.</p>
        <a href="/matching" class="btn-primary reveal">Coba Skill Matching</a>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="reveal">Kenapa DoleKerjo?</h2>
        <div class="cards">
            <div class="card reveal">
                <h3>
                    <svg style="display:inline-block;vertical-align:-2px;margin-right:4px" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    Matching Berbasis Skill
                </h3>
                <p>Sistem kami mencocokkan CV dengan kebutuhan industri secara otomatis.</p>
            </div>
            <div class="card reveal">
                <h3>
                    <svg style="display:inline-block;vertical-align:-2px;margin-right:4px" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Insight Perusahaan
                </h3>
                <p>Lihat review dan budaya kerja sebelum melamar.</p>
            </div>
            <div class="card reveal">
                <h3>
                    <svg style="display:inline-block;vertical-align:-2px;margin-right:4px" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Rekomendasi Karier
                </h3>
                <p>Dapatkan saran karier berdasarkan kompetensimu.</p>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container stats-grid">
        <div class="stats-text">
            <div class="stat-box reveal">
                <h3 data-target="{{ $totalCompanies }}">0</h3>
                <p>Perusahaan Terdaftar</p>
            </div>
            <div class="stat-box reveal">
                <h3 data-target="{{ $totalUsers }}">0</h3>
                <p>Fresh Graduate Terbantu</p>
            </div>
            <div class="stat-box reveal">
                <h3 data-target="{{ $totalJobs }}">0</h3>
                <p>Lowongan Aktif</p>
            </div>
        </div>
        <div class="stats-visual reveal">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Ilustrasi Karier">
        </div>
    </div>
</section>

<section class="jobs-preview">
    <div class="container">
        <h2 class="reveal">Lowongan Terbaru</h2>
        @forelse($recentJobs as $rj)
        <a href="{{ route('jobs.show', $rj->id) }}" class="job-card reveal" style="text-decoration:none;color:inherit">
            <div>
                <h3>{{ $rj->nama_posisi }}</h3>
                <p>{{ $rj->nama_perusahaan }} &middot; {{ $rj->lokasi ?? '-' }}</p>
            </div>
            <span class="tag">{{ $rj->tipe_label }}</span>
        </a>
        @empty
        <p class="reveal" style="color:var(--text-3);font-size:14px">Belum ada lowongan tersedia.</p>
        @endforelse
        <div style="text-align:center">
            <a href="/jobs" class="btn-primary reveal">Lihat Semua Lowongan</a>
        </div>
    </div>
</section>

<section class="reviews">
    <div class="container">
        <h2 class="reveal">Review Perusahaan</h2>
        @forelse($recentReviews as $rev)
        <div class="review-card reveal">
            <p>"{{ $rev->isi_review }}"</p>
            <strong>— {{ $rev->reviewer ?? 'Anonim' }}, {{ $rev->nama_perusahaan }}</strong>
        </div>
        @empty
        <p class="reveal" style="color:var(--text-3);font-size:14px">Belum ada review perusahaan.</p>
        @endforelse
    </div>
</section>

<footer><p>© 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>