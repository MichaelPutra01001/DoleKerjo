/* ============================================================
   dark-mode.js — GradMatch Dark Mode Synchronization
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('#theme-toggle, .theme-toggle-btn');
    const profilToggle = document.querySelector('input[name="dark_mode"]');

    // Sync all toggle UIs with current theme
    const updateUI = (theme) => {
        if (profilToggle) {
            profilToggle.checked = (theme === 'dark');
        }
    };

    // Toggle theme action
    const toggleTheme = () => {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        if (newTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        localStorage.setItem('theme', newTheme);
        updateUI(newTheme);
    };

    // Bind event listeners to navbar / corner toggles
    toggles.forEach(btn => {
        // Clean event listener to avoid duplicate bindings
        btn.removeEventListener('click', toggleTheme);
        btn.addEventListener('click', toggleTheme);
    });

    // Bind event listener to Profile Settings toggle (if present)
    if (profilToggle) {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        profilToggle.checked = (currentTheme === 'dark');
        
        profilToggle.removeEventListener('change', toggleTheme);
        profilToggle.addEventListener('change', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const expectedTheme = profilToggle.checked ? 'dark' : 'light';
            if (currentTheme !== expectedTheme) {
                toggleTheme();
            }
        });
    }

    // Set initial toggle UIs
    const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateUI(initialTheme);
});
