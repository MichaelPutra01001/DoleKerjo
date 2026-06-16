<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Job - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/jobs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="jobs-listing-page">

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

<section class="listing-section">
    <div class="listing-container">

        <!-- ── Left: Filter Sidebar ── -->
        <aside class="filter-sidebar" id="filterSidebar">
            <form id="filterForm" method="GET" action="{{ route('jobs') }}">

                <!-- Sort -->
                <div class="filter-group">
                    <h4>Urutkan</h4>
                    <label class="filter-radio">
                        <input type="radio" name="sort" value="gaji" {{ $sort === 'gaji' ? 'checked' : '' }}>
                        <span class="radio-dot"></span>
                        Gaji Tertinggi
                    </label>
                    <label class="filter-radio">
                        <input type="radio" name="sort" value="terbaru" {{ $sort === 'terbaru' ? 'checked' : '' }}>
                        <span class="radio-dot"></span>
                        Terbaru
                    </label>
                </div>

                <!-- Kategori -->
                @php
                    $trendLimit = 5;
                    $hasCheckedOverflow = false;
                    foreach(array_slice($categories, $trendLimit) as $cat) {
                        if(in_array($cat->nama, (array)$kategori)) { $hasCheckedOverflow = true; break; }
                    }
                @endphp
                <div class="filter-group">
                    <h4>Kategori</h4>
                    <div class="filter-list {{ $hasCheckedOverflow ? 'expanded' : '' }}" id="kategoriList">
                        @foreach($categories as $i => $cat)
                            <label class="filter-check {{ $i >= $trendLimit ? 'filter-overflow' : '' }}">
                                <input type="checkbox" name="kategori[]" value="{{ $cat->nama }}" {{ in_array($cat->nama, (array)$kategori) ? 'checked' : '' }}>
                                <span class="check-box"></span>
                                {{ $cat->nama }}
                                <small class="filter-count">({{ $cat->job_count }})</small>
                            </label>
                        @endforeach
                        @if(count($categories) > $trendLimit)
                            <button type="button" class="btn-show-more" onclick="toggleShowMore('kategoriList')">
                                <span class="show-more-text">+{{ count($categories) - $trendLimit }} lainnya</span>
                                <span class="show-less-text">Sembunyikan</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Tipe Kerja -->
                <div class="filter-group">
                    <h4>Tipe Kerja</h4>
                    @foreach(['full-time','remote','hybrid','contract'] as $t)
                    <label class="filter-check">
                        <input type="checkbox" name="tipe[]" value="{{ $t }}" {{ in_array($t, (array)$tipe) ? 'checked' : '' }}>
                        <span class="check-box"></span>
                        {{ ucfirst($t) }}
                    </label>
                    @endforeach
                </div>

                <!-- Lokasi -->
                @php
                    $locLimit = 5;
                    $allLocs = array_merge(array_map(function($l){ return $l->lokasi; }, $locations), ['remote']);
                    $hasLocOverflow = false;
                    foreach(array_slice($allLocs, $locLimit) as $loc) {
                        if(in_array($loc, (array)$lokasi)) { $hasLocOverflow = true; break; }
                    }
                @endphp
                <div class="filter-group">
                    <h4>Lokasi</h4>
                    <div class="filter-list {{ $hasLocOverflow ? 'expanded' : '' }}" id="lokasiList">
                        @foreach($locations as $i => $loc)
                            <label class="filter-check {{ $i >= $locLimit ? 'filter-overflow' : '' }}">
                                <input type="checkbox" name="lokasi[]" value="{{ $loc->lokasi }}" {{ in_array($loc->lokasi, (array)$lokasi) ? 'checked' : '' }}>
                                <span class="check-box"></span>
                                {{ $loc->lokasi }}
                            </label>
                        @endforeach
                        <label class="filter-check {{ count($locations) >= $locLimit ? 'filter-overflow' : '' }}">
                            <input type="checkbox" name="lokasi[]" value="remote" {{ in_array('remote', (array)$lokasi) ? 'checked' : '' }}>
                            <span class="check-box"></span>
                            Remote / Mana saja
                        </label>
                        @if(count($allLocs) > $locLimit)
                            <button type="button" class="btn-show-more" onclick="toggleShowMore('lokasiList')">
                                <span class="show-more-text">+{{ count($allLocs) - $locLimit }} lainnya</span>
                                <span class="show-less-text">Sembunyikan</span>
                            </button>
                        @endif
                    </div>
                </div>

                <button type="button" class="btn-reset-filter" onclick="resetFilter()">Reset Filter</button>
            </form>

            <!-- Mobile toggle -->
            <button class="filter-toggle-mobile" id="filterToggle" onclick="toggleFilterMobile()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filter
            </button>
        </aside>

        <!-- ── Right: Job List ── -->
        <div class="listing-main">
            <div class="listing-header">
                <h2>Daftar Lowongan</h2>
                <span class="job-count">{{ count($jobs) }} lowongan ditemukan</span>
            </div>

            @forelse ($jobs as $job)
                <div class="job-card reveal">
                    <div class="job-info">
                        <h3>{{ $job->nama_posisi }}</h3>
                        <p>{{ $job->nama_perusahaan }} &nbsp;&middot;&nbsp; {{ $job->lokasi ?? '-' }}</p>
                        <div class="job-badges">
                            <span class="badge-type {{ $job->tipe_class }}">{{ $job->tipe_label }}</span>
                            @if($job->kategori)
                                <span class="badge-kategori">{{ ucfirst($job->kategori) }}</span>
                            @endif
                            @if($job->gaji_min)
                                <span class="badge-gaji">Rp {{ number_format($job->gaji_min / 1000000, 0) }} - {{ number_format($job->gaji_max / 1000000, 0) }} Jt</span>
                            @endif
                            <span class="badge-pelamar">{{ $job->total_pelamar ?? 0 }} pelamar</span>
                        </div>
                    </div>
                    <div class="job-actions">
                        <button class="btn-daftar-cepat" data-job-id="{{ $job->id }}" {{ ($job->sudah_lamar ?? 0) ? 'disabled' : '' }}>
                            @if($job->sudah_lamar ?? 0)
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Sudah Dilamar
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                Daftar Cepat
                            @endif
                        </button>
                        <a href="{{ route('jobs.show', $job->id) }}" class="btn-detail-link" title="Lihat detail lengkap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <p>Belum ada lowongan tersedia.</p>
                    <p class="empty-sub">Coba ubah filter untuk melihat hasil lainnya.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- Toast notification -->
<div id="toast" class="toast"></div>

<footer><p>&copy; 2026 GradMatch</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/jobs.js') }}"></script>
</body>
</html>
