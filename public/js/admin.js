// === admin panel JS ===

// efek scroll reveal buat elemen yang masuk viewport
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// animasi angka naik pelan-pelan
function countUp(el) {
    const target = parseInt(el.dataset.target);
    const duration = 1200;
    const start = performance.now();
    const step = now => {
        const p = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.floor(ease * target).toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

// jalanin animasi angka waktu elemen masuk viewport
const statObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting && e.target.dataset.target !== undefined) {
            countUp(e.target);
            statObserver.unobserve(e.target);
        }
    });
}, { threshold: 0.3 });
document.querySelectorAll('[data-target]').forEach(el => statObserver.observe(el));

// konfirmasi sebelum hapus data
function confirmDelete(formId) {
    if (confirm('Yakin ingin menghapus data ini?')) {
        document.getElementById(formId).submit();
    }
}

// alert otomatis hilang setelah 4 detik
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity .3s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 4000);
});

// ganti arah sort terus submit form
function toggleDir() {
    const dirInput = document.getElementById('dirInput');
    if (!dirInput) return;
    dirInput.value = dirInput.value === 'desc' ? 'asc' : 'desc';
    document.getElementById('sortForm').submit();
}

// submit form search setelah user berhenti ngetik 600ms
(function() {
    const input = document.getElementById('searchInput');
    const form  = document.getElementById('searchForm');
    if (!input || !form) return;

    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 600);
    });

    // langsung submit kalau pencet Enter
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            clearTimeout(timer);
            form.submit();
        }
    });
})();
