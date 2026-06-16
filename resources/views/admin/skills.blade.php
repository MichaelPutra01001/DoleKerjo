<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Skills - GradMatch</title>
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
            <a href="{{ route('admin.jobs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                Lowongan
            </a>
            <a href="{{ route('admin.users') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Users
            </a>
            <a href="{{ route('admin.skills') }}" class="active">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                Skills
            </a>
        </nav>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">

        <div class="page-header reveal">
            <h1>Kelola Skills</h1>
            <p>Tambah dan hapus skill yang tersedia di platform</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- ── Add Kategori Form ── -->
        <div class="card reveal">
            <h2>Kategori Lowongan</h2>
            <p style="font-size:13px;color:var(--text-3);margin-bottom:16px;">Kelola kategori yang muncul di filter job listing dan form posting lowongan.</p>
            <form action="{{ route('admin.kategori.add') }}" method="POST" style="display:flex;gap:10px;align-items:flex-end;margin-bottom:18px;">
                @csrf
                <div class="form-group" style="flex:1">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama" placeholder="Contoh: Pertanian" required>
                </div>
                <button type="submit" class="btn-primary" style="height:38px;align-self:flex-end;">Tambah Kategori</button>
            </form>
            @if(count($kategoriList) > 0)
                <div class="skill-chips">
                    @foreach($kategoriList as $kat)
                        <span class="skill-chip">
                            {{ $kat->nama }}
                            <small style="opacity:.6;margin-left:2px;">({{ $kat->job_count }})</small>
                            <form id="del-kat-{{ $kat->id }}" action="{{ route('admin.kategori.delete', $kat->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="chip-del" onclick="confirmDelete('del-kat-{{ $kat->id }}')" title="Hapus kategori {{ $kat->nama }}">&times;</button>
                            </form>
                        </span>
                    @endforeach
                </div>
            @else
                <p class="empty-state">Belum ada kategori.</p>
            @endif
        </div>

        <!-- ── Add Skill Form ── -->
        <div class="card reveal">
            <h2>Tambah Skill Baru</h2>
            <form action="{{ route('admin.skills.add') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group" style="flex:1">
                        <label>Nama Skill</label>
                        <input type="text" name="nama" placeholder="Contoh: Digital Marketing" required>
                    </div>
                    <div class="form-group" style="min-width:160px">
                        <label>Kategori</label>
                        <select name="kategori" required>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary" style="height:38px;align-self:flex-end;">Tambah</button>
                </div>
            </form>
        </div>

        <!-- ── Skills List ── -->
        <div class="card reveal">
            <h2>Daftar Skills ({{ $totalSkills }})</h2>

            @if ($totalSkills > 0)
                <div class="skill-groups">
                    @foreach ($grouped as $kat => $items)
                        <div class="skill-group">
                            <h3 class="skill-group-title">{{ ucfirst($kat) }} <span>({{ count($items) }})</span></h3>
                            <div class="skill-chips">
                                @foreach ($items as $skill)
                                    <span class="skill-chip">
                                        {{ $skill->nama }}
                                        <form id="del-skill-{{ $skill->id }}" action="{{ route('admin.skills.delete', $skill->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="chip-del" onclick="confirmDelete('del-skill-{{ $skill->id }}')" title="Hapus {{ $skill->nama }}">&times;</button>
                                        </form>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="empty-state">Belum ada skill terdaftar.</p>
            @endif
        </div>

    </main>
</div>

<footer class="admin-footer"><p>© 2026 GradMatch — Admin Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
