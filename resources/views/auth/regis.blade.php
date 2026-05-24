<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/regis.css') }}">
</head>
<body>

<div class="top-bar">
    <a href="{{ route('login') }}" class="brand">GradMatch</a>
    <p>Platform Job Matching Berbasis Kompetensi</p>
</div>

<section class="register-container">
    <div class="register-card">

        <div class="card-header">
            <h2>Buat Akun Baru</h2>
            <p class="sub">Bergabung dan mulai temukan karier yang tepat</p>
        </div>

        <form action="{{ route('register') }}" method="POST" id="formRegister">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Hila Ro'uf Rusdi" value="{{ old('nama') }}" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="rouf123" value="{{ old('username') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" placeholder="kamu@email.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telepon">No. Telepon</label>
                    <input type="text" id="telepon" name="telepon" placeholder="+62 812 3456 7890" value="{{ old('telepon') }}">
                </div>
                <div class="form-group">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" placeholder="Jakarta, Indonesia" value="{{ old('lokasi') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" placeholder="Min. 8 karakter" required>
                        <button type="button" class="toggle-pass" onclick="togglePass('password', this)" tabindex="-1">👁</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                        <button type="button" class="toggle-pass" onclick="togglePass('confirm_password', this)" tabindex="-1">👁</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="pendidikan">Pendidikan Terakhir</label>
                <select id="pendidikan" name="pendidikan">
                    <option value="" disabled selected>Pilih jenjang pendidikan</option>
                    <option value="sma" {{ old('pendidikan') == 'sma' ? 'selected' : '' }}>SMA / SMK</option>
                    <option value="d3"  {{ old('pendidikan') == 'd3'  ? 'selected' : '' }}>D3</option>
                    <option value="s1"  {{ old('pendidikan') == 's1'  ? 'selected' : '' }}>S1</option>
                    <option value="s2"  {{ old('pendidikan') == 's2'  ? 'selected' : '' }}>S2</option>
                    <option value="s3"  {{ old('pendidikan') == 's3'  ? 'selected' : '' }}>S3</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jurusan">Program Studi / Jurusan</label>
                <input type="text" id="jurusan" name="jurusan" placeholder="Teknik Informatika" value="{{ old('jurusan') }}">
            </div>

            <div class="checkbox-group">
                <label class="check-label">
                    <input type="checkbox" id="agree" name="agree" required>
                    <span>Saya menyetujui <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> GradMatch</span>
                </label>
            </div>

            @if ($errors->any())
                <p class="error-msg">{{ $errors->first() }}</p>
            @else
                <p class="error-msg" id="errorMsg"></p>
            @endif

            <button type="submit">Buat Akun</button>

        </form>

        <p class="login-link">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

    </div>
</section>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/regis.js') }}"></script>
</body>
</html>