<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil - GradMatch</title>
    <link rel="stylesheet" href="{{ asset('css/profil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="brand">GradMatch</a>
    <div class="nav-links">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('profil') }}" class="active">Profil</a>
        <a href="/jobs">List Job</a>
        <a href="{{ route('perusahaan') }}">Perusahaan</a>
        <a href="/matching">Skill Matching</a>
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:14px;font-weight:500;color:#8A9099;padding:7px 14px;border-radius:6px;">
                Logout
            </button>
        </form>
    </div>
</nav>

<div class="layout">
    <aside class="sidebar">
        <div class="avatar" id="sidebarAvatar">👤</div>
        <h3 id="sidebarNama">Memuat...</h3>
        <ul>
            <li class="active" onclick="switchTab(this,'detail')">Detail Profil</li>
            <li onclick="switchTab(this,'pengaturan')">Pengaturan Akun</li>
            <li onclick="switchTab(this,'lamaran')">Riwayat Lamaran</li>
            <li onclick="switchTab(this,'notifikasi')">Notifikasi</li>
            <li class="keluar" style="list-style:none;padding:0">
                <form action="{{ route('logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;font-size:14px;font-weight:500;color:#CC1016;width:100%;text-align:left;padding:10px 24px;font-family:'DM Sans',sans-serif;">
                        Keluar
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="content">

        <!-- ── TAB: DETAIL PROFIL ── -->
        <div class="tab-panel active" id="tab-detail">
            <section class="card">
                <h2>Informasi Pribadi</h2>
                <div class="grid-4">
                    <div><span>Nama Lengkap</span><strong id="detailNama">—</strong></div>
                    <div><span>Email</span><strong id="detailEmail">—</strong></div>
                    <div><span>No. Telepon</span><strong id="detailTelepon">—</strong></div>
                    <div><span>Lokasi</span><strong id="detailLokasi">—</strong></div>
                    <div><span>Pendidikan</span><strong id="detailPendidikan">—</strong></div>
                    <div><span>Jurusan</span><strong id="detailJurusan">—</strong></div>
                </div>
            </section>
            <section class="card">
                <h2>Tentang Saya</h2>
                <p id="detailBio" style="font-size:14px;color:#555;line-height:1.7">—</p>
            </section>
            <section class="card">
                <h2>Keahlian</h2>
                <div class="skills" id="detailSkills">
                    <span style="color:var(--text-3);font-size:13px">Belum ada skill yang ditambahkan.</span>
                </div>
            </section>
        </div>

        <!-- ── TAB: PENGATURAN AKUN ── -->
        <div class="tab-panel" id="tab-pengaturan">

            <section class="card">
                <h2>Ubah Informasi Pribadi</h2>
                <form class="settings-form" id="formInfo">
                    <input type="hidden" name="action" value="info">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="set-nama">Nama Lengkap</label>
                            <input type="text" id="set-nama" name="nama" placeholder="Nama lengkap">
                        </div>
                        <div class="form-group">
                            <label for="set-email">Email</label>
                            <input type="email" id="set-email" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="set-telp">No. Telepon</label>
                            <input type="text" id="set-telp" name="telepon" placeholder="Nomor telepon">
                        </div>
                        <div class="form-group">
                            <label for="set-lokasi">Lokasi</label>
                            <input type="text" id="set-lokasi" name="lokasi" placeholder="Kota, Negara">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="set-bio">Tentang Saya</label>
                        <textarea id="set-bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu..."></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Simpan Perubahan</button>
                        <span class="save-msg" id="msg-info"></span>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2>Ubah Password</h2>
                <form class="settings-form" id="formPassword">
                    <input type="hidden" name="action" value="password">
                    <div class="form-group">
                        <label for="set-oldpass">Password Saat Ini</label>
                        <input type="password" id="set-oldpass" name="old_password" placeholder="Masukkan password lama">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="set-newpass">Password Baru</label>
                            <input type="password" id="set-newpass" name="new_password" placeholder="Password baru">
                        </div>
                        <div class="form-group">
                            <label for="set-confirmpass">Konfirmasi Password</label>
                            <input type="password" id="set-confirmpass" name="confirm_password" placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <p class="form-hint">Password minimal 8 karakter, kombinasi huruf dan angka.</p>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Ubah Password</button>
                        <span class="save-msg" id="msg-password"></span>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2>Preferensi Notifikasi</h2>
                <form class="settings-form" id="formNotif">
                    <input type="hidden" name="action" value="notif">
                    <div class="toggle-item">
                        <div>
                            <strong>Email Lowongan Baru</strong>
                            <p>Terima notifikasi email saat ada lowongan baru yang cocok</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="notif_lowongan" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <div>
                            <strong>Update Status Lamaran</strong>
                            <p>Notifikasi saat status lamaranmu berubah</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="notif_lamaran" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <div>
                            <strong>Tips Karier</strong>
                            <p>Artikel dan tips mingguan seputar pengembangan karier</p>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="notif_tips">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="form-actions" style="margin-top:20px">
                        <button type="submit" class="btn-save">Simpan Preferensi</button>
                        <span class="save-msg" id="msg-notif"></span>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2>Preferensi Tampilan</h2>
                <div class="toggle-item">
                    <div>
                        <strong>Mode Gelap</strong>
                        <p>Aktifkan tampilan mode gelap untuk kenyamanan membaca di lingkungan redup</p>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="dark_mode">
                        <span class="slider"></span>
                    </label>
                </div>
            </section>

            <section class="card card-danger">
                <h2>Hapus Akun</h2>
                <p class="danger-desc">Tindakan berikut bersifat permanen dan tidak dapat dibatalkan.</p>
                <button class="btn-danger" onclick="confirmDelete()">Hapus Akun Saya</button>
            </section>

        </div>

        <!-- ── TAB: RIWAYAT LAMARAN ── -->
        <div class="tab-panel" id="tab-lamaran">
            <section class="card">
                <h2>Riwayat Lamaran</h2>
                <div id="lamaranList">
                    <p style="font-size:14px;color:#777">Memuat data lamaran...</p>
                </div>
            </section>
        </div>

        <!-- ── TAB: NOTIFIKASI ── -->
        <div class="tab-panel" id="tab-notifikasi">
            <section class="card">
                <h2>Notifikasi</h2>
                <p style="font-size:14px;color:#777">Tidak ada notifikasi baru.</p>
            </section>
        </div>

    </main>
</div>

<footer><p>© 2026 GradMatch</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/profil.js') }}"></script>
</body>
</html>