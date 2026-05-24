<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>List Job - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/jobs.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">GradMatch</a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('profil') }}">Profil</a>
        <a href="{{ route('jobs') }}" class="active">List Job</a>
        <a href="/matching">Skill Matching</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:14px;font-weight:500;color:#8A9099;padding:7px 14px;border-radius:6px;">
                Logout
            </button>
        </form>
    </div>
</nav>

<section>
    <div class="container">
        <h2>Daftar Lowongan</h2>

        @forelse ($jobs as $job)
            <div class="job-card reveal">
                <div>
                    <h3>{{ $job->nama_posisi }}</h3>
                    <p>{{ $job->nama_perusahaan }} &nbsp;·&nbsp; {{ $job->lokasi ?? '-' }}</p>
                    <span class="badge-type {{ $job->tipe_class }}">{{ $job->tipe_label }}</span>
                </div>
                <button class="btn-detail" data-id="{{ $job->id }}">Lihat Detail</button>
            </div>
        @empty
            <p style="font-size:14px;color:#777;text-align:center;padding:40px 0">
                Belum ada lowongan tersedia.
            </p>
        @endforelse

    </div>
</section>

<!-- ── Modal Detail Job ── -->
<div id="modalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:32px;max-width:520px;width:90%;max-height:80vh;overflow-y:auto;position:relative;">
        <button onclick="tutupModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:#8A9099;">✕</button>
        <h3 id="modalPosisi" style="font-size:1.2rem;font-weight:700;margin-bottom:4px"></h3>
        <p id="modalPerusahaan" style="font-size:13.5px;color:#8A9099;margin-bottom:16px"></p>
        <div id="modalBody" style="font-size:14px;color:#555;line-height:1.7"></div>
    </div>
</div>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/jobs.js') }}"></script>
</body>
</html>