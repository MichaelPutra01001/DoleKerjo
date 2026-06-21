<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Skill Matching - DoleKerjo</title>
    <link rel="stylesheet" href="{{ asset('css/hasil.css') }}">
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

<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">DoleKerjo</a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('profil') }}">Profil</a>
        <a href="{{ route('jobs') }}">List Job</a>
        <a href="{{ route('perusahaan') }}">Perusahaan</a>
        <a href="/matching" class="active">Skill Matching</a>
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

<div class="container">
    <h1>Hasil Analisis Skill Kamu</h1>
    <p class="subtitle">Berdasarkan {{ $totalSkills }} skill yang dicocokkan dengan lowongan</p>

    <!-- Score card -->
    <div class="card highlight reveal">
        <h2>Ringkasan Kecocokan</h2>
        <div class="score" data-target="{{ $matchScore }}">0%</div>
        @if($matchScore >= 60)
            <p>Kamu memiliki kecocokan tinggi dengan lowongan yang tersedia.</p>
        @elseif($matchScore >= 30)
            <p>Kamu memiliki kecocokan sedang. Tambahkan lebih banyak skill untuk hasil lebih baik.</p>
        @else
            <p>Kecocokan masih rendah. Coba tambahkan skill yang lebih spesifik atau update profil kamu.</p>
        @endif
    </div>

    <!-- Skill analysis bars -->
    @if(!empty($skillAnalysis))
    <div class="card reveal">
        <h2>Analisis Skill</h2>
        @foreach($skillAnalysis as $sa)
        <div class="skill">
            <span title="{{ $sa['kategori'] ?? 'Lainnya' }}">{{ $sa['name'] }}</span>
            <div class="bar">
                <div class="fill" data-width="{{ $sa['strength'] }}"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Strengths & Weaknesses -->
    <div class="grid-2 reveal">
        <div class="card">
            <h2>Kelebihan</h2>
            <ul>
                @foreach($strengths as $s)
                <li>{{ $s }}</li>
                @endforeach
            </ul>
        </div>
        <div class="card">
            <h2>Perlu Ditingkatkan</h2>
            <ul>
                @foreach($weaknesses as $w)
                <li>{{ $w }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Job recommendations -->
    <div class="card reveal">
        <h2>Rekomendasi Pekerjaan</h2>
        @forelse($matchedJobs as $job)
        <a href="{{ route('jobs.show', $job['id']) }}" class="job-card-link">
            <div class="job-card">
                <div>
                    <h3>{{ $job['nama_posisi'] }}</h3>
                    <p>{{ $job['nama_perusahaan'] }} &middot; {{ $job['lokasi'] }}</p>
                    <div class="matched-tags">
                        @foreach(array_slice($job['matched_skills'], 0, 4) as $ms)
                            <span class="match-tag">{{ $ms }}</span>
                        @endforeach
                    </div>
                </div>
                <span class="badge">Match {{ $job['match_percent'] }}%</span>
            </div>
        </a>
        @empty
        <div class="no-results">
            <p>Tidak ada lowongan yang cocok dengan skill kamu saat ini.</p>
            <p class="no-results-hint">Coba tambahkan skill lain atau perbarui profil kamu.</p>
        </div>
        @endforelse
    </div>

    <div style="text-align:center; margin-top:16px">
        <a href="/matching" class="btn-back">
            <svg style="display:inline-block;vertical-align:-3px;margin-right:4px" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Analisis Ulang
        </a>
    </div>
</div>

<footer><p>&copy; 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/hasil.js') }}"></script>
</body>
</html>
