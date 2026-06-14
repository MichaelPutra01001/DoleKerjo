<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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

<!-- ── Topbar ── -->
<header class="topbar">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="brand">GradMatch <small>Admin</small></a>
    </div>
    <div class="topbar-right">
        <span class="admin-name">{{ session('nama') }}</span>
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

<div class="admin-layout">

    <!-- ── Sidebar ── -->
    <aside class="sidebar">
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="active">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.jobs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                Lowongan
            </a>
            <a href="{{ route('admin.users') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Users
            </a>
            <a href="{{ route('admin.skills') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                Skills
            </a>
        </nav>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">

        <div class="page-header reveal">
            <h1>Dashboard</h1>
            <p>Ringkasan data platform GradMatch</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- ── Stats ── -->
        <div class="stats-grid">
            <div class="stat-card reveal">
                <div class="stat-icon teal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalUsers }}">0</div>
                <div class="stat-label">Total Job Seeker</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon yellow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalRecruiters }}">0</div>
                <div class="stat-label">Recruiter</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalJobs }}">0</div>
                <div class="stat-label">Lowongan</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="15" y2="16"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalLamaran }}">0</div>
                <div class="stat-label">Lamaran</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon teal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalSkills }}">0</div>
                <div class="stat-label">Skills</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-icon red">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="6" x2="15" y2="6"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="11" y2="14"/></svg>
                </div>
                <div class="stat-value" data-target="{{ $totalPerusahaan }}">0</div>
                <div class="stat-label">Perusahaan</div>
            </div>
        </div>

        @if ($pendingRecruiters > 0)
            <div class="alert error" style="margin-bottom:24px">
                ⚠️ Ada <strong>{{ $pendingRecruiters }}</strong> recruiter menunggu verifikasi.
                <a href="{{ route('admin.users') }}" style="text-decoration:underline;margin-left:6px;color:inherit;">Cek sekarang</a>
            </div>
        @endif

        <!-- ── Recent data ── -->
        <div class="grid-2">

            <div class="card reveal">
                <h2>User Terbaru</h2>
                @forelse ($recentUsers as $u)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                        <div>
                            <strong style="font-size:13.5px;">{{ $u->nama }}</strong>
                            <p style="font-size:12px;color:var(--text-3);margin-top:2px;">{{ $u->email }}</p>
                        </div>
                        <span class="badge {{ $u->role }}">{{ $u->role }}</span>
                    </div>
                @empty
                    <p class="empty-state">Belum ada user.</p>
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
                        <span style="font-size:11px;color:var(--text-3);">{{ \Carbon\Carbon::parse($j->created_at)->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="empty-state">Belum ada lowongan.</p>
                @endforelse
            </div>

        </div>

    </main>
</div>

<footer class="admin-footer"><p>© 2026 GradMatch — Admin Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
