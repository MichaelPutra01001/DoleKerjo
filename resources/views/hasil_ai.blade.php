<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Matching Results - DoleKerjo</title>
    <link rel="stylesheet" href="{{ asset('css/matching.css') }}">
    <link rel="stylesheet" href="{{ asset('css/hasil_ai.css') }}">
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
    <div class="ai-header">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
        <div>
            <h1>AI Job Matching</h1>
            <p class="subtitle">Analisis AI berdasarkan CV {{ $user->nama ?? 'Kamu' }}</p>
        </div>
    </div>

    @if($error)
        <div class="error-card">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <p>{{ $error }}</p>
        </div>
    @endif

    @if(!empty($aiResults))
        {{-- Summary --}}
        <div class="summary-card reveal">
            <h2>Ringkasan</h2>
            <p>AI menemukan <strong>{{ count($aiResults) }}</strong> lowongan yang cocok dengan profil kamu.</p>
            <div class="avg-score">
                <span class="score-num">{{ round(array_sum(array_column($aiResults, 'match_percent')) / count($aiResults)) }}%</span>
                <span class="score-label">Rata-rata Kecocokan</span>
            </div>
        </div>

        {{-- Job Results --}}
        <div class="results-list">
            @foreach($aiResults as $i => $job)
            <div class="job-result-card reveal" style="animation-delay: {{ $i * 0.08 }}s">
                <div class="job-header">
                    <div class="job-info">
                        <h3>{{ $job['nama_posisi'] }}</h3>
                        <p class="job-company">{{ $job['nama_perusahaan'] }} &middot; {{ $job['lokasi'] ?? 'Indonesia' }}</p>
                        <div class="job-tags">
                            @if($job['tipe'])
                                <span class="tag tag-type">{{ $job['tipe'] }}</span>
                            @endif
                            @if($job['kategori'])
                                <span class="tag tag-cat">{{ $job['kategori'] }}</span>
                            @endif
                            @if($job['gaji_min'] || $job['gaji_max'])
                                <span class="tag tag-salary">
                                    @if($job['gaji_min'])
                                        {{ number_format($job['gaji_min']/1000000, 1) }}jt
                                    @endif
                                    -
                                    @if($job['gaji_max'])
                                        {{ number_format($job['gaji_max']/1000000, 1) }}jt
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="score-circle {{ $job['match_percent'] >= 70 ? 'score-high' : ($job['match_percent'] >= 50 ? 'score-mid' : 'score-low') }}">
                        <span class="score-val">{{ $job['match_percent'] }}</span>
                        <span class="score-pct">%</span>
                    </div>
                </div>

                {{-- AI Reasoning --}}
                <div class="ai-reasoning">
                    <div class="reasoning-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>Analisis AI</span>
                    </div>
                    <p class="reasoning-text">{{ $job['reasoning'] }}</p>
                </div>

                {{-- Matched Skills --}}
                @if(!empty($job['matched_skills']))
                <div class="matched-section">
                    <span class="section-label">Skill yang cocok:</span>
                    <div class="skill-tags">
                        @foreach($job['matched_skills'] as $ms)
                            <span class="match-tag">{{ $ms }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Gaps --}}
                @if(!empty($job['gaps']))
                <div class="gaps-section">
                    <span class="section-label">Perlu ditingkatkan:</span>
                    <div class="skill-tags">
                        @foreach($job['gaps'] as $gap)
                            <span class="gap-tag">{{ $gap }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="job-action">
                    <a href="{{ route('jobs.show', $job['id']) }}" class="btn-view">Lihat Lowongan</a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        @if(!$error)
        <div class="no-results reveal">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h3>Tidak ada lowongan yang cocok</h3>
            <p>AI tidak menemukan lowongan yang cukup cocok dengan CV kamu saat ini. Coba tambahkan lebih banyak skill atau pengalaman di CV.</p>
        </div>
        @endif
    @endif

    <div class="actions">
        <a href="/matching" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Matching
        </a>
        <a href="{{ route('jobs') }}" class="btn-explore">Jelajahi Semua Lowongan</a>
    </div>
</div>

<footer><p>&copy; 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/hasil.js') }}"></script>
</body>
</html>
