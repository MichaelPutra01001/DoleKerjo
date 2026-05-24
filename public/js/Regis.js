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

// ── Client-side Validation ──
document.getElementById('formRegister').addEventListener('submit', function(e) {
    const err      = document.getElementById('errorMsg');
    if (!err) return; // kalau error sudah ditampilkan Laravel, skip
    err.textContent = '';

    const nama    = document.getElementById('nama').value.trim();
    const email   = document.getElementById('email').value.trim();
    const pass    = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const agree   = document.getElementById('agree').checked;

    if (!nama || !email || !pass || !confirm) {
        e.preventDefault();
        err.textContent = 'Harap lengkapi semua kolom yang wajib diisi.';
        return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        err.textContent = 'Format email tidak valid.';
        return;
    }
    if (pass.length < 8) {
        e.preventDefault();
        err.textContent = 'Password minimal 8 karakter.';
        return;
    }
    if (pass !== confirm) {
        e.preventDefault();
        err.textContent = 'Konfirmasi password tidak cocok.';
        return;
    }
    if (!agree) {
        e.preventDefault();
        err.textContent = 'Kamu harus menyetujui syarat & ketentuan.';
        return;
    }
});