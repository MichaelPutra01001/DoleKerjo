// ambil csrf token buat semua request POST
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ganti tab yang aktif
function switchTab(el, tabId) {
    document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
}

// tampilin pesan sukses atau error
function showMsg(elId, text, type) {
    const el = document.getElementById(elId);
    if (!el) return;
    el.textContent = text;
    el.className = 'save-msg ' + type;
    setTimeout(() => { el.textContent = ''; el.className = 'save-msg'; }, 3000);
}

// konfirmasi hapus akun
function confirmDelete() {
    if (confirm('Apakah kamu yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) {
        fetch('/profil/hapus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken
            },
            body: 'action=hapus'
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = '/login';
            } else {
                alert(data.message || 'Gagal menghapus akun.');
            }
        })
        .catch(() => alert('Terjadi kesalahan. Coba lagi.'));
    }
}

// map kode pendidikan ke label yang keliatan di UI
const pendidikanLabel = {
    sma: 'SMA / SMK',
    d3:  'D3',
    s1:  'S1',
    s2:  'S2',
    s3:  'S3',
};

// map status lamaran ke class css-nya
const statusClass = {
    review:    'review',
    interview: 'interview',
    pending:   'pending',
    diterima:  'interview',
    ditolak:   'pending',
};

// ── Render data ke halaman ──
function renderProfil(data) {
    const u = data.user;

    // Sidebar
    document.getElementById('sidebarNama').textContent = u.nama || '—';

    // Avatar
    const avatarEl = document.getElementById('sidebarAvatar');
    if (u.foto_profil) {
        avatarEl.innerHTML = '';
        avatarEl.style.backgroundImage = `url('/${u.foto_profil}')`;
        avatarEl.style.backgroundSize = 'cover';
        avatarEl.style.backgroundPosition = 'center';
    } else {
        avatarEl.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        avatarEl.style.backgroundImage = '';
    }

    // Detail Profil
    document.getElementById('detailNama').textContent       = u.nama       || '—';
    document.getElementById('detailEmail').textContent      = u.email      || '—';
    document.getElementById('detailTelepon').textContent    = u.telepon    || '—';
    document.getElementById('detailLokasi').textContent     = u.lokasi     || '—';
    document.getElementById('detailPendidikan').textContent = pendidikanLabel[u.pendidikan] || u.pendidikan || '—';
    document.getElementById('detailJurusan').textContent    = u.jurusan    || '—';
    document.getElementById('detailBio').textContent        = u.bio        || 'Belum ada deskripsi.';

    // Email verification state
    const verifyRow = document.getElementById('emailVerifyRow');
    const badge = document.getElementById('emailBadge');
    const btnVerify = document.getElementById('btnVerifyEmail');
    if (u.email_verified == 1) {
        verifyRow.style.display = 'flex';
        badge.textContent = 'Terverifikasi';
        badge.className = 'email-badge verified';
        btnVerify.style.display = 'none';
    } else if (u.email_verified == 2) {
        // Pending — waiting admin approval
        verifyRow.style.display = 'flex';
        badge.textContent = 'Menunggu verifikasi admin';
        badge.className = 'email-badge pending';
        btnVerify.style.display = 'none';
    } else {
        verifyRow.style.display = 'flex';
        badge.textContent = 'Belum diverifikasi';
        badge.className = 'email-badge unverified';
        btnVerify.style.display = '';
    }

    // Skills
    renderSkills(data.skills);

    // Isi form pengaturan (pre-fill)
    document.getElementById('set-nama').value   = u.nama    || '';
    document.getElementById('set-email').value  = u.email   || '';
    document.getElementById('set-telp').value   = u.telepon || '';
    document.getElementById('set-lokasi').value = u.lokasi  || '';
    document.getElementById('set-bio').value    = u.bio     || '';

    // Riwayat Lamaran (Progress Timeline)
    const lamaranList = document.getElementById('lamaranList');
    if (data.lamaran && data.lamaran.length > 0) {
        lamaranList.innerHTML = data.lamaran.map(l => {
            const tgl = new Date(l.created_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            });
            const updatedTgl = l.updated_at ? new Date(l.updated_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            }) : '';

            // Determine stage index: 0=Dilamar, 1=Review, 2=Interview, 3=Final
            const stages = ['dilamar', 'review', 'interview', 'final'];
            const statusOrder = { pending: 0, review: 1, interview: 2, diterima: 3, ditolak: 3 };
            const currentIdx = statusOrder[l.status] ?? 0;
            const isRejected = l.status === 'ditolak';

            // Build pipeline steps
            let pipelineHTML = '';
            stages.forEach((stage, i) => {
                let label = stage.charAt(0).toUpperCase() + stage.slice(1);
                if (i === 3) label = isRejected ? 'Ditolak' : 'Diterima';
                const active = i <= currentIdx;
                const current = i === currentIdx;
                const cls = active ? (isRejected && i === 3 ? 'step-rejected' : 'step-active') : 'step-inactive';
                const dotCls = current ? ' step-current' : '';
                pipelineHTML += `<div class="pipeline-step ${cls}${dotCls}">
                    <div class="pipeline-dot"></div>
                    <span>${label}</span>
                </div>`;
                // Add connector line between steps (3 lines total for 4 steps)
                if (i < stages.length - 1) {
                    const lineActive = i < currentIdx ? ' line-active' : '';
                    pipelineHTML += `<div class="pipeline-line${lineActive}"></div>`;
                }
            });

            // Catatan (recruiter notes)
            const catatanHTML = l.catatan
                ? `<div class="lamaran-catatan"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><span>${l.catatan}</span></div>`
                : '';

            return `
                <div class="lamaran-card">
                    <div class="lamaran-card-header">
                        <div>
                            <strong>${l.nama_posisi}</strong>
                            <p>${l.nama_perusahaan} &nbsp;&middot;&nbsp; Dilamar ${tgl}</p>
                        </div>
                    </div>
                    <div class="pipeline-wrap">${pipelineHTML}</div>
                    ${updatedTgl && currentIdx > 0 ? `<p class="lamaran-updated">Terakhir diperbarui: ${updatedTgl}</p>` : ''}
                    ${catatanHTML}
                </div>`;
        }).join('');
    } else {
        lamaranList.innerHTML = '<p style="font-size:14px;color:var(--text-3)">Belum ada riwayat lamaran.</p>';
    }

    // ── Profile Completion Tracker ──
    if (data.steps) {
        renderCompletion(data.steps);
    }

    // ── CV state ──
    renderCVState(u.cv);

    // ── Load available skills for dropdown ──
    loadSkillsList();
}

// ── Fetch data profil dari server ──
function loadProfil() {
    fetch('/profil/data')
        .then(r => {
            if (r.status === 401) {
                window.location.href = '/login';
                return null;
            }
            return r.json();
        })
        .then(data => {
            if (!data) return;
            if (data.status === 'success') {
                renderProfil(data);
            } else {
                console.error('Profil error:', data.message);
            }
        })
        .catch(err => console.error('Fetch profil gagal:', err));
}

// ── Submit form Info Pribadi ──
document.getElementById('formInfo').addEventListener('submit', function(e) {
    e.preventDefault();
    const body = new URLSearchParams(new FormData(this)).toString();
    fetch('/profil/update-info', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body
    })
    .then(r => r.json())
    .then(data => {
        showMsg('msg-info', data.message, data.status === 'success' ? 'success' : 'error');
        if (data.status === 'success') loadProfil();
    })
    .catch(() => showMsg('msg-info', 'Terjadi kesalahan.', 'error'));
});

// ── Submit form Password ──
document.getElementById('formPassword').addEventListener('submit', function(e) {
    e.preventDefault();
    const np = document.getElementById('set-newpass').value;
    const cp = document.getElementById('set-confirmpass').value;
    if (np !== cp) {
        showMsg('msg-password', 'Konfirmasi password tidak cocok.', 'error');
        return;
    }
    const body = new URLSearchParams(new FormData(this)).toString();
    fetch('/profil/update-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body
    })
    .then(r => r.json())
    .then(data => {
        showMsg('msg-password', data.message, data.status === 'success' ? 'success' : 'error');
        if (data.status === 'success') this.reset();
    })
    .catch(() => showMsg('msg-password', 'Terjadi kesalahan.', 'error'));
});

// ── Submit form Notifikasi ──
document.getElementById('formNotif').addEventListener('submit', function(e) {
    e.preventDefault();
    showMsg('msg-notif', 'Preferensi disimpan.', 'success');
});

// ── Jalankan saat halaman dibuka ──
loadProfil();

// ══════════════════════════════════════════════════════════
//  PROFILE COMPLETION
// ══════════════════════════════════════════════════════════

// Map step id → action to navigate user
const stepActions = {
    foto:  () => document.getElementById('avatarWrap').click(),
    info:  () => { switchToPengaturan(); focusField('set-nama'); },
    bio:   () => { switchToPengaturan(); focusField('set-bio'); },
    cv:    () => { switchToPengaturan(); scrollToElement('cvUploadZone'); },
    skill: () => { switchToDetail(); scrollToElement('detailSkills'); },
    email: () => { switchToDetail(); scrollToElement('emailVerifyRow'); },
};

function switchToPengaturan() {
    const li = document.querySelectorAll('.sidebar li')[1]; // Pengaturan Akun
    switchTab(li, 'pengaturan');
}
function switchToDetail() {
    const li = document.querySelectorAll('.sidebar li')[0]; // Detail Profil
    switchTab(li, 'detail');
}
function focusField(id) {
    setTimeout(() => {
        const el = document.getElementById(id);
        if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    }, 200);
}
function scrollToElement(id) {
    setTimeout(() => {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 200);
}

function renderCompletion(steps) {
    const bar     = document.getElementById('completionBar');
    const percent = document.getElementById('completionPercent');
    const sub     = document.getElementById('completionSub');
    const stepsEl = document.getElementById('completionSteps');

    if (!bar || !percent || !stepsEl) return;

    // Animate bar
    setTimeout(() => { bar.style.width = steps.percent + '%'; }, 100);
    percent.textContent = steps.percent + '%';
    sub.textContent = steps.done + ' dari ' + steps.total + ' langkah selesai';

    // Render steps
    const checkSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
    const circleSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle></svg>';

    stepsEl.innerHTML = steps.items.map(s => `
        <div class="step-item ${s.done ? 'done' : 'clickable'}" data-step="${s.id}" onclick="onStepClick('${s.id}', ${s.done})">
            <span class="step-icon">${s.done ? checkSVG : circleSVG}</span>
            <span class="step-label">${s.label}</span>
            ${!s.done ? '<span class="step-arrow">→</span>' : ''}
        </div>
    `).join('');
}

function onStepClick(stepId, done) {
    if (done) return;
    const action = stepActions[stepId];
    if (action) action();
}

// ══════════════════════════════════════════════════════════
//  CV UPLOAD
// ══════════════════════════════════════════════════════════

function renderCVState(cvPath) {
    const dropArea  = document.getElementById('cvDropArea');
    const uploaded  = document.getElementById('cvUploaded');
    if (!dropArea || !uploaded) return;

    if (cvPath) {
        dropArea.style.display = 'none';
        uploaded.style.display = 'flex';
        const fileName = cvPath.split('/').pop();
        document.getElementById('cvFileName').textContent = fileName;
        document.getElementById('cvDownloadLink').href = '/' + cvPath;
    } else {
        dropArea.style.display = '';
        uploaded.style.display = 'none';
    }
}

// File input change
const cvInput = document.getElementById('cvFileInput');
if (cvInput) {
    cvInput.addEventListener('change', function() {
        if (this.files.length > 0) uploadCV(this.files[0]);
    });
}

// Drag & drop
const cvDrop = document.getElementById('cvDropArea');
if (cvDrop) {
    ['dragenter', 'dragover'].forEach(evt => {
        cvDrop.addEventListener(evt, e => { e.preventDefault(); cvDrop.classList.add('drag-over'); });
    });
    ['dragleave', 'drop'].forEach(evt => {
        cvDrop.addEventListener(evt, e => { e.preventDefault(); cvDrop.classList.remove('drag-over'); });
    });
    cvDrop.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files.length > 0) uploadCV(files[0]);
    });
}

function uploadCV(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['pdf', 'docx', 'doc'].includes(ext)) {
        alert('Format file harus PDF, DOCX, atau DOC.');
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file maksimal 5 MB.');
        return;
    }

    const formData = new FormData();
    formData.append('cv', file);

    fetch('/profil/upload-cv', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            renderCVState(data.path);
            loadProfil(); // reload to update completion
        } else {
            alert(data.message || 'Gagal mengunggah CV.');
        }
    })
    .catch(() => alert('Terjadi kesalahan. Coba lagi.'));
}

// ── Delete CV ──
function deleteCV() {
    if (!confirm('Hapus CV yang sudah diunggah?')) return;

    fetch('/profil/delete-cv', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            renderCVState(null);
            loadProfil();
        } else {
            alert(data.message || 'Gagal menghapus CV.');
        }
    })
    .catch(() => alert('Terjadi kesalahan. Coba lagi.'));
}
window.deleteCV = deleteCV;

// ── Replace CV (file input change) ──
const cvReplaceInput = document.getElementById('cvReplaceInput');
if (cvReplaceInput) {
    cvReplaceInput.addEventListener('change', function() {
        if (this.files.length > 0) uploadCV(this.files[0]);
        this.value = ''; // reset so same file can be picked again
    });
}

// ══════════════════════════════════════════════════════════
//  SKILLS MANAGEMENT
// ══════════════════════════════════════════════════════════

const levelLabel = { pemula: 'Pemula', menengah: 'Menengah', mahir: 'Mahir' };

function renderSkills(skills) {
    const el = document.getElementById('detailSkills');
    if (!el) return;
    if (skills && skills.length > 0) {
        el.innerHTML = skills.map(s => `
            <span class="skill-chip">
                ${s.nama} · ${levelLabel[s.level] || s.level}
                <button type="button" class="skill-remove" onclick="removeSkill(${s.skill_id})" title="Hapus skill">&times;</button>
            </span>
        `).join('');
    } else {
        el.innerHTML = '<span style="color:var(--text-3);font-size:13px">Belum ada skill yang ditambahkan.</span>';
    }
}

function loadSkillsList() {
    fetch('/profil/skills-list')
        .then(r => r.json())
        .then(data => {
            if (data.status !== 'success') return;
            const sel = document.getElementById('skillSelect');
            if (!sel) return;
            sel.innerHTML = '<option value="">Pilih skill...</option>';
            let lastKat = '';
            data.skills.forEach(s => {
                if (s.kategori !== lastKat) {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = s.kategori.charAt(0).toUpperCase() + s.kategori.slice(1);
                    sel.appendChild(optgroup);
                    lastKat = s.kategori;
                }
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.nama;
                // Append to last optgroup
                const groups = sel.querySelectorAll('optgroup');
                groups[groups.length - 1].appendChild(opt);
            });
        })
        .catch(() => {});
}

function addSkill() {
    const sel   = document.getElementById('skillSelect');
    const level = document.getElementById('skillLevel');
    if (!sel.value) {
        showMsg('msg-skill', 'Pilih skill terlebih dahulu.', 'error');
        return;
    }
    fetch('/profil/add-skill', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken
        },
        body: `skill_id=${sel.value}&level=${level.value}`
    })
    .then(r => r.json())
    .then(data => {
        showMsg('msg-skill', data.message, data.status === 'success' ? 'success' : 'error');
        if (data.status === 'success') {
            sel.value = '';
            loadProfil();
        }
    })
    .catch(() => showMsg('msg-skill', 'Terjadi kesalahan.', 'error'));
}

function removeSkill(skillId) {
    if (!confirm('Hapus skill ini?')) return;
    fetch(`/profil/remove-skill/${skillId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') loadProfil();
    })
    .catch(() => alert('Terjadi kesalahan.'));
}

// ══════════════════════════════════════════════════════════
//  EMAIL VERIFICATION
// ══════════════════════════════════════════════════════════

function verifyEmail() {
    const btn = document.getElementById('btnVerifyEmail');
    btn.disabled = true;
    btn.textContent = 'Memverifikasi...';

    fetch('/profil/verify-email', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            loadProfil();
        } else {
            alert(data.message || 'Gagal memverifikasi email.');
            btn.disabled = false;
            btn.textContent = 'Verifikasi Email';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan.');
        btn.disabled = false;
        btn.textContent = 'Verifikasi Email';
    });
}

// ══════════════════════════════════════════════════════════
//  PHOTO UPLOAD
// ══════════════════════════════════════════════════════════

const avatarWrap = document.getElementById('avatarWrap');
const photoInput = document.getElementById('photoFileInput');

if (avatarWrap && photoInput) {
    avatarWrap.addEventListener('click', () => photoInput.click());
    photoInput.addEventListener('change', function() {
        if (this.files.length > 0) uploadPhoto(this.files[0]);
    });
}

function uploadPhoto(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
        alert('Format file harus JPG, PNG, atau WEBP.');
        return;
    }
    if (file.size > 3 * 1024 * 1024) {
        alert('Ukuran file maksimal 3 MB.');
        return;
    }

    const formData = new FormData();
    formData.append('foto', file);

    fetch('/profil/upload-photo', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            loadProfil();
        } else {
            alert(data.message || 'Gagal mengunggah foto.');
        }
    })
    .catch(() => alert('Terjadi kesalahan. Coba lagi.'));
}