<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="top-bar">
    <h1>GradMatch</h1>
    <p>Platform Job Matching Berbasis Kompetensi</p>
</div>

<section class="login-container">
    <div class="login-card">
        <h2>Masuk ke Akun</h2>
        <p class="sub">Selamat datang kembali</p>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="text" name="username" placeholder="Email atau Username"
                   value="{{ old('username') }}" required>
            <input type="password" name="password" placeholder="Password" required>

            @if ($errors->has('login'))
                <p class="error-msg">{{ $errors->first('login') }}</p>
            @else
                <p class="error-msg" id="errorMsg"></p>
            @endif

            <button type="submit">Masuk</button>
        </form>

        <p class="register">Belum punya akun? <a href="/register">Daftar</a></p>
    </div>
</section>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>