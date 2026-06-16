<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan Saya - GradMatch Recruiter</title>
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
            <a href="{{ route('recruiter.jobs') }}" class="active">
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

        <div class="page-header reveal" style="display:flex;align-items:flex-end;justify-content:space-between;">
            <div>
                <h1>Lowongan Saya</h1>
                <p>Kelola semua lowongan pekerjaan yang Anda posting</p>
            </div>
            <button class="btn-primary" onclick="openJobModal('create')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Lowongan
            </button>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <!-- ── Sort Bar ── -->
        <div class="card reveal" style="padding:14px 20px;">
            <div class="sort-bar">
                <label>Urutkan:</label>
                <form id="sortForm" method="GET" action="{{ route('recruiter.jobs') }}" style="display:flex;gap:8px;align-items:center;">
                    <select name="sort" id="sortSelect" onchange="document.getElementById('sortForm').submit()">
                        <option value="id" {{ $sort === 'id' ? 'selected' : '' }}>ID</option>
                        <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Tanggal</option>
                        <option value="nama_posisi" {{ $sort === 'nama_posisi' ? 'selected' : '' }}>Posisi</option>
                        <option value="nama_perusahaan" {{ $sort === 'nama_perusahaan' ? 'selected' : '' }}>Perusahaan</option>
                        <option value="tipe" {{ $sort === 'tipe' ? 'selected' : '' }}>Tipe</option>
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
        </div>

        <!-- ── Jobs Table ── -->
        <div class="card reveal">
            @if (count($jobs) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Posisi</th>
                        <th>Perusahaan</th>
                        <th>Lokasi</th>
                        <th>Tipe</th>
                        <th>Gaji</th>
                        <th>Pelamar</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                    <tr>
                        <td>{{ $job->id }}</td>
                        <td><strong>{{ $job->nama_posisi }}</strong></td>
                        <td>{{ $job->nama_perusahaan }}</td>
                        <td>{{ $job->lokasi ?? '-' }}</td>
                        <td><span class="tipe-badge {{ $job->tipe_class }}">{{ $job->tipe_label }}</span></td>
                        <td>
                            @if ($job->gaji_min || $job->gaji_max)
                                {{ $job->gaji_min ? number_format($job->gaji_min, 0, ',', '.') : '?' }} - {{ $job->gaji_max ? number_format($job->gaji_max, 0, ',', '.') : '?' }}
                            @else
                                -
                            @endif
                        </td>
                        <td><strong>{{ $job->pelamar_count }}</strong></td>
                        <td style="font-size:12px;color:var(--text-3);">{{ \Carbon\Carbon::parse($job->created_at)->format('d M Y') }}</td>
                        <td style="text-align:right;">
                            <button class="btn-sm btn-edit" onclick="editJob({{ $job->id }})">Edit</button>
                            <form id="delete-form-{{ $job->id }}" action="{{ route('recruiter.jobs.delete', $job->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-sm btn-delete" onclick="confirmDelete('delete-form-{{ $job->id }}')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <p class="empty-state">Belum ada lowongan. Klik "Tambah Lowongan" untuk membuat yang pertama.</p>
            @endif
        </div>

    </main>
</div>

<!-- ── Job Modal ── -->
<div id="jobModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Lowongan Baru</h2>
            <button class="modal-close" onclick="closeJobModal()">&times;</button>
        </div>
        <form id="jobForm" method="POST"
              data-create-url="{{ route('recruiter.jobs.store') }}"
              data-update-url="{{ url('recruiter/jobs/__ID__') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Posisi *</label>
                    <input type="text" name="nama_posisi" required placeholder="Contoh: Frontend Developer">
                </div>
                <div class="form-group">
                    <label>Perusahaan *</label>
                    <input type="text" name="nama_perusahaan" required placeholder="Nama perusahaan">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="lokasi" placeholder="Contoh: Jakarta Selatan">
                </div>
                <div class="form-group">
                    <label>Tipe *</label>
                    <select name="tipe" required>
                        <option value="full-time">Full Time</option>
                        <option value="part-time">Part Time</option>
                        <option value="remote">Remote</option>
                        <option value="hybrid">Hybrid</option>
                        <option value="contract">Contract</option>
                        <option value="partnership">Partnership</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label>Kategori *</label>
                <select name="kategori" required>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Gaji Min</label>
                    <input type="number" name="gaji_min" placeholder="0" min="0">
                </div>
                <div class="form-group">
                    <label>Gaji Max</label>
                    <input type="number" name="gaji_max" placeholder="0" min="0">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi pekerjaan..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom:22px;">
                <label>Requirement</label>
                <textarea name="requirement" rows="3" placeholder="Kualifikasi yang dibutuhkan..."></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn-secondary" onclick="closeJobModal()">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<footer class="recruiter-footer"><p>© 2026 GradMatch — Recruiter Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/recruiter.js') }}"></script>
</body>
</html>
