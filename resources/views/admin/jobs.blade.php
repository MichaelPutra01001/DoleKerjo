<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Lowongan - GradMatch</title>
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
            <a href="{{ route('admin.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.jobs') }}" class="active">
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
            <h1>Kelola Lowongan</h1>
            <p>Lihat dan hapus semua lowongan yang tersedia</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="card reveal">
            <h2>Semua Lowongan ({{ count($jobs) }})</h2>

            {{-- Sort Bar --}}
            <div class="sort-bar">
                <label>Urutkan:</label>
                <form id="sortForm" method="GET" action="{{ route('admin.jobs') }}" style="display:flex;gap:8px;align-items:center;">
                    <select name="sort" id="sortSelect" onchange="document.getElementById('sortForm').submit()">
                        <option value="id"                 {{ $sort === 'id'                 ? 'selected' : '' }}>ID</option>
                        <option value="created_at"         {{ $sort === 'created_at'         ? 'selected' : '' }}>Tanggal Dibuat</option>
                        <option value="nama_posisi"        {{ $sort === 'nama_posisi'        ? 'selected' : '' }}>Posisi</option>
                        <option value="nama_perusahaan"    {{ $sort === 'nama_perusahaan'    ? 'selected' : '' }}>Perusahaan</option>
                        <option value="tipe"               {{ $sort === 'tipe'               ? 'selected' : '' }}>Tipe</option>
                    </select>
                    <button type="button" class="sort-dir" onclick="toggleDir()" title="{{ $dir === 'asc' ? 'Ascending' : 'Descending' }}">
                        @if ($dir === 'asc')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                        @endif
                    </button>
                    <input type="hidden" name="dir" id="dirInput" value="{{ $dir === 'asc' ? 'asc' : 'desc' }}">
                </form>
            </div>

            @if (count($jobs) > 0)
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Posisi</th>
                                <th>Perusahaan</th>
                                <th>Recruiter</th>
                                <th>Tipe</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jobs as $job)
                                <tr>
                                    <td>{{ $job->id }}</td>
                                    <td><strong>{{ $job->nama_posisi }}</strong></td>
                                    <td>{{ $job->nama_perusahaan }}</td>
                                    <td>{{ $job->recruiter_nama ?? '-' }}</td>
                                    <td><span class="badge-type {{ $job->tipe_class }}">{{ $job->tipe_label }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($job->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <form id="del-job-{{ $job->id }}" action="{{ route('admin.jobs.delete', $job->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-sm btn-delete" onclick="confirmDelete('del-job-{{ $job->id }}')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="empty-state">Belum ada lowongan tersedia.</p>
            @endif
        </div>

    </main>
</div>

<footer class="admin-footer"><p>© 2026 GradMatch — Admin Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
