<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Skill Matching - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/matching.css') }}">
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
    <a href="{{ route('home') }}" class="brand">GradMatch</a>
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

<section class="upload-section">
    <h2>
        <svg style="display:inline-block;vertical-align:-4px;margin-right:6px" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
        Skill Matching
    </h2>
    <p class="desc">Masukkan skill yang kamu kuasai, lalu sistem akan mencocokkan dengan lowongan yang tersedia.</p>

    <form id="matchingForm" class="form-card" method="POST" action="{{ route('hasil') }}">
        @csrf

        <!-- Skill chips area -->
        <div class="skill-label">Skill Kamu</div>
        <div class="skill-chips-area" id="skillChipsArea">
            @forelse($userSkills as $us)
                <span class="skill-chip" data-skill="{{ $us->nama }}">
                    {{ $us->nama }}
                    <button type="button" class="chip-remove" onclick="removeChip(this)" aria-label="Hapus">&times;</button>
                </span>
            @empty
                <span class="no-skills-hint" id="noSkillsHint">Belum ada skill di profil. Tambahkan di bawah.</span>
            @endforelse
        </div>

        <!-- Add skill input -->
        <div class="add-skill-row">
            <input type="text" id="skillInput" placeholder="Ketik skill lalu tekan Enter (cth: React, Node.js)">
            <button type="button" class="btn-add-skill" id="btnAddSkill" onclick="addSkillFromInput()">Tambah</button>
        </div>
        <p class="hint">Tekan Enter atau klik Tambah untuk menambahkan skill. Pisahkan dengan koma untuk banyak skill sekaligus.</p>

        <!-- Hidden input that collects all skill names -->
        <input type="hidden" name="skills" id="skillsHidden" value="">

        <button type="submit" id="btnSubmit">Cocokkan Sekarang</button>
    </form>
</section>

<footer><p>&copy; 2026 GradMatch</p></footer>

<!-- Pass profile skills to JS -->
<script>
    window.PROFILE_SKILLS = @json(array_map(fn($s) => $s->nama, $userSkills));
</script>
<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/matching.js') }}"></script>
</body>
</html>
