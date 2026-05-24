<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Matching - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/matching.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">GradMatch</a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('profil') }}">Profil</a>
        <a href="{{ route('jobs') }}">List Job</a>
        <a href="/matching" class="active">Skill Matching</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:14px;font-weight:500;color:#8A9099;padding:7px 14px;border-radius:6px;">
                Logout
            </button>
        </form>
    </div>
</nav>

<section class="upload-section">
    <h2>Upload CV & Kompetensi</h2>
    <p class="desc">Upload CV dalam format PDF atau DOCX, lalu masukkan skill yang kamu kuasai.</p>
   <form id="matchingForm" class="form-card">
        @csrf
        <input type="file" id="cvFile" accept=".pdf,.docx">
        <p class="file-error" id="fileError"></p>
        <input type="text" id="skillInput" placeholder="Masukkan skill (contoh: HTML, Python, UI/UX)">
        <button type="submit">Cocokkan Sekarang</button>
    </form>
</section>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/matching.js') }}"></script>
</body>
</html>