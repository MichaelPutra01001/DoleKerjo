// === skill matching JS ===

const chipsArea  = document.getElementById('skillChipsArea');
const skillInput = document.getElementById('skillInput');
const hiddenIn   = document.getElementById('skillsHidden');
const noHint     = document.getElementById('noSkillsHint');
const form       = document.getElementById('matchingForm');

// kumpulin semua chip skill terus masukin ke hidden input
function syncHidden() {
    const chips = chipsArea.querySelectorAll('.skill-chip');
    const names = Array.from(chips).map(c => c.dataset.skill);
    hiddenIn.value = names.join(',');
    // tampilkan atau sembunyikan hint kalau ga ada chip
    if (noHint) {
        noHint.style.display = chips.length > 0 ? 'none' : '';
    }
}

// bikin elemen chip baru
function createChip(name) {
    const span = document.createElement('span');
    span.className = 'skill-chip';
    span.dataset.skill = name;
    span.innerHTML = name +
        '<button type="button" class="chip-remove" aria-label="Hapus">&times;</button>';
    span.querySelector('.chip-remove').addEventListener('click', function() {
        removeChip(this);
    });
    return span;
}

// cek apakah skill sudah ada (case-insensitive)
function skillExists(name) {
    const lower = name.toLowerCase();
    const chips = chipsArea.querySelectorAll('.skill-chip');
    return Array.from(chips).some(c => c.dataset.skill.toLowerCase() === lower);
}

// tambah skill dari input, bisa dipisah koma
function addSkillFromInput() {
    const raw = skillInput.value.trim();
    if (!raw) return;

    // split berdasarkan koma
    const parts = raw.split(',').map(s => s.trim()).filter(s => s.length > 0);
    let added = 0;

    parts.forEach(name => {
        if (!skillExists(name)) {
            chipsArea.appendChild(createChip(name));
            added++;
        }
    });

    if (added > 0) {
        skillInput.value = '';
        syncHidden();
    }
}

// expose ke global biar bisa dipanggil dari onclick
window.addSkillFromInput = addSkillFromInput;

// hapus chip dengan animasi kecil
function removeChip(btn) {
    const chip = btn.closest('.skill-chip');
    if (chip) {
        chip.style.transform = 'scale(.8)';
        chip.style.opacity = '0';
        setTimeout(() => {
            chip.remove();
            syncHidden();
        }, 150);
    }
}
window.removeChip = removeChip;

// tekan Enter di input langsung nambahin skill
skillInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkillFromInput();
    }
});

// waktu form di-submit, pastiin hidden input terisi
form.addEventListener('submit', function(e) {
    syncHidden();

    // kalau ga ada skill sama sekali, blokir submit
    if (!hiddenIn.value.trim()) {
        e.preventDefault();
        skillInput.focus();
        skillInput.style.borderColor = '#CC1016';
        skillInput.placeholder = 'Tambahkan minimal 1 skill terlebih dahulu!';
        setTimeout(() => {
            skillInput.style.borderColor = '';
            skillInput.placeholder = 'Ketik skill lalu tekan Enter (cth: React, Node.js)';
        }, 2500);
        return;
    }
});

// sync hidden pas halaman pertama kali dibuka (skill dari profil udah ke-load)
syncHidden();

// tombol AI matching, tampilin loading spinner waktu disubmit
const aiForm = document.getElementById('aiForm');
const btnAI = document.getElementById('btnAI');
if (aiForm) {
    aiForm.addEventListener('submit', function() {
        if (btnAI) {
            btnAI.classList.add('loading');
            btnAI.disabled = true;
            btnAI.innerHTML = 'AI sedang menganalisis...';
        }
    });
}
