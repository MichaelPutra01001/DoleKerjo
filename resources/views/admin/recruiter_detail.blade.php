<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Recruiter - DoleKerjo Admin</title>
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
        <a href="{{ route('admin.dashboard') }}" class="brand">DoleKerjo <small>Admin</small></a>
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
            <a href="{{ route('admin.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.jobs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                Lowongan
            </a>
            <a href="{{ route('admin.users') }}" class="active">
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

        {{-- Back link --}}
        <div class="detail-back reveal">
            <a href="{{ route('admin.users') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Users
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="page-header reveal">
            <h1>Detail Recruiter</h1>
            <p>Periksa informasi recruiter sebelum melakukan verifikasi</p>
        </div>

        {{-- ── Section 1: Akun Recruiter ── --}}
        <div class="card reveal detail-card">
            <div class="detail-card-header">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Informasi Akun
                </h2>
                <div>
                    @if ($user->is_verified)
                        <span class="badge verified">Terverifikasi</span>
                    @else
                        <span class="badge pending">Pending</span>
                    @endif
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-field">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value">{{ $user->nama }}</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Username</span>
                    <span class="detail-value">{{ $user->username }}</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">No. Telepon</span>
                    <span class="detail-value">{{ $user->telepon ?: '—' }}</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Tanggal Daftar</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Total Lowongan</span>
                    <span class="detail-value">{{ $totalJobs }} lowongan</span>
                </div>
            </div>
        </div>

        {{-- ── Section 2: Info Perusahaan ── --}}
        <div class="card reveal detail-card">
            <div class="detail-card-header">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    Informasi Perusahaan
                </h2>
            </div>

            @if ($perusahaan)
                <div class="detail-grid">
                    <div class="detail-field">
                        <span class="detail-label">Nama Perusahaan</span>
                        <span class="detail-value">{{ $perusahaan->nama }}</span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Bidang Industri</span>
                        <span class="detail-value">{{ $perusahaan->tipe_bisnis ?: '—' }}</span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Lokasi Kantor</span>
                        <span class="detail-value">{{ $perusahaan->lokasi ?: '—' }}</span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Website</span>
                        <span class="detail-value">
                            @if ($perusahaan->website)
                                <a href="{{ $perusahaan->website }}" target="_blank" rel="noopener">{{ $perusahaan->website }}</a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Tahun Didirikan</span>
                        <span class="detail-value">{{ $perusahaan->ditemukan_tahun ?: '—' }}</span>
                    </div>
                </div>

                @if ($perusahaan->deskripsi)
                    <div class="detail-field detail-field-full">
                        <span class="detail-label">Deskripsi Perusahaan</span>
                        <p class="detail-desc">{{ $perusahaan->deskripsi }}</p>
                    </div>
                @endif
            @else
                <p class="empty-state">Recruiter ini belum melengkapi data perusahaan.</p>
            @endif
        </div>

        {{-- ── Action Buttons ── --}}
        <div class="detail-actions reveal">
            @if (!$user->is_verified)
                <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-action btn-approve">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Verifikasi Recruiter
                    </button>
                </form>
            @endif
            <form id="del-user-{{ $user->id }}" action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn-action btn-reject" onclick="confirmDelete('del-user-{{ $user->id }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Tolak Ajuan
                </button>
            </form>
            <a href="{{ route('admin.users') }}" class="btn-action btn-back-link">Kembali</a>
        </div>

    </main>
</div>

<footer class="admin-footer"><p>© 2026 DoleKerjo — Admin Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
