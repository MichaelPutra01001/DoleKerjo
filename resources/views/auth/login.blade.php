<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - DoleKerjo</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
    <h1>DoleKerjo</h1>
    <p>Platform Job Matching Berbasis Kompetensi</p>
    <div style="margin-left: auto;">
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>
</div>

<section class="login-container">
    <div class="login-card">
        <h2 id="loginTitle">Masuk ke Akun</h2>
        <p class="sub" id="loginSub">Selamat datang kembali</p>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="login_mode" id="loginMode" value="user">
            <input type="text" name="username" id="usernameInput" placeholder="Email atau Username"
                   value="{{ old('username') }}" required>
            <div class="input-wrap">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="toggle-pass" onclick="togglePass('password', this)" tabindex="-1">👁</button>
            </div>
            <div style="text-align: right; margin-top: -4px;">
                <a href="#" id="forgotPasswordLink" style="font-size: 13px; color: var(--blue); text-decoration: none; font-weight: 500;">Lupa password?</a>
            </div>

            @if ($errors->has('login'))
                <p class="error-msg">{{ $errors->first('login') }}</p>
            @else
                <p class="error-msg" id="errorMsg"></p>
            @endif

            <button type="submit" id="loginButton">Masuk Sebagai Pencari Kerja</button>
        </form>

        <p class="register" id="registerText">Belum punya akun? <a href="/register">Daftar</a></p>
        
        <div style="text-align: center; margin-top: 24px; border-top: 1px solid var(--border); padding-top: 16px;">
            <a href="#" id="roleToggleLink" onclick="toggleRole(event)" style="font-size: 13.5px; color: var(--blue); text-decoration: none; font-weight: 600;">Masuk sebagai Eksekutif (Recruiter/Admin) →</a>
        </div>
    </div>
<!-- ── Modal Lupa Password ── -->
<div id="forgotPasswordModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;backdrop-filter:blur(3px);">
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;padding:32px;max-width:420px;width:90%;position:relative;color:var(--text);box-shadow:var(--shadow);">
        <button type="button" onclick="closeForgotPassword()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-3);line-height:1;">✕</button>
        
        <div id="stepEmail">
            <h3 style="font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;margin-bottom:8px;">Lupa Password</h3>
            <p style="font-size:13.5px;color:var(--text-3);margin-bottom:20px;">Masukkan email akun kamu untuk mereset password.</p>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <input type="email" id="forgotEmail" placeholder="Masukkan email kamu" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;background:var(--white);color:var(--text);outline:none;">
                <p id="emailError" style="font-size:13px;color:#CC1016;margin:0;min-height:18px;"></p>
                <button type="button" onclick="handleCheckEmail()" style="width:100%;padding:12px;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;margin-top:4px;">Lanjut</button>
            </div>
        </div>

        <div id="stepReset" style="display:none;">
            <h3 style="font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;margin-bottom:8px;">Reset Password</h3>
            <p style="font-size:13.5px;color:var(--text-3);margin-bottom:20px;">Masukkan kode OTP (dekorasi) dan buat password baru kamu.</p>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:12px;font-weight:600;color:var(--text-2);">Kode OTP (Dekorasi)</label>
                    <input type="text" id="otpCode" placeholder="Masukkan 6 digit kode OTP" value="123456" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;background:var(--white);color:var(--text);outline:none;">
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:12px;font-weight:600;color:var(--text-2);">Password Baru</label>
                    <input type="password" id="forgotNewPassword" placeholder="Min. 8 karakter" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;background:var(--white);color:var(--text);outline:none;">
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:12px;font-weight:600;color:var(--text-2);">Konfirmasi Password Baru</label>
                    <input type="password" id="forgotConfirmPassword" placeholder="Ulangi password baru" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;background:var(--white);color:var(--text);outline:none;">
                </div>
                <p id="resetError" style="font-size:13px;color:#CC1016;margin:0;min-height:18px;"></p>
                <button type="button" onclick="handleResetPassword()" style="width:100%;padding:12px;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;margin-top:4px;">Simpan Password Baru</button>
            </div>
        </div>
    </div>
</div>

</section>

<footer><p>© 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>