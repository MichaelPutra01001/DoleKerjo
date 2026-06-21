/* jobs.js - halaman daftar job dan detail job */

// efek scroll reveal
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// tampilin notifikasi toast
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.className = 'toast show ' + type;
    setTimeout(() => { toast.className = 'toast'; }, 3000);
}

// ambil csrf token dari meta tag
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// === bagian listing job ===

// auto submit filter kalau ada yang diubah
document.querySelectorAll('#filterForm input').forEach(input => {
    input.addEventListener('change', () => {
        document.getElementById('filterForm').submit();
    });
});

// toggle show more / show less di grup filter
function toggleShowMore(listId) {
    const list = document.getElementById(listId);
    if (!list) return;
    list.classList.toggle('expanded');
}

// reset semua filter ke default
function resetFilter() {
    const form = document.getElementById('filterForm');
    if (!form) return;
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    form.querySelectorAll('input[type="radio"]').forEach(r => {
        r.checked = r.value === 'terbaru';
    });
    form.submit();
}

// toggle sidebar filter di mobile
function toggleFilterMobile() {
    const sidebar = document.getElementById('filterSidebar');
    if (sidebar) sidebar.classList.toggle('open');
}

// daftar cepat langsung dari listing page
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-daftar-cepat');
    if (!btn || btn.disabled) return;

    const jobId = btn.getAttribute('data-job-id');
    if (!jobId) return;

    btn.disabled = true;
    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spin"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Mengirim...';

    fetch('/perusahaan/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: 'job_id=' + jobId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Sudah Dilamar';
            btn.classList.add('applied');
            showToast('Lamaran berhasil dikirim!');
        } else {
            if (data.error && data.error.includes('sudah')) {
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Sudah Dilamar';
                btn.classList.add('applied');
            } else {
                btn.disabled = false;
                btn.textContent = 'Daftar Cepat';
                showToast(data.error || 'Gagal mengirim lamaran', 'error');
            }
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Daftar Cepat';
        showToast('Terjadi kesalahan. Coba lagi.', 'error');
    });
});

// tutup sidebar filter kalau klik di luar area filter
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('filterSidebar');
    if (!sidebar || !sidebar.classList.contains('open')) return;
    if (!sidebar.contains(e.target) && !document.getElementById('filterToggle')?.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});
