<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Perusahaan - GradMatch Recruiter</title>
    <link rel="stylesheet" href="{{ asset('css/recruiter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="recruiter-page">

<!-- ── Topbar ── -->
<header class="topbar">
    <div>
        <a href="{{ route('recruiter.dashboard') }}" class="brand">GradMatch <small>Recruiter</small></a>
    </div>
    <div class="topbar-right">
        <span class="recruiter-name">{{ session('nama') }}</span>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme" style="background:none;border:none;cursor:pointer;padding:6px 8px;border-radius:6px;color:var(--text-2);">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</header>

<div class="recruiter-layout">

    <!-- ── Sidebar ── -->
    <aside class="sidebar">
        <nav>
            <a href="{{ route('recruiter.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('recruiter.jobs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                Lowongan
            </a>
            <a href="{{ route('recruiter.lamaran') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Lamaran
            </a>
            <a href="{{ route('recruiter.profil') }}" class="active">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profil
            </a>
        </nav>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">

        <div class="page-header reveal">
            <h1>Profil Perusahaan</h1>
            <p>Kelola informasi akun dan perusahaan Anda</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        {{-- Account Info (read-only) --}}
        <div class="card reveal">
            <h2>Informasi Akun</h2>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px 24px;">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" value="{{ $user->nama }}" disabled>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="{{ $user->username }}" disabled>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" value="{{ $user->email }}" disabled>
                </div>
                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" value="{{ $user->telepon ?? '-' }}" disabled>
                </div>
            </div>
        </div>

        {{-- Company Rating --}}
        @php
            $avgR = $companyRating ? (float)($companyRating->avg_rating ?? 0) : 0;
            $totalR = $companyRating ? (int)($companyRating->total ?? 0) : 0;
        @endphp
        <div class="card reveal">
            <h2>Rating & Review</h2>
            <div style="display:flex;align-items:center;gap:24px;padding:8px 0;">
                <div style="text-align:center;">
                    <div style="font-family:'Fraunces',serif;font-size:2.8rem;font-weight:700;color:var(--primary);line-height:1;">
                        {{ $totalR > 0 ? number_format($avgR, 1) : '-' }}
                    </div>
                    <div style="display:flex;gap:2px;justify-content:center;margin:4px 0;">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="color:{{ $avgR >= $i ? '#F59E0B' : '#D1D5DB' }};font-size:18px;">&#9733;</span>
                        @endfor
                    </div>
                    <div style="font-size:12px;color:var(--text-3);">{{ $totalR }} review</div>
                </div>
                <div style="flex:1;">
                    <p style="font-size:13px;color:var(--text-2);line-height:1.6;">
                        Rating ini berasal dari review pengguna di halaman perusahaan Anda.
                        Semakin baik rating, semakin menarik perusahaan Anda bagi pelamar.
                    </p>
                </div>
            </div>
        </div>

        {{-- Company Profile (editable) --}}
        <div class="card reveal">
            <h2>Informasi Perusahaan</h2>
            <form method="POST" action="{{ route('recruiter.profil.update') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Perusahaan *</label>
                        <input type="text" name="nama" value="{{ $perusahaan->nama ?? '' }}" required placeholder="Nama perusahaan">
                    </div>
                    <div class="form-group">
                        <label>Tipe Bisnis</label>
                        <input type="text" name="tipe_bisnis" value="{{ $perusahaan->tipe_bisnis ?? '' }}" placeholder="Contoh: Teknologi, Keuangan, dll.">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" name="lokasi" value="{{ $perusahaan->lokasi ?? '' }}" placeholder="Contoh: Jakarta, Indonesia">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" name="website" value="{{ $perusahaan->website ?? '' }}" placeholder="https://example.com">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group" style="max-width:200px;">
                        <label>Tahun Didirikan</label>
                        <input type="number" name="ditemukan_tahun" value="{{ $perusahaan->ditemukan_tahun ?? '' }}" placeholder="2020" min="1800" max="2100">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:22px;">
                    <label>Deskripsi Perusahaan</label>
                    <textarea name="deskripsi" rows="5" placeholder="Ceritakan tentang perusahaan Anda...">{{ $perusahaan->deskripsi ?? '' }}</textarea>
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>

    </main>
</div>

<footer class="recruiter-footer"><p>© 2026 GradMatch — Recruiter Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/recruiter.js') }}"></script>
</body>
</html>
