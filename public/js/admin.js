// =============================================
// ADMIN — GradMatch Admin Panel JS
// =============================================

// Scroll reveal
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Count-up animation for stat values
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

const statObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting && e.target.dataset.target !== undefined) {
            countUp(e.target);
            statObserver.unobserve(e.target);
        }
    });
}, { threshold: 0.3 });
document.querySelectorAll('[data-target]').forEach(el => statObserver.observe(el));

// Confirm delete
function confirmDelete(formId) {
    if (confirm('Yakin ingin menghapus data ini?')) {
        document.getElementById(formId).submit();
    }
}

// Auto-dismiss alerts after 4s
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity .3s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 4000);
});

// Toggle sort direction and submit
function toggleDir() {
    const dirInput = document.getElementById('dirInput');
    if (!dirInput) return;
    dirInput.value = dirInput.value === 'desc' ? 'asc' : 'desc';
    document.getElementById('sortForm').submit();
}

// Search input — submit after 600ms idle
(function() {
    const input = document.getElementById('searchInput');
    const form  = document.getElementById('searchForm');
    if (!input || !form) return;

    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 600);
    });

    // Submit immediately on Enter
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            clearTimeout(timer);
            form.submit();
        }
    });
})();
