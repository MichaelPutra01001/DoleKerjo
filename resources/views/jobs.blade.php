<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Job - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/jobs.css') }}">
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
        <a href="{{ route('jobs') }}" class="active">List Job</a>
        <a href="{{ route('perusahaan') }}">Perusahaan</a>
        <a href="/matching">Skill Matching</a>
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

<section>
    <div class="container">
        <h2>Daftar Lowongan</h2>

        @forelse ($jobs as $job)
            <div class="job-card reveal">
                <div>
                    <h3>{{ $job->nama_posisi }}</h3>
                    <p>{{ $job->nama_perusahaan }} &nbsp;·&nbsp; {{ $job->lokasi ?? '-' }}</p>
                    <span class="badge-type {{ $job->tipe_class }}">{{ $job->tipe_label }}</span>
                </div>
                <button class="btn-detail" data-id="{{ $job->id }}">Lihat Detail</button>
            </div>
        @empty
            <p style="font-size:14px;color:#777;text-align:center;padding:40px 0">
                Belum ada lowongan tersedia.
            </p>
        @endforelse

    </div>
</section>

<!-- ── Modal Detail Job ── -->
<div id="modalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;">
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;padding:32px;max-width:520px;width:90%;max-height:80vh;overflow-y:auto;position:relative;color:var(--text);">
        <button onclick="tutupModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-3);">✕</button>
        <h3 id="modalPosisi" style="font-size:1.2rem;font-weight:700;margin-bottom:4px"></h3>
        <p id="modalPerusahaan" style="font-size:13.5px;color:var(--text-3);margin-bottom:16px"></p>
        <div id="modalBody" style="font-size:14px;color:var(--text-2);line-height:1.7"></div>
    </div>
</div>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/jobs.js') }}"></script>
</body>
</html>