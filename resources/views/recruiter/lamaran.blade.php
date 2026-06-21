<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran - DoleKerjo Recruiter</title>
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
            <a href="{{ route('recruiter.dashboard') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('recruiter.jobs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                Lowongan
            </a>
            <a href="{{ route('recruiter.lamaran') }}" class="active">
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
            <h1>Kelola Lamaran</h1>
            <p>Menampilkan {{ $total }} dari total pelamar di semua lowongan Anda</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <!-- ── Toolbar: Search + Status Filter ── -->
        <div class="toolbar reveal">
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <form id="searchForm" method="GET" action="{{ route('recruiter.lamaran') }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1;">
                    @if ($status !== '')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="search-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="searchInput" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau posisi...">
                        @if ($search !== '')
                            <a href="{{ route('recruiter.lamaran', $status !== '' ? ['status' => $status] : []) }}" class="search-clear">&times;</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="status-filters">
                @php
                    $allCount = array_sum($countMap);
                    $queryParams = function($s) use ($search) {
                        $p = [];
                        if ($s !== '') $p['status'] = $s;
                        if ($search !== '') $p['search'] = $search;
                        return $p;
                    };
                @endphp
                <a href="{{ route('recruiter.lamaran', $queryParams('')) }}" class="status-btn {{ $status === '' ? 'active' : '' }}">Semua ({{ $allCount }})</a>
                <a href="{{ route('recruiter.lamaran', $queryParams('pending')) }}" class="status-btn pending {{ $status === 'pending' ? 'active' : '' }}">Pending ({{ $countMap['pending'] ?? 0 }})</a>
                <a href="{{ route('recruiter.lamaran', $queryParams('review')) }}" class="status-btn review {{ $status === 'review' ? 'active' : '' }}">Review ({{ $countMap['review'] ?? 0 }})</a>
                <a href="{{ route('recruiter.lamaran', $queryParams('interview')) }}" class="status-btn interview {{ $status === 'interview' ? 'active' : '' }}">Interview ({{ $countMap['interview'] ?? 0 }})</a>
                <a href="{{ route('recruiter.lamaran', $queryParams('diterima')) }}" class="status-btn diterima {{ $status === 'diterima' ? 'active' : '' }}">Diterima ({{ $countMap['diterima'] ?? 0 }})</a>
                <a href="{{ route('recruiter.lamaran', $queryParams('ditolak')) }}" class="status-btn ditolak {{ $status === 'ditolak' ? 'active' : '' }}">Ditolak ({{ $countMap['ditolak'] ?? 0 }})</a>
            </div>
        </div>

        <!-- ── Applicants Table ── -->
        <div class="card reveal">
            @if (count($applicants) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pelamar</th>
                        <th>Posisi Dilamar</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>CV</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="text-align:right;">Ubah Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applicants as $a)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="applicant-avatar">{{ strtoupper(substr($a->nama, 0, 1)) }}</div>
                                <strong>{{ $a->nama }}</strong>
                            </div>
                        </td>
                        <td>{{ $a->nama_posisi }}</td>
                        <td style="font-size:12px;">{{ $a->email }}</td>
                        <td style="font-size:12px;">{{ $a->telepon ?? '-' }}</td>
                        <td>
                            @if ($a->cv)
                                <a href="{{ asset($a->cv) }}" target="_blank" class="btn-cv-dl" title="Download CV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    <span>CV</span>
                                </a>
                            @else
                                <span style="color:var(--text-3);font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--text-3);">{{ \Carbon\Carbon::parse($a->created_at)->format('d M Y') }}</td>
                        <td><span class="badge {{ $a->status }}">{{ $a->status }}</span></td>
                        <td style="text-align:right;">
                            <form id="status-form-{{ $a->id }}" action="{{ route('recruiter.lamaran.status', $a->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $a->status }}">
                                <input type="hidden" name="catatan" value="{{ $a->catatan ?? '' }}">
                                <select class="status-select" onchange="changeStatus({{ $a->id }}, this.value)">
                                    <option value="pending" {{ $a->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="review" {{ $a->status === 'review' ? 'selected' : '' }}>Review</option>
                                    <option value="interview" {{ $a->status === 'interview' ? 'selected' : '' }}>Interview</option>
                                    <option value="diterima" {{ $a->status === 'diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="ditolak" {{ $a->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($totalPages > 1)
            <div class="pagination">
                @php
                    $baseUrl = route('recruiter.lamaran');
                    $buildUrl = function($p) use ($baseUrl, $status, $search) {
                        $params = ['page' => $p];
                        if ($status !== '') $params['status'] = $status;
                        if ($search !== '') $params['search'] = $search;
                        return $baseUrl . '?' . http_build_query($params);
                    };
                @endphp

                {{-- Prev --}}
                @if ($page > 1)
                    <a href="{{ $buildUrl($page - 1) }}" class="page-btn">&laquo;</a>
                @else
                    <span class="page-btn disabled">&laquo;</span>
                @endif

                {{-- Page numbers with smart ellipsis --}}
                @php $start = max(1, $page - 2); $end = min($totalPages, $page + 2); @endphp

                @if ($start > 1)
                    <a href="{{ $buildUrl(1) }}" class="page-btn">1</a>
                    @if ($start > 2) <span class="page-dots">...</span> @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    <a href="{{ $buildUrl($i) }}" class="page-btn {{ $i === $page ? 'active' : '' }}">{{ $i }}</a>
                @endfor

                @if ($end < $totalPages)
                    @if ($end < $totalPages - 1) <span class="page-dots">...</span> @endif
                    <a href="{{ $buildUrl($totalPages) }}" class="page-btn">{{ $totalPages }}</a>
                @endif

                {{-- Next --}}
                @if ($page < $totalPages)
                    <a href="{{ $buildUrl($page + 1) }}" class="page-btn">&raquo;</a>
                @else
                    <span class="page-btn disabled">&raquo;</span>
                @endif
            </div>
            @endif

            @else
                <p class="empty-state">
                    @if ($search !== '' || $status !== '')
                        Tidak ada hasil yang cocok dengan filter Anda.
                    @else
                        Belum ada pelamar di lowongan Anda.
                    @endif
                </p>
            @endif
        </div>

    </main>
</div>

<footer class="recruiter-footer"><p>© 2026 DoleKerjo — Recruiter Panel</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/recruiter.js') }}"></script>
</body>
</html>
