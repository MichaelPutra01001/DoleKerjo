<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Skill Matching - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/hasil.css') }}">
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
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('profil') }}">Profil</a>
        <a href="{{ route('jobs') }}">List Job</a>
        <a href="{{ route('perusahaan') }}">Perusahaan</a>
        <a href="/matching" class="active">Skill Matching</a>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:14px;font-weight:500;color:#8A9099;padding:7px 14px;border-radius:6px;">
                Logout
            </button>
        </form>
    </div>
</nav>

<div class="container">
    <h1>Hasil Analisis CV Kamu</h1>
    <p class="subtitle">Berdasarkan data CV yang kamu unggah</p>

    <div class="card highlight reveal">
        <h2>Ringkasan Kecocokan</h2>
        <div class="score" id="scoreNum">0%</div>
        <p>Kamu memiliki kecocokan tinggi dengan bidang <strong>Web Development</strong>.</p>
    </div>

    <div class="card reveal">
        <h2>Analisis Skill</h2>
        <div class="skill"><span>HTML & CSS</span><div class="bar"><div class="fill" data-width="95"></div></div></div>
        <div class="skill"><span>JavaScript</span><div class="bar"><div class="fill" data-width="75"></div></div></div>
        <div class="skill"><span>UI/UX Design</span><div class="bar"><div class="fill" data-width="70"></div></div></div>
        <div class="skill"><span>Backend Development</span><div class="bar"><div class="fill" data-width="55"></div></div></div>
    </div>

    <div class="grid-2 reveal">
        <div class="card">
            <h2>Kelebihan</h2>
            <ul>
                <li>Struktur HTML rapi & terstruktur</li>
                <li>Memahami responsive design</li>
                <li>Portofolio frontend cukup kuat</li>
            </ul>
        </div>
        <div class="card">
            <h2>Perlu Ditingkatkan</h2>
            <ul>
                <li>Pengalaman backend masih terbatas</li>
                <li>Belum familiar dengan framework modern</li>
                <li>Kurang pengalaman kerja tim (Git workflow)</li>
            </ul>
        </div>
    </div>

    <div class="card reveal">
        <h2>Rekomendasi Pekerjaan</h2>
        <div class="job-card"><div><h3>Frontend Developer</h3><p>PT Digital Nusantara</p></div><span class="badge">Match 90%</span></div>
        <div class="job-card"><div><h3>UI Designer</h3><p>Creative Studio ID</p></div><span class="badge">Match 85%</span></div>
        <div class="job-card"><div><h3>Junior Web Developer</h3><p>Startup Teknologi Maju</p></div><span class="badge">Match 80%</span></div>
    </div>

    <div style="text-align:center; margin-top:16px">
        <a href="/matching" class="btn-back">← Analisis Ulang</a>
    </div>
</div>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/hasil.js') }}"></script>
</body>
</html>