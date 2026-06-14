<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Rekruter - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/regis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/regis_recruiter.css') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body>

<div class="top-bar">
    <a href="{{ route('login') }}" class="brand">GradMatch</a>
    <p>Portal Registrasi Perusahaan Mitra & Rekruter</p>
    <div style="margin-left: auto;">
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>
</div>

<section class="register-container">
    <div class="register-card">

        <!-- Progress Steps Bar -->
        <div class="steps-progress-bar">
            <div class="step active" id="step-1">
                <div class="step-num">1</div>
                <div class="step-label">Akun Rekruter</div>
            </div>
            <div class="step-line" id="line-1"></div>
            <div class="step" id="step-2">
                <div class="step-num">2</div>
                <div class="step-label">Info Perusahaan</div>
            </div>
            <div class="step-line" id="line-2"></div>
            <div class="step" id="step-3">
                <div class="step-num">3</div>
                <div class="step-label">Verifikasi</div>
            </div>
        </div>

        <form action="/register/recruiter" method="POST" id="recruiterForm">
            @csrf

            <!-- STEP 1: Personal Recruiter Account -->
            <div class="form-step-panel" id="panel-step-1">
                <div class="card-header">
                    <h2>Informasi Akun Rekruter</h2>
                    <p class="sub">Lengkapi kredensial pribadi Anda untuk mengelola portal perusahaan</p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso" value="{{ old('nama') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Contoh: budis12" value="{{ old('username') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Alamat Email Kerja</label>
                        <input type="email" id="email" name="email" placeholder="Contoh: budi@perusahaan.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="telepon">No. Telepon Aktif</label>
                        <input type="text" id="telepon" name="telepon" placeholder="Contoh: +628123456789" value="{{ old('telepon') }}">
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

                <p class="error-msg" id="step1Error">
                    @if ($errors->any())
                        {{ $errors->first() }}
                    @endif
                </p>

                <button type="button" onclick="goToStep2()" class="btn-next">
                    Lanjut ke Detail Perusahaan →
                </button>
            </div>

            <!-- STEP 2: Company Details -->
            <div class="form-step-panel" id="panel-step-2" style="display: none;">
                <div class="card-header">
                    <h2>Informasi Perusahaan</h2>
                    <p class="sub">Tentukan detail profil perusahaan untuk ditampilkan pada direktori pencari kerja</p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_perusahaan">Nama Perusahaan Resmi</label>
                        <input type="text" id="nama_perusahaan" name="nama_perusahaan" placeholder="Contoh: PT Teknologi Maju" value="{{ old('nama_perusahaan') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="tipe_bisnis">Bidang Industri / Bisnis</label>
                        <input type="text" id="tipe_bisnis" name="tipe_bisnis" placeholder="Contoh: Teknologi, Finansial, F&B" value="{{ old('tipe_bisnis') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="lokasi">Lokasi Kantor Pusat</label>
                        <input type="text" id="lokasi_kantor" name="lokasi" placeholder="Contoh: Jakarta, Indonesia" value="{{ old('lokasi') }}">
                    </div>
                    <div class="form-group">
                        <label for="website">Website Perusahaan</label>
                        <input type="text" id="website" name="website" placeholder="Contoh: https://teknologimaju.com" value="{{ old('website') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ditemukan_tahun">Tahun Didirikan</label>
                    <input type="text" id="ditemukan_tahun" name="ditemukan_tahun" placeholder="Contoh: 2018" value="{{ old('ditemukan_tahun') }}">
                </div>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Singkat Perusahaan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat mengenai industri, budaya kerja, atau visi misi perusahaan..."></textarea>
                </div>

                <div class="checkbox-group" style="margin-top: 8px;">
                    <label class="check-label">
                        <input type="checkbox" id="agree" required>
                        <span>Saya menyatakan bahwa informasi perusahaan di atas adalah sah dan saya berwenang mewakilinya.</span>
                    </label>
                </div>

                <p class="error-msg" id="step2Error"></p>

                <div class="form-row" style="margin-top: 8px;">
                    <button type="button" onclick="goToStep1()" class="btn-back">
                        ← Kembali
                    </button>
                    <button type="submit" class="btn-submit">
                        Kirim Pendaftaran
                    </button>
                </div>
            </div>

        </form>

        <p class="login-link">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

    </div>
</section>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script>
    // Toggle Password Visibility
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁';
        }
    }

    // Client-side multi-step transitions
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const line1 = document.getElementById('line-1');
    
    const panel1 = document.getElementById('panel-step-1');
    const panel2 = document.getElementById('panel-step-2');
    
    const error1 = document.getElementById('step1Error');
    const error2 = document.getElementById('step2Error');

    function goToStep2() {
        // Simple front-end validation for Step 1
        const nama = document.getElementById('nama').value.trim();
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;

        if (!nama || !username || !email || !password || !confirm) {
            error1.textContent = 'Harap lengkapi semua kolom wajib diisi.';
            return;
        }
        if (password.length < 8) {
            error1.textContent = 'Password minimal harus 8 karakter.';
            return;
        }
        if (password !== confirm) {
            error1.textContent = 'Konfirmasi password tidak cocok.';
            return;
        }
        
        error1.textContent = '';
        
        // Update Progress Bar
        step1.classList.add('completed');
        step1.classList.remove('active');
        line1.classList.add('completed');
        step2.classList.add('active');

        // Swap Panel
        panel1.style.display = 'none';
        panel2.style.display = 'block';
    }

    function goToStep1() {
        // Update Progress Bar
        step1.classList.remove('completed');
        step1.classList.add('active');
        line1.classList.remove('completed');
        step2.classList.remove('active');

        // Swap Panel
        panel2.style.display = 'none';
        panel1.style.display = 'block';
    }

    // Submit Validation
    document.getElementById('recruiterForm').addEventListener('submit', function(e) {
        const namaPerusahaan = document.getElementById('nama_perusahaan').value.trim();
        const agree = document.getElementById('agree').checked;

        if (!namaPerusahaan) {
            e.preventDefault();
            error2.textContent = 'Nama perusahaan wajib diisi.';
            return;
        }
        if (!agree) {
            e.preventDefault();
            error2.textContent = 'Anda harus menyetujui pernyataan persetujuan wewenang perusahaan.';
            return;
        }
    });
</script>
</body>
</html>
