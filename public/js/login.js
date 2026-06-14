// ── Toggle Password Visibility ──
function togglePass(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '⌣';
    } else {
        input.type = 'password';
        btn.textContent = '👁';
    }
}

function handleLogin(e) {
    e.preventDefault();
    const user = document.getElementById('username').value.trim();
    const pass = document.getElementById('password').value.trim();
    const err  = document.getElementById('errorMsg');
    if (!user || !pass) { err.textContent = 'Email dan password tidak boleh kosong.'; return; }
    err.textContent = '';
}
// TAMPILKAN ERROR DARI PHP
const params = new URLSearchParams(window.location.search);
const error = params.get('error');

if (error) {
    document.getElementById('errorMsg').textContent = error;
    window.history.replaceState({}, document.title, "Login.html");
}

// ── Forgot Password Logic ──
const forgotModal = document.getElementById('forgotPasswordModal');
const stepEmail = document.getElementById('stepEmail');
const stepReset = document.getElementById('stepReset');
const forgotEmailInput = document.getElementById('forgotEmail');
const emailError = document.getElementById('emailError');
const resetError = document.getElementById('resetError');

document.getElementById('forgotPasswordLink')?.addEventListener('click', (e) => {
    e.preventDefault();
    forgotModal.style.display = 'flex';
    stepEmail.style.display = 'block';
    stepReset.style.display = 'none';
    forgotEmailInput.value = '';
    emailError.textContent = '';
    resetError.textContent = '';
});

function closeForgotPassword() {
    forgotModal.style.display = 'none';
}

function handleCheckEmail() {
    const email = forgotEmailInput.value.trim();
    if (!email) {
        emailError.textContent = 'Harap isi email terlebih dahulu.';
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/forgot-password/check-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ email })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            emailError.textContent = '';
            stepEmail.style.display = 'none';
            stepReset.style.display = 'block';
        } else {
            emailError.textContent = data.message;
        }
    })
    .catch(() => {
        emailError.textContent = 'Terjadi kesalahan sistem.';
    });
}

function handleResetPassword() {
    const email = forgotEmailInput.value.trim();
    const password = document.getElementById('forgotNewPassword').value;
    const confirm_password = document.getElementById('forgotConfirmPassword').value;
    
    if (!password || !confirm_password) {
        resetError.textContent = 'Harap isi semua kolom password.';
        return;
    }
    if (password.length < 8) {
        resetError.textContent = 'Password minimal 8 karakter.';
        return;
    }
    if (password !== confirm_password) {
        resetError.textContent = 'Konfirmasi password tidak cocok.';
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/forgot-password/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ email, password, confirm_password })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            closeForgotPassword();
        } else {
            resetError.textContent = data.message;
        }
    })
    .catch(() => {
        resetError.textContent = 'Terjadi kesalahan sistem.';
    });
}

// ── Role Switcher Logic ──
let currentRole = 'user';

function toggleRole(e) {
    if (e) e.preventDefault();
    
    currentRole = currentRole === 'user' ? 'executive' : 'user';
    
    const loginTitle = document.getElementById('loginTitle');
    const loginSub = document.getElementById('loginSub');
    const usernameInput = document.getElementById('usernameInput');
    const loginButton = document.getElementById('loginButton');
    const registerText = document.getElementById('registerText');
    const roleToggleLink = document.getElementById('roleToggleLink');

    if (currentRole === 'user') {
        if (loginTitle) loginTitle.textContent = 'Masuk ke Akun';
        if (loginSub) loginSub.textContent = 'Selamat datang kembali pencari kerja';
        if (usernameInput) usernameInput.placeholder = 'Email atau Username';
        if (loginButton) loginButton.textContent = 'Masuk Sebagai Pencari Kerja';
        if (registerText) registerText.innerHTML = 'Belum punya akun? <a href="/register">Daftar</a>';
        if (roleToggleLink) roleToggleLink.textContent = 'Masuk sebagai Eksekutif (Recruiter/Admin) →';
    } else {
        if (loginTitle) loginTitle.textContent = 'Executive Portal';
        if (loginSub) loginSub.textContent = 'Akses khusus Rekruter & Admin';
        if (usernameInput) usernameInput.placeholder = 'Username atau Email Executive';
        if (loginButton) loginButton.textContent = 'Masuk Sebagai Eksekutif';
        if (registerText) registerText.innerHTML = 'Ingin bermitra sebagai Perusahaan? <a href="/register/recruiter" style="color:var(--blue);font-weight:500;">Daftar Rekruter</a>';
        if (roleToggleLink) roleToggleLink.textContent = '← Masuk sebagai Pencari Kerja';
    }
}