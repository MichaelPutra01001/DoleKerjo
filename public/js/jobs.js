// ── Scroll Reveal ──
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ── Modal Detail Job ──
const overlay = document.getElementById('modalOverlay');

function lihatDetail(jobId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/jobs/${jobId}`, {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(job => {
        document.getElementById('modalPosisi').textContent     = job.nama_posisi;
        document.getElementById('modalPerusahaan').textContent = `${job.nama_perusahaan} · ${job.lokasi ?? '-'}`;

        const gaji = job.gaji_min
            ? `Rp ${Number(job.gaji_min).toLocaleString('id-ID')} – Rp ${Number(job.gaji_max).toLocaleString('id-ID')}`
            : 'Tidak disebutkan';

        document.getElementById('modalBody').innerHTML = `
            <p><strong>Tipe:</strong> ${job.tipe}</p>
            <p><strong>Gaji:</strong> ${gaji}</p>
            <hr style="margin:12px 0;border:none;border-top:1px solid #E2E5E9">
            <p><strong>Deskripsi:</strong></p>
            <p style="margin-top:6px">${job.deskripsi ?? '-'}</p>
            <p style="margin-top:12px"><strong>Requirements:</strong></p>
            <p style="margin-top:6px">${job.requirement ?? '-'}</p>
        `;

        overlay.style.display = 'flex';
    })
    .catch(() => alert('Gagal memuat detail lowongan.'));
}

function tutupModal() {
    overlay.style.display = 'none';
}

// ── Event listener untuk tombol Lihat Detail ──
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-detail')) {
        const jobId = e.target.getAttribute('data-id');
        lihatDetail(jobId);
    }
});

// ── Tutup modal kalau klik di luar ──
overlay.addEventListener('click', function(e) {
    if (e.target === overlay) tutupModal();
});