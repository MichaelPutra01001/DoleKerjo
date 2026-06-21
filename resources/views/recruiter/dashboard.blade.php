<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Dashboard - DoleKerjo</title>
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
        <a href="{{ route('recruiter.dashboard') }}" class="brand">DoleKerjo <small>Recruiter</small></a>
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
            <a href="{{ route('recruiter.dashboard') }}" class="active">
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
            <a href="{{ route('recruiter.profil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profil
            </a>
        </nav>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">

        <div class="page-header reveal">
            <h1>Dashboard</h1>
            <p>Selamat datang kembali, {{ session('nama') }}</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- ── Stats Grid ── -->
        <div class="stats-grid">
            <div class="stat-card reveal">
                <div class="stat-icon indigo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalJobs }}">0</div>
                <div class="stat-label">Lowongan Aktif</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon teal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalLamaran }}">0</div>
                <div class="stat-label">Total Pelamar</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon yellow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $pendingLamaran }}">0</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $interviewCount }}">0</div>
                <div class="stat-label">Interview</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $diterimaCount }}">0</div>
                <div class="stat-label">Diterima</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon red">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $ditolakCount }}">0</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>

        <!-- ── Company Rating ── -->
        @php
            $avgR = $companyRating ? (float)($companyRating->avg_rating ?? 0) : 0;
            $totalR = $companyRating ? (int)($companyRating->total ?? 0) : 0;
        @endphp
        <div class="card reveal" style="padding:20px 24px;margin-bottom:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <h2 style="font-size:14px;margin-bottom:4px;">Rating Perusahaan</h2>
                    <p style="font-size:12px;color:var(--text-3);">Dari {{ $totalR }} review pengguna</p>
                </div>
                <div style="text-align:right;">
                    @if($totalR > 0)
                        <span style="font-family:'Fraunces',serif;font-size:2rem;font-weight:700;color:var(--primary);">{{ number_format($avgR, 1) }}</span>
                        <div style="display:flex;gap:2px;justify-content:flex-end;">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color:{{ $avgR >= $i ? '#F59E0B' : '#D1D5DB' }};font-size:16px;">&#9733;</span>
                            @endfor
                        </div>
                    @else
                        <span style="font-size:13px;color:var(--text-3);">Belum ada review</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- ── Recent Data ── -->
        <div class="grid-2">
            <div class="card reveal">
                <h2>Pelamar Terbaru</h2>
                @forelse ($recentApplicants as $a)
                    <div class="applicant-row">
                        <div class="applicant-avatar">{{ strtoupper(substr($a->nama, 0, 1)) }}</div>
                        <div class="applicant-info">
                            <div class="nama">{{ $a->nama }}</div>
                            <div class="meta">{{ $a->nama_posisi }} &middot; {{ \Carbon\Carbon::parse($a->created_at)->diffForHumans() }}</div>
                        </div>
                        <span class="badge {{ $a->status }}">{{ $a->status }}</span>
                    </div>
                @empty
                    <p class="empty-state">Belum ada pelamar.</p>
                @endforelse
            </div>

            <div class="card reveal">
                <h2>Lowongan Terbaru</h2>
                @forelse ($recentJobs as $j)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                        <div>
                            <strong style="font-size:13.5px;">{{ $j->nama_posisi }}</strong>
                            <p style="font-size:12px;color:var(--text-3);margin-top:2px;">{{ $j->nama_perusahaan }}</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="tipe-badge {{ $j->tipe }}">{{ ucfirst($j->tipe) }}</span>
                            <span style="font-size:11px;color:var(--text-3);">{{ \Carbon\Carbon::parse($j->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="empty-state">Belum ada lowongan.</p>
                @endforelse
            </div>
        </div>

    </main>
</div>

<footer class="recruiter-footer"><p>© 2026 DoleKerjo — Recruiter Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/recruiter.js') }}"></script>
</body>
</html>
