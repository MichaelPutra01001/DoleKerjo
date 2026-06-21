<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users - DoleKerjo</title>
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

        <div class="page-header reveal">
            <h1>Kelola Users</h1>
            <p>Lihat, verifikasi recruiter, dan hapus user</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="card reveal">
            <h2>Semua Users ({{ $total }})</h2>

            {{-- Search + Sort + Filter Bar --}}
            <div class="toolbar">
                {{-- Search --}}
                <form id="searchForm" method="GET" action="{{ route('admin.users') }}" class="search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" id="searchInput" placeholder="Cari nama, username, atau email..." value="{{ $search }}" autocomplete="off">
                    @if ($search)
                        <a href="{{ route('admin.users', ['role'=>$role,'sort'=>$sort,'dir'=>$dir]) }}" class="search-clear" title="Hapus pencarian">&times;</a>
                    @endif
                    {{-- Carry over sort/filter params --}}
                    <input type="hidden" name="role" value="{{ $role }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="dir"  value="{{ $dir }}">
                </form>

                {{-- Sort + Role Filter --}}
                <div class="sort-bar">
                    <label>Urutkan:</label>
                    <form id="sortForm" method="GET" action="{{ route('admin.users') }}" style="display:flex;gap:8px;align-items:center;">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="role"  value="{{ $role }}">
                        <select name="sort" id="sortSelect" onchange="document.getElementById('sortForm').submit()">
                            <option value="id"         {{ $sort === 'id'         ? 'selected' : '' }}>ID</option>
                            <option value="created_at" {{ $sort === 'created_at' ? 'selected' : '' }}>Tanggal Daftar</option>
                            <option value="nama"       {{ $sort === 'nama'       ? 'selected' : '' }}>Nama</option>
                            <option value="email"      {{ $sort === 'email'      ? 'selected' : '' }}>Email</option>
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

                    {{-- Role filter buttons --}}
                    <div class="role-filters">
                        <a href="{{ route('admin.users', ['search'=>$search,'sort'=>$sort,'dir'=>$dir,'role'=>'']) }}"
                           class="role-btn {{ $role === '' ? 'active' : '' }}">Semua</a>
                        <a href="{{ route('admin.users', ['search'=>$search,'sort'=>$sort,'dir'=>$dir,'role'=>'user']) }}"
                           class="role-btn user {{ $role === 'user' ? 'active' : '' }}">User</a>
                        <a href="{{ route('admin.users', ['search'=>$search,'sort'=>$sort,'dir'=>$dir,'role'=>'recruiter']) }}"
                           class="role-btn recruiter {{ $role === 'recruiter' ? 'active' : '' }}">Recruiter</a>
                        <a href="{{ route('admin.users', ['search'=>$search,'sort'=>$sort,'dir'=>$dir,'role'=>'admin']) }}"
                           class="role-btn admin {{ $role === 'admin' ? 'active' : '' }}">Admin</a>
                    </div>
                </div>
            </div>

            @if (count($users) > 0)
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                                <tr>
                                    <td>{{ $u->id }}</td>
                                    <td><strong>{{ $u->nama }}</strong></td>
                                    <td>{{ $u->username }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td><span class="badge {{ $u->role }}">{{ $u->role }}</span></td>
                                    <td>
                                        @if ($u->role === 'recruiter')
                                            @if ($u->is_verified)
                                                <span class="badge verified">Terverifikasi</span>
                                            @else
                                                <span class="badge pending">Pending</span>
                                            @endif
                                        @elseif ($u->role === 'user')
                                            @if ($u->email_verified == 1)
                                                <span class="badge verified">Email Terverifikasi</span>
                                            @elseif ($u->email_verified == 2)
                                                <span class="badge pending">Email Pending</span>
                                            @else
                                                <span style="font-size:12px;color:var(--text-3);">—</span>
                                            @endif
                                        @else
                                            <span style="font-size:12px;color:var(--text-3);">—</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</td>
                                    <td style="white-space:nowrap;display:flex;gap:6px;">
                                        @if ($u->role === 'recruiter')
                                            <a href="{{ route('admin.recruiter.detail', $u->id) }}" class="btn-sm btn-detail">Detail</a>
                                            @if (!$u->is_verified)
                                                <form action="{{ route('admin.users.verify', $u->id) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit" class="btn-sm btn-verify">Verifikasi</button>
                                                </form>
                                            @endif
                                        @endif
                                        @if ($u->role === 'user' && $u->email_verified == 2)
                                            <form action="{{ route('admin.users.verifyEmail', $u->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                <button type="submit" class="btn-sm btn-verify">Verifikasi Email</button>
                                            </form>
                                        @endif
                                        @if ($u->id != session('user_id'))
                                            <form id="del-user-{{ $u->id }}" action="{{ route('admin.users.delete', $u->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-sm btn-delete" onclick="confirmDelete('del-user-{{ $u->id }}')">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($totalPages > 1)
                    <div class="pagination">
                        {{-- Prev --}}
                        @if ($page > 1)
                            <a href="{{ route('admin.users', ['search'=>$search,'role'=>$role,'sort'=>$sort,'dir'=>$dir,'page'=>$page-1]) }}" class="page-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </a>
                        @else
                            <span class="page-btn disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        @endif

                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $totalPages; $i++)
                            @if ($i == $page)
                                <span class="page-btn active">{{ $i }}</span>
                            @elseif ($i == 1 || $i == $totalPages || abs($i - $page) <= 2)
                                <a href="{{ route('admin.users', ['search'=>$search,'role'=>$role,'sort'=>$sort,'dir'=>$dir,'page'=>$i]) }}" class="page-btn">{{ $i }}</a>
                            @elseif (abs($i - $page) == 3)
                                <span class="page-dots">&hellip;</span>
                            @endif
                        @endfor

                        {{-- Next --}}
                        @if ($page < $totalPages)
                            <a href="{{ route('admin.users', ['search'=>$search,'role'=>$role,'sort'=>$sort,'dir'=>$dir,'page'=>$page+1]) }}" class="page-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        @else
                            <span class="page-btn disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </span>
                        @endif
                    </div>
                @endif
            @else
                <p class="empty-state">
                    @if ($search)
                        Tidak ada user yang cocok dengan pencarian "{{ $search }}".
                    @else
                        Belum ada user terdaftar.
                    @endif
                </p>
            @endif
        </div>

    </main>
</div>

<footer class="admin-footer"><p>© 2026 DoleKerjo — Admin Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
