// ─── Skill Matching JS ─────────────────────────────────────────────────

const chipsArea  = document.getElementById('skillChipsArea');
const skillInput = document.getElementById('skillInput');
const hiddenIn   = document.getElementById('skillsHidden');
const noHint     = document.getElementById('noSkillsHint');
const form       = document.getElementById('matchingForm');

// ── Collect all current chip skill names into hidden input ──
function syncHidden() {
    const chips = chipsArea.querySelectorAll('.skill-chip');
    const names = Array.from(chips).map(c => c.dataset.skill);
    hiddenIn.value = names.join(',');
    // Toggle hint visibility
    if (noHint) {
        noHint.style.display = chips.length > 0 ? 'none' : '';
    }
}

// ── Create a chip element ──
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

// ── Check if skill already exists (case-insensitive) ──
function skillExists(name) {
    const lower = name.toLowerCase();
    const chips = chipsArea.querySelectorAll('.skill-chip');
    return Array.from(chips).some(c => c.dataset.skill.toLowerCase() === lower);
}

// ── Add skills from input ──
function addSkillFromInput() {
    const raw = skillInput.value.trim();
    if (!raw) return;

    // Split by comma
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

// Make it global so onclick works
window.addSkillFromInput = addSkillFromInput;

// ── Remove a chip ──
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

// ── Enter key on input ──
skillInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkillFromInput();
    }
});

// ── Form submit: ensure hidden is populated ──
form.addEventListener('submit', function(e) {
    syncHidden();

    // Check at least 1 skill
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

// ── Init: sync hidden on page load (profile skills pre-populated) ──
syncHidden();
