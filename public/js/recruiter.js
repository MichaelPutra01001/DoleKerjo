// =============================================
// RECRUITER — GradMatch Recruiter Portal JS
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

// Count-up animation
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

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity .3s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 4000);
});

// Toggle sort direction
function toggleDir() {
    const dirInput = document.getElementById('dirInput');
    if (!dirInput) return;
    dirInput.value = dirInput.value === 'desc' ? 'asc' : 'desc';
    document.getElementById('sortForm').submit();
}

// Search debounce (for lamaran page)
(function() {
    const input = document.getElementById('searchInput');
    const form  = document.getElementById('searchForm');
    if (!input || !form) return;
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 600);
    });
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') { clearTimeout(timer); form.submit(); }
    });
})();

// ── Job Modal ──
function openJobModal(mode, jobData) {
    const modal = document.getElementById('jobModal');
    const title = document.getElementById('modalTitle');
    const form  = document.getElementById('jobForm');

    if (mode === 'create') {
        title.textContent = 'Tambah Lowongan Baru';
        form.action = form.dataset.createUrl;
        form.querySelector('[name="_method"]')?.remove();
        form.reset();
    } else if (mode === 'edit' && jobData) {
        title.textContent = 'Edit Lowongan';
        form.action = form.dataset.updateUrl.replace('__ID__', jobData.id);
        // Add _method PUT
        if (!form.querySelector('[name="_method"]')) {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = '_method'; input.value = 'PUT';
            form.appendChild(input);
        }
        // Fill fields
        form.nama_posisi.value = jobData.nama_posisi || '';
        form.nama_perusahaan.value = jobData.nama_perusahaan || '';
        form.lokasi.value = jobData.lokasi || '';
        form.tipe.value = jobData.tipe || 'full-time';
        form.kategori.value = jobData.kategori || 'teknologi';
        form.deskripsi.value = jobData.deskripsi || '';
        form.requirement.value = jobData.requirement || '';
        form.gaji_min.value = jobData.gaji_min || '';
        form.gaji_max.value = jobData.gaji_max || '';
    }

    modal.classList.add('show');
}

function closeJobModal() {
    document.getElementById('jobModal').classList.remove('show');
}

function editJob(id) {
    fetch(`/recruiter/jobs/${id}/data`)
        .then(r => r.json())
        .then(data => {
            if (data) openJobModal('edit', data);
        });
}

// Close modal on overlay click
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) closeJobModal();
});

// Close modal on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeJobModal();
});

// Change applicant status
function changeStatus(lamaranId, newStatus) {
    const form = document.getElementById('status-form-' + lamaranId);
    if (form) {
        form.querySelector('[name="status"]').value = newStatus;
        form.submit();
    }
}
