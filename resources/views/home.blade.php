<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - GradMatch</title>
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
    <a href="{{ route('home') }}" class="brand">GradMatch</a>
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
        <h2 class="reveal">Mulai Kariermu dengan Tepat 🎯</h2>
        <p class="reveal">GradMatch membantu fresh graduate menemukan pekerjaan yang sesuai dengan skill dan kompetensi mereka.</p>
        <a href="/matching" class="btn-primary reveal">Coba Skill Matching</a>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="reveal">Kenapa GradMatch?</h2>
        <div class="cards">
            <div class="card reveal">
                <h3>🎯 Matching Berbasis Skill</h3>
                <p>Sistem kami mencocokkan CV dengan kebutuhan industri secara otomatis.</p>
            </div>
            <div class="card reveal">
                <h3>🏢 Insight Perusahaan</h3>
                <p>Lihat review dan budaya kerja sebelum melamar.</p>
            </div>
            <div class="card reveal">
                <h3>📊 Rekomendasi Karier</h3>
                <p>Dapatkan saran karier berdasarkan kompetensimu.</p>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container stats-grid">
        <div class="stats-text">
            <div class="stat-box reveal">
                <h3 data-target="500">0</h3>
                <p>Perusahaan Terdaftar</p>
            </div>
            <div class="stat-box reveal">
                <h3 data-target="10000">0</h3>
                <p>Fresh Graduate Terbantu</p>
            </div>
            <div class="stat-box reveal">
                <h3 data-target="85">0</h3>
                <p>Tingkat Kesesuaian Pekerjaan (%)</p>
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
        <div class="job-card reveal">
            <div>
                <h3>Frontend Developer</h3>
                <p>PT Digital Nusantara</p>
            </div>
            <span class="tag">Remote</span>
        </div>
        <div class="job-card reveal">
            <div>
                <h3>Data Analyst</h3>
                <p>Startup Insight Indonesia</p>
            </div>
            <span class="tag">Full-time</span>
        </div>
        <div style="text-align:center">
            <a href="/jobs" class="btn-primary reveal">Lihat Semua Lowongan</a>
        </div>
    </div>
</section>

<section class="reviews">
    <div class="container">
        <h2 class="reveal">Review Perusahaan</h2>
        <div class="review-card reveal">
            <p>"Lingkungan kerja yang suportif dan banyak peluang belajar."</p>
            <strong>— Karyawan, PT Digital Nusantara</strong>
        </div>
        <div class="review-card reveal">
            <p>"Work-life balance sangat baik dan tim kolaboratif."</p>
            <strong>— Data Analyst, Insight Indonesia</strong>
        </div>
    </div>
</section>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>