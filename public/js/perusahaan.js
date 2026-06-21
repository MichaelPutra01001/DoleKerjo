/* ============================================================
   perusahaan.js - DoleKerjo Company Pages
   ============================================================ */

const PERUSAHAAN_ID   = window.PERUSAHAAN_ID   || null;
const PERUSAHAAN_NAMA = window.PERUSAHAAN_NAMA || '';
const USER_ROLE       = window.USER_ROLE       || 'user';
const USER_ID         = window.USER_ID         || null;

// CSRF token helper
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Format rupiah
function formatRupiah(n) {
    if (!n) return '-';
    if (n >= 1_000_000) return 'Rp ' + (n / 1_000_000).toFixed(0) + ' Jt';
    if (n >= 1_000) return 'Rp ' + (n / 1_000).toFixed(0) + ' Rb';
    return 'Rp ' + n;
}

// Inline SVG icons
const ICO = {
    globe:   '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
    pin:     '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
    user:    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    brief:   '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
    warn:    '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    doc:     '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
    chat:    '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    clip:    '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>',
    link:    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
    pinSm:   '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
    money:   '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
    users:   '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    starF:   '<svg class="star filled" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    starE:   '<svg class="star" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    starPk:  '<svg class="star-pick" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
};

// Render bintang
function renderStars(rating) {
    let html = '<span class="stars">';
    for (let i = 1; i <= 5; i++) {
        html += rating >= i ? ICO.starF : ICO.starE;
    }
    html += '</span>';
    return html;
}

// Inisial nama
function initials(name) {
    if (!name) return '?';
    const parts = name.trim().split(' ');
    if (parts.length === 1) return parts[0][0].toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

// Format tanggal
function formatDate(str) {
    if (!str) return '-';
    const d = new Date(str);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Skeleton loader
function skeletonHTML(rows = 3) {
    let h = '<div class="skeleton-wrap">';
    for (let i = 0; i < rows; i++) {
        const h_px = i === 0 ? 160 : 80;
        const op   = 1 - i * 0.2;
        h += `<div class="skeleton" style="height:${h_px}px;opacity:${op}"></div>`;
    }
    return h + '</div>';
}

/* ============================================================
   INDEX PAGE - Search & Reveal
   ============================================================ */

if (document.getElementById('companyGrid')) {
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.company-card');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            cards.forEach(card => {
                const nama   = (card.dataset.nama   || '').toLowerCase();
                const lokasi = (card.dataset.lokasi || '').toLowerCase();
                const match  = nama.includes(q) || lokasi.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const empty = document.getElementById('emptyState');
            if (empty) empty.style.display = visible === 0 ? '' : 'none';
        });
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.06 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

/* ============================================================
   DETAIL PAGE - Tab switching & AJAX data loading
   ============================================================ */

const loaded = {};

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const target = this.dataset.tab;

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('panel-' + target).classList.add('active');

        // Reload reviews every time to reflect new submissions
        if ((!loaded[target] || target === 'reviews') && PERUSAHAAN_ID) {
            loadTab(target);
        }
    });
});

if (PERUSAHAAN_ID) {
    loadTab('overview');
}

function loadTab(tab) {
    const panel = document.getElementById('panel-' + tab);
    if (!panel) return;
    panel.innerHTML = skeletonHTML(4);

    const endpoint = tab === 'connections' ? 'connections' : tab;
    fetch('/perusahaan/' + PERUSAHAAN_ID + '/' + endpoint)
        .then(r => r.json())
        .then(data => {
            loaded[tab] = true;
            renderTab(tab, data, panel);
        })
        .catch(() => {
            panel.innerHTML = `
            <div class="no-data">
                <div class="icon">${ICO.warn}</div>
                <p>Gagal memuat data. Coba refresh halaman.</p>
            </div>`;
        });
}

function renderTab(tab, data, panel) {
    switch (tab) {
        case 'overview':    renderOverview(data, panel);    break;
        case 'reviews':     renderReviews(data, panel);     break;
        case 'lamaran':     renderLamaran(data, panel);     break;
        case 'connections': renderConnections(data, panel); break;
    }
}

/* ---------- Overview ---------- */
function renderOverview(data, panel) {
    const p  = data.perusahaan;
    const rs = data.review_stats;
    const rr = data.recent_reviews || [];
    const totalJobs = data.total_jobs || 0;
    const avg   = rs ? (parseFloat(rs.avg_rating) || 0) : 0;
    const total = rs ? (parseInt(rs.total) || 0) : 0;

    let html = `<div class="overview-grid">`;

    // Update tab counts from overview data
    const reviewBadge = document.querySelector('[data-tab="reviews"] .tab-count');
    if (reviewBadge) reviewBadge.textContent = total;
    const lamaranBadge = document.querySelector('[data-tab="lamaran"] .tab-count');
    if (lamaranBadge) lamaranBadge.textContent = totalJobs;

    // Left: info umum
    html += `
    <div class="info-card reveal visible">
        <h3>Informasi Umum</h3>
        <div class="info-list">
            <div class="info-row">
                <div class="ico">${ICO.globe}</div>
                <div class="txt">
                    <div class="lbl">Website</div>
                    <div class="val">${p.website
                        ? `<a href="${p.website}" target="_blank">${p.website}</a>`
                        : '<span style="color:var(--text-3)">-</span>'
                    }</div>
                </div>
            </div>
            <div class="info-row">
                <div class="ico">${ICO.pin}</div>
                <div class="txt">
                    <div class="lbl">Lokasi</div>
                    <div class="val">${p.lokasi || '-'}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="ico">${ICO.user}</div>
                <div class="txt">
                    <div class="lbl">Recruiter</div>
                    <div class="val">${p.recruiter_nama || '-'}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="ico">${ICO.brief}</div>
                <div class="txt">
                    <div class="lbl">Lowongan Aktif</div>
                    <div class="val">${totalJobs} posisi</div>
                </div>
            </div>
        </div>
    </div>`;

    // Right: review summary
    html += `<div class="info-card reveal visible"><h3>Ringkasan Review</h3>`;
    if (total > 0) {
        html += `
        <div class="rating-summary">
            <div class="big-rating">
                <div class="num">${avg.toFixed(1)}</div>
                <div class="stars-row">${renderStars(avg)}</div>
                <div class="count">${total} review</div>
            </div>
            <div class="rating-bars">
                ${[5,4,3,2,1].map(n => {
                    const cnt = parseInt(rs['bintang'+n]) || 0;
                    const pct = Math.round((cnt / total) * 100);
                    return `<div class="bar-row">
                        <span class="lbl">${n}</span>
                        <div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div>
                        <span class="cnt">${cnt}</span>
                    </div>`;
                }).join('')}
            </div>
        </div>`;
    } else {
        html += `<div class="no-data" style="padding:24px 0"><div class="icon">${ICO.doc}</div><p>Belum ada review</p></div>`;
    }
    html += `</div></div>`;

    // Description
    if (p.deskripsi) {
        html += `
        <div class="info-card reveal visible" style="margin-bottom:14px">
            <h3>Tentang Perusahaan</h3>
            <p style="font-size:14px;color:var(--text-2);line-height:1.8;margin-top:8px">${p.deskripsi}</p>
        </div>`;
    }

    // Recent reviews
    if (rr.length > 0) {
        html += `<div class="section-title">Review Terbaru</div><div class="recent-reviews">`;
        rr.forEach(r => {
            html += `
            <div class="mini-review reveal visible">
                <div class="top">
                    <div>
                        <span class="reviewer">${r.reviewer || 'Anonim'}</span>
                        ${r.posisi_user ? `<span class="pos"> &middot; ${r.posisi_user}</span>` : ''}
                    </div>
                    ${renderStars(r.rating)}
                </div>
                <div class="txt">${r.isi_review || ''}</div>
            </div>`;
        });
        html += `
        <a href="#" onclick="switchTab('reviews');return false;"
           style="font-size:13px;color:var(--blue);text-decoration:none;display:inline-block;margin-top:4px;font-weight:500">
            Lihat semua review &rarr;
        </a>
        </div>`;
    }

    panel.innerHTML = html;
}

/* ---------- Reviews ---------- */
function renderReviews(data, panel) {
    let html = '';

    // Review form (only for regular users)
    if (USER_ROLE === 'user' && USER_ID) {
        html += `
        <div class="review-form-card" id="reviewFormCard">
            <h3 class="review-form-title">Tulis Review Anda</h3>
            <p class="review-form-sub">Bagikan pengalaman Anda tentang perusahaan ini</p>
            <form id="reviewForm" onsubmit="submitReview(event)">
                <div class="star-picker" id="starPicker">
                    <span class="star-pick" data-val="1" onclick="setRating(1)">${ICO.starPk}</span>
                    <span class="star-pick" data-val="2" onclick="setRating(2)">${ICO.starPk}</span>
                    <span class="star-pick" data-val="3" onclick="setRating(3)">${ICO.starPk}</span>
                    <span class="star-pick" data-val="4" onclick="setRating(4)">${ICO.starPk}</span>
                    <span class="star-pick" data-val="5" onclick="setRating(5)">${ICO.starPk}</span>
                    <span class="rating-label" id="ratingLabel">Pilih rating</span>
                </div>
                <input type="hidden" id="ratingValue" name="rating" value="0">
                <div class="form-group">
                    <label>Posisi Anda <span class="optional">(opsional)</span></label>
                    <input type="text" id="reviewPosisi" name="posisi_user" placeholder="cth: Software Engineer" maxlength="100">
                </div>
                <div class="form-group">
                    <label>Review Anda <span class="required">*</span></label>
                    <textarea id="reviewIsi" name="isi_review" rows="4" placeholder="Ceritakan pengalaman kerja, budaya perusahaan, prosesi interview, dll." required></textarea>
                </div>
                <button type="submit" class="btn-submit-review" id="btnSubmitReview">Kirim Review</button>
                <div class="form-msg" id="reviewFormMsg" style="display:none"></div>
            </form>
        </div>`;
    }

    if (!data || data.length === 0) {
        html += '<div class="no-data"><div class="icon">' + ICO.chat + '</div><p>Belum ada review untuk perusahaan ini.</p></div>';
    } else {
        html += '<div class="reviews-list">';
        data.forEach(r => {
            html += `
            <div class="review-card-full reveal visible">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">${initials(r.reviewer_nama)}</div>
                        <div>
                            <div class="reviewer-name">${r.reviewer_nama || 'Anonim'}</div>
                            <div class="reviewer-pos">${r.posisi_user || 'Karyawan'}</div>
                        </div>
                    </div>
                    <div class="review-date">${formatDate(r.created_at)}</div>
                </div>
                <div class="review-stars">${renderStars(r.rating)}</div>
                <div class="review-body">"${r.isi_review || ''}"</div>
            </div>`;
        });
        html += '</div>';
    }
    panel.innerHTML = html;

    const badge = document.querySelector('[data-tab="reviews"] .tab-count');
    if (badge) badge.textContent = data ? data.length : 0;
}

// Star rating picker
let currentRating = 0;
const ratingLabels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];

function setRating(val) {
    currentRating = val;
    document.getElementById('ratingValue').value = val;
    document.getElementById('ratingLabel').textContent = ratingLabels[val] || '';
    document.querySelectorAll('.star-pick').forEach(s => {
        s.classList.toggle('active', parseInt(s.dataset.val) <= val);
    });
}

// Submit review
function submitReview(e) {
    e.preventDefault();
    const rating = parseInt(document.getElementById('ratingValue').value);
    const posisi = document.getElementById('reviewPosisi').value.trim();
    const isi    = document.getElementById('reviewIsi').value.trim();
    const msgEl  = document.getElementById('reviewFormMsg');
    const btn    = document.getElementById('btnSubmitReview');

    if (rating < 1) {
        msgEl.style.display = 'block';
        msgEl.style.color   = '#DC2626';
        msgEl.textContent   = 'Silakan pilih rating terlebih dahulu.';
        return;
    }
    if (!isi || isi.length < 3) {
        msgEl.style.display = 'block';
        msgEl.style.color   = '#DC2626';
        msgEl.textContent   = 'Review minimal 3 karakter.';
        return;
    }

    btn.disabled    = true;
    btn.textContent = 'Mengirim...';

    fetch('/perusahaan/review', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            nama_perusahaan: PERUSAHAAN_NAMA,
            rating: rating,
            posisi_user: posisi,
            isi_review: isi,
        }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.error) throw new Error(res.error);
        msgEl.style.display = 'block';
        msgEl.style.color   = '#059669';
        msgEl.textContent   = res.message;
        document.getElementById('reviewForm').reset();
        setRating(0);
        // Reload reviews tab
        loaded['reviews'] = false;
        loadTab('reviews');
        // Also reload overview to update rating summary
        loaded['overview'] = false;
    })
    .catch(err => {
        msgEl.style.display = 'block';
        msgEl.style.color   = '#DC2626';
        msgEl.textContent   = err.message || 'Gagal mengirim review.';
    })
    .finally(() => {
        btn.disabled    = false;
        btn.textContent = 'Kirim Review';
    });
}

/* ---------- Lamaran (Jobs list) ---------- */
function renderLamaran(data, panel) {
    const jobs = data.jobs || [];
    const role = data.role;

    if (jobs.length === 0) {
        panel.innerHTML = '<div class="no-data"><div class="icon">' + ICO.clip + '</div><p>Tidak ada lowongan dari perusahaan ini.</p></div>';
        return;
    }

    const tipeMap = {
        'full-time':   { label: 'Full Time',   cls: '' },
        'part-time':   { label: 'Part Time',   cls: 'parttime' },
        'remote':      { label: 'Remote',      cls: 'remote' },
        'hybrid':      { label: 'Hybrid',      cls: 'hybrid' },
        'contract':    { label: 'Contract',    cls: 'contract' },
        'partnership': { label: 'Partnership', cls: '' },
    };

    let html = '<div class="jobs-list">';
    jobs.forEach(j => {
        const tp     = tipeMap[j.tipe] || { label: j.tipe, cls: '' };
        const gajiMin = j.gaji_min ? formatRupiah(j.gaji_min) : null;
        const gajiMax = j.gaji_max ? formatRupiah(j.gaji_max) : null;
        const gajiStr = gajiMin && gajiMax ? gajiMin + ' \u2013 ' + gajiMax : (gajiMin || gajiMax || null);

        // Apply button logic (only for regular users)
        let applyHTML = '';
        if (role === 'user') {
            if (parseInt(j.sudah_lamar) === 1) {
                applyHTML = `<button class="btn-apply applied" disabled>&#10003; Sudah Melamar</button>`;
            } else {
                applyHTML = `<button class="btn-apply" onclick="applyJob(${j.id}, this)">Lamar Sekarang</button>`;
            }
        }

        html += `
        <div class="job-item reveal visible">
            <div class="job-left">
                <h4>${j.nama_posisi}</h4>
                <div class="meta">
                    <span class="tipe-badge ${tp.cls}">${tp.label}</span>
                    ${j.lokasi ? `<span>${ICO.pinSm} ${j.lokasi}</span>` : ''}
                    ${gajiStr ? `<span class="gaji-range">${ICO.money} ${gajiStr}</span>` : ''}
                </div>
            </div>
            <div class="job-right">
                ${role !== 'user'
                    ? `<span class="applicant-count">${ICO.users} ${j.total_lamaran || 0} pelamar</span>`
                    : ''}
                <button class="btn-detail-job" onclick="lihatDetailJob(${j.id})">Detail</button>
                ${applyHTML}
            </div>
        </div>`;
    });
    html += '</div>';
    panel.innerHTML = html;

    const badge = document.querySelector('[data-tab="lamaran"] .tab-count');
    if (badge) badge.textContent = jobs.length;
}

// Apply job
function applyJob(jobId, btnEl) {
    if (!confirm('Apakah Anda yakin ingin melamar pekerjaan ini?')) return;

    btnEl.disabled    = true;
    btnEl.textContent = 'Mengirim...';

    fetch('/perusahaan/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify({ job_id: jobId }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.error) throw new Error(res.error);
        btnEl.textContent = '\u2713 Sudah Melamar';
        btnEl.classList.add('applied');
        // Reload lamaran to reflect new count
        loaded['lamaran'] = false;
    })
    .catch(err => {
        alert(err.message || 'Gagal mengirim lamaran.');
        btnEl.disabled    = false;
        btnEl.textContent = 'Lamar Sekarang';
    });
}

/* ---------- Connections ---------- */
function renderConnections(data, panel) {
    const conns = data.connections || [];

    if (data.note === 'table_not_ready') {
        panel.innerHTML = `
        <div class="no-data">
            <div class="icon">${ICO.link}</div>
            <p>Fitur koneksi belum aktif.<br>
            <small style="font-size:12px;color:var(--text-3)">Import SQL tabel perusahaan_connections terlebih dahulu.</small></p>
        </div>`;
        return;
    }

    if (conns.length === 0) {
        panel.innerHTML = '<div class="no-data"><div class="icon">' + ICO.link + '</div><p>Belum ada koneksi perusahaan yang terdaftar.</p></div>';
        return;
    }

    let html = `<div class="section-title">${conns.length} Perusahaan Terhubung</div>`;
    html += '<div class="connections-grid">';
    conns.forEach(c => {
        const logoHTML = c.connected_logo
            ? `<img class="conn-logo" src="${c.connected_logo}" alt="${c.connected_nama}"
                   onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`
            : '';
        const placeholderHTML = `<div class="conn-logo-placeholder" ${c.connected_logo ? 'style="display:none"' : ''}>${initials(c.connected_nama)}</div>`;

        html += `
        <a href="/perusahaan/${c.connected_id}" class="connection-card reveal visible">
            ${logoHTML}${placeholderHTML}
            <div class="conn-info">
                <h4>${c.connected_nama}</h4>
                <div class="tipe-tag">${ICO.link} ${c.tipe || 'Partner'}</div>
                ${c.connected_lokasi ? `<div class="lokasi-tag">${ICO.pinSm} ${c.connected_lokasi}</div>` : ''}
            </div>
        </a>`;
    });
    html += '</div>';
    panel.innerHTML = html;

    const badge = document.querySelector('[data-tab="connections"] .tab-count');
    if (badge) badge.textContent = conns.length;
}

/* ---------- Helper: switch tab ---------- */
function switchTab(tabName) {
    const btn = document.querySelector('[data-tab="' + tabName + '"]');
    if (btn) btn.click();
}

/* ---------- Modal Detail Job ---------- */
function lihatDetailJob(id) {
    fetch('/jobs/' + id + '/data')
        .then(r => r.json())
        .then(job => {
            const overlay = document.getElementById('modalOverlay');
            if (!overlay) return;
            document.getElementById('modalPosisi').textContent    = job.nama_posisi;
            document.getElementById('modalPerusahaan').textContent = job.nama_perusahaan + (job.lokasi ? ' \u00b7 ' + job.lokasi : '');
            let body = '';
            if (job.deskripsi)   body += `<p><strong>Deskripsi:</strong><br>${job.deskripsi}</p><br>`;
            if (job.requirement) body += `<p><strong>Requirement:</strong><br>${job.requirement}</p>`;
            document.getElementById('modalBody').innerHTML = body || '<p style="color:var(--text-3)">Tidak ada detail tersedia.</p>';
            overlay.style.display = 'flex';
        })
        .catch(() => {
            alert('Gagal memuat detail pekerjaan.');
        });
}

function tutupModal() {
    const overlay = document.getElementById('modalOverlay');
    if (overlay) overlay.style.display = 'none';
}

document.addEventListener('click', e => {
    const overlay = document.getElementById('modalOverlay');
    if (overlay && e.target === overlay) tutupModal();
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });
