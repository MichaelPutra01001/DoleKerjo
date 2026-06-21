<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $job->nama_posisi }} - {{ $job->nama_perusahaan }} | DoleKerjo</title>
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
<body class="job-detail-page">

<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">DoleKerjo</a>
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

<!-- Pass data to JS -->
<script>
    window.JOB_ID    = {{ $job->id }};
    window.JOB_DATA  = @json($job);
    window.SUDAH_LAMAR = {{ $job->sudah_lamar ?? 0 }};
</script>

<section class="detail-section">
    <div class="detail-container">

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('jobs') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                List Job
            </a>
            <span>/</span>
            <span>{{ $job->nama_posisi }}</span>
        </div>

        <div class="detail-grid">
            <!-- ── Left: Job Details ── -->
            <div class="detail-main">
                <div class="detail-header card reveal">
                    <div class="detail-header-top">
                        <div>
                            <h1>{{ $job->nama_posisi }}</h1>
                            <p class="detail-company-name">
                                {{ $job->nama_perusahaan }}
                                @if($job->lokasi)
                                    &nbsp;·&nbsp; {{ $job->lokasi }}
                                @endif
                            </p>
                        </div>
                        <span class="badge-type {{ $job->tipe_class }}">{{ $job->tipe_label }}</span>
                        @if($job->kategori)
                        <span class="badge-kategori">{{ ucfirst($job->kategori) }}</span>
                        @endif
                    </div>
                    <div class="detail-meta">
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            <span>{{ $job->total_pelamar ?? 0 }} pelamar</span>
                        </div>
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Diposting {{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</span>
                        </div>
                        @if($job->gaji_min)
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            <span>Rp {{ number_format($job->gaji_min, 0, ',', '.') }} - Rp {{ number_format($job->gaji_max, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card reveal">
                    <h2>Deskripsi Pekerjaan</h2>
                    <div class="detail-text">{!! nl2br(e($job->deskripsi ?? 'Tidak ada deskripsi.')) !!}</div>
                </div>

                <div class="card reveal">
                    <h2>Persyaratan</h2>
                    <div class="detail-text">{!! nl2br(e($job->requirement ?? 'Tidak ada persyaratan khusus.')) !!}</div>
                </div>
            </div>

            <!-- ── Right Sidebar ── -->
            <div class="detail-sidebar">

                <!-- Apply Card -->
                <div class="card reveal apply-card">
                    <button id="btnDaftarCepat" class="btn-daftar-cepat" data-job-id="{{ $job->id }}" {{ $job->sudah_lamar ? 'disabled' : '' }}>
                        @if($job->sudah_lamar)
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Sudah Melamar
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            Daftar Cepat
                        @endif
                    </button>
                    <p class="apply-hint">
                        @if($job->sudah_lamar)
                            Kamu sudah melamar pekerjaan ini.
                        @else
                            Klik untuk langsung melamar pekerjaan ini.
                        @endif
                    </p>
                </div>

                <!-- Company Card -->
                <div class="card reveal">
                    <h2>Tentang Perusahaan</h2>
                    @if($perusahaan)
                        <div class="company-info-card">
                            @if($perusahaan->logo)
                                <img src="{{ asset($perusahaan->logo) }}" alt="{{ $perusahaan->nama }}" class="company-logo-img">
                            @else
                                <div class="company-logo-placeholder">{{ strtoupper(substr($perusahaan->nama, 0, 1)) }}</div>
                            @endif
                            <h3>{{ $perusahaan->nama }}</h3>
                            @if($perusahaan->lokasi)
                                <p class="company-loc">{{ $perusahaan->lokasi }}</p>
                            @endif
                            @if($perusahaan->deskripsi)
                                <p class="company-desc">{{ Str::limit($perusahaan->deskripsi, 120) }}</p>
                            @endif
                            <a href="{{ route('perusahaan.show', $perusahaan->id) }}" class="btn-company-link">
                                Lihat Profil Perusahaan
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    @else
                        <div class="company-info-card">
                            <div class="company-logo-placeholder">{{ strtoupper(substr($job->nama_perusahaan, 0, 1)) }}</div>
                            <h3>{{ $job->nama_perusahaan }}</h3>
                        </div>
                    @endif
                </div>

                <!-- Related Jobs -->
                @if(count($relatedJobs) > 0)
                <div class="card reveal">
                    <h2>Lowongan Lainnya</h2>
                    <div class="related-jobs">
                        @foreach($relatedJobs as $rj)
                            <a href="{{ route('jobs.show', $rj->id) }}" class="related-job-item">
                                <div>
                                    <strong>{{ $rj->nama_posisi }}</strong>
                                    <p>{{ $rj->lokasi ?? '-' }}</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

<!-- Toast notification -->
<div id="toast" class="toast"></div>

<footer><p>&copy; 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/jobs.js') }}"></script>
</body>
</html>
