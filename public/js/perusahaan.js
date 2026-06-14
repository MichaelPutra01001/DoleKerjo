/* ============================================================
   perusahaan.js — GradMatch Company Pages
   ============================================================ */

// ── Helper: format rupiah ──────────────────────────────────
function formatRupiah(n) {
    if (!n) return '-';
    if (n >= 1_000_000) return 'Rp ' + (n / 1_000_000).toFixed(0) + ' Jt';
    if (n >= 1_000) return 'Rp ' + (n / 1_000).toFixed(0) + ' Rb';
    return 'Rp ' + n;
}

// ── Helper: render bintang ─────────────────────────────────
function renderStars(rating) {
    let html = '<span class="stars">';
    for (let i = 1; i <= 5; i++) {
        if (rating >= i) {
            html += '<span class="star filled">★</span>';
        } else if (rating >= i - 0.5) {
            html += '<span class="star half">★</span>';
        } else {
            html += '<span class="star">★</span>';
        }
    }
    html += '</span>';
    return html;
}

// ── Helper: inisial nama ───────────────────────────────────
function initials(name) {
    if (!name) return '?';
    const parts = name.trim().split(' ');
    if (parts.length === 1) return parts[0][0].toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

// ── Helper: format tanggal ─────────────────────────────────
function formatDate(str) {
    if (!str) return '-';
    const d = new Date(str);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

// ── Helper: skeleton ───────────────────────────────────────
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
   INDEX PAGE — Search & Reveal
   ============================================================ */

if (document.getElementById('companyGrid')) {
    // Live search
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

    // Reveal on scroll
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
   DETAIL PAGE — Tab switching & AJAX data loading
   ============================================================ */

const PERUSAHAAN_ID = window.PERUSAHAAN_ID || null;
const loaded = {}; // cache per tab

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const target = this.dataset.tab;

        // UI
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('panel-' + target).classList.add('active');

        // Load data if not cached
        if (!loaded[target] && PERUSAHAAN_ID) {
            loadTab(target);
        }
    });
});

// Auto-load first tab
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
                <div class="icon">⚠️</div>
                <p>Gagal memuat data. Coba refresh halaman.</p>
            </div>`;
        });
}

// ── Render dispatcher ──────────────────────────────────────
function renderTab(tab, data, panel) {
    switch (tab) {
        case 'overview':    renderOverview(data, panel);    break;
        case 'reviews':     renderReviews(data, panel);     break;
        case 'lamaran':     renderLamaran(data, panel);     break;
        case 'connections': renderConnections(data, panel); break;
    }
}

/* ── Overview ─────────────────────────────────────────────── */
function renderOverview(data, panel) {
    const p  = data.perusahaan;
    const rs = data.review_stats;
    const rr = data.recent_reviews || [];
    const totalJobs = data.total_jobs || 0;
    const avg   = rs ? (parseFloat(rs.avg_rating) || 0) : 0;
    const total = rs ? (parseInt(rs.total) || 0) : 0;

    let html = `<div class="overview-grid">`;

    // Kiri: info umum
    html += `
    <div class="info-card reveal visible">
        <h3>Informasi Umum</h3>
        <div class="info-list">
            <div class="info-row">
                <div class="ico">🌐</div>
                <div class="txt">
                    <div class="lbl">Website</div>
                    <div class="val">${p.website
                        ? `<a href="${p.website}" target="_blank">${p.website}</a>`
                        : '<span style="color:var(--text-3)">-</span>'
                    }</div>
                </div>
            </div>
            <div class="info-row">
                <div class="ico">📍</div>
                <div class="txt">
                    <div class="lbl">Lokasi</div>
                    <div class="val">${p.lokasi || '-'}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="ico">👤</div>
                <div class="txt">
                    <div class="lbl">Recruiter</div>
                    <div class="val">${p.recruiter_nama || '-'}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="ico">💼</div>
                <div class="txt">
                    <div class="lbl">Lowongan Aktif</div>
                    <div class="val">${totalJobs} posisi</div>
                </div>
            </div>
        </div>
    </div>`;

    // Kanan: ringkasan review
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
        html += `<div class="no-data" style="padding:24px 0"><div class="icon" style="font-size:2rem">📝</div><p>Belum ada review</p></div>`;
    }
    html += `</div></div>`; // end overview-grid

    // Deskripsi
    if (p.deskripsi) {
        html += `
        <div class="info-card reveal visible" style="margin-bottom:14px">
            <h3>Tentang Perusahaan</h3>
            <p style="font-size:14px;color:var(--text-2);line-height:1.8;margin-top:8px">${p.deskripsi}</p>
        </div>`;
    }

    // Review terbaru
    if (rr.length > 0) {
        html += `<div class="section-title">Review Terbaru</div><div class="recent-reviews">`;
        rr.forEach(r => {
            html += `
            <div class="mini-review reveal visible">
                <div class="top">
                    <div>
                        <span class="reviewer">${r.reviewer || 'Anonim'}</span>
                        ${r.posisi_user ? `<span class="pos"> · ${r.posisi_user}</span>` : ''}
                    </div>
                    ${renderStars(r.rating)}
                </div>
                <div class="txt">${r.isi_review || ''}</div>
            </div>`;
        });
        html += `
        <a href="#" onclick="switchTab('reviews');return false;"
           style="font-size:13px;color:var(--blue);text-decoration:none;display:inline-block;margin-top:4px;font-weight:500">
            Lihat semua review →
        </a>
        </div>`;
    }

    panel.innerHTML = html;
}

/* ── Reviews ──────────────────────────────────────────────── */
function renderReviews(data, panel) {
    if (!data || data.length === 0) {
        panel.innerHTML = '<div class="no-data"><div class="icon">💬</div><p>Belum ada review untuk perusahaan ini.</p></div>';
        return;
    }

    let html = '<div class="reviews-list">';
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
    panel.innerHTML = html;

    const badge = document.querySelector('[data-tab="reviews"] .tab-count');
    if (badge) badge.textContent = data.length;
}

/* ── Lamaran (Jobs list) ──────────────────────────────────── */
function renderLamaran(data, panel) {
    const jobs = data.jobs || [];
    const role = data.role;

    if (jobs.length === 0) {
        panel.innerHTML = '<div class="no-data"><div class="icon">📋</div><p>Tidak ada lowongan dari perusahaan ini.</p></div>';
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
        const gajiStr = gajiMin && gajiMax ? gajiMin + ' – ' + gajiMax : (gajiMin || gajiMax || null);

        html += `
        <div class="job-item reveal visible">
            <div class="job-left">
                <h4>${j.nama_posisi}</h4>
                <div class="meta">
                    <span class="tipe-badge ${tp.cls}">${tp.label}</span>
                    ${j.lokasi ? `<span>📍 ${j.lokasi}</span>` : ''}
                    ${gajiStr ? `<span class="gaji-range">💰 ${gajiStr}</span>` : ''}
                </div>
            </div>
            <div class="job-right">
                ${role !== 'user'
                    ? `<span class="applicant-count">👥 ${j.total_lamaran || 0} pelamar</span>`
                    : ''}
                <button class="btn-detail-job" onclick="lihatDetailJob(${j.id})">Lihat Detail</button>
            </div>
        </div>`;
    });
    html += '</div>';
    panel.innerHTML = html;

    const badge = document.querySelector('[data-tab="lamaran"] .tab-count');
    if (badge) badge.textContent = jobs.length;
}

/* ── Connections ──────────────────────────────────────────── */
function renderConnections(data, panel) {
    const conns = data.connections || [];

    if (data.note === 'table_not_ready') {
        panel.innerHTML = `
        <div class="no-data">
            <div class="icon">🔗</div>
            <p>Fitur koneksi belum aktif.<br>
            <small style="font-size:12px;color:var(--text-3)">Import SQL tabel perusahaan_connections terlebih dahulu.</small></p>
        </div>`;
        return;
    }

    if (conns.length === 0) {
        panel.innerHTML = '<div class="no-data"><div class="icon">🔗</div><p>Belum ada koneksi perusahaan yang terdaftar.</p></div>';
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
                <div class="tipe-tag">🔗 ${c.tipe || 'Partner'}</div>
                ${c.connected_lokasi ? `<div class="lokasi-tag">📍 ${c.connected_lokasi}</div>` : ''}
            </div>
        </a>`;
    });
    html += '</div>';
    panel.innerHTML = html;

    const badge = document.querySelector('[data-tab="connections"] .tab-count');
    if (badge) badge.textContent = conns.length;
}

/* ── Helper: switch tab dari luar ─────────────────────────── */
function switchTab(tabName) {
    const btn = document.querySelector('[data-tab="' + tabName + '"]');
    if (btn) btn.click();
}

/* ── Modal Detail Job ─────────────────────────────────────── */
function lihatDetailJob(id) {
    fetch('/jobs/' + id)
        .then(r => r.json())
        .then(job => {
            const overlay = document.getElementById('modalOverlay');
            if (!overlay) return;
            document.getElementById('modalPosisi').textContent    = job.nama_posisi;
            document.getElementById('modalPerusahaan').textContent = job.nama_perusahaan + (job.lokasi ? ' · ' + job.lokasi : '');
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

// Tutup modal klik di luar
document.addEventListener('click', e => {
    const overlay = document.getElementById('modalOverlay');
    if (overlay && e.target === overlay) tutupModal();
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupModal(); });
