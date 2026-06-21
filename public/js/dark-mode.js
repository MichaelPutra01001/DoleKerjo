/* dark-mode.js — ngatur sinkronisasi dark mode di semua halaman */

document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('#theme-toggle, .theme-toggle-btn');
    const profilToggle = document.querySelector('input[name="dark_mode"]');

    // update tampilan semua tombol toggle sesuai tema sekarang
    const updateUI = (theme) => {
        if (profilToggle) {
            profilToggle.checked = (theme === 'dark');
        }
    };

    // fungsi buat ganti tema
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

    // pasang event listener ke tombol toggle di navbar
    toggles.forEach(btn => {
        // remove dulu biar ga dobel
        btn.removeEventListener('click', toggleTheme);
        btn.addEventListener('click', toggleTheme);
    });

    // pasang juga ke toggle di halaman pengaturan profil kalau ada
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

    // set tampilan awal sesuai tema yang tersimpan
    const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
    updateUI(initialTheme);
});
