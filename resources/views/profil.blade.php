<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil - DoleKerjo</title>
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
    <a href="{{ route('home') }}" class="brand">DoleKerjo</a>
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
        <div class="avatar-wrap" id="avatarWrap" title="Klik untuk upload foto">
            <div class="avatar" id="sidebarAvatar">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="avatar-overlay">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            </div>
            <input type="file" id="photoFileInput" accept=".jpg,.jpeg,.png,.webp" style="display:none">
        </div>
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

            <div class="detail-grid">
                <!-- Left: Main profile content -->
                <div class="detail-main">
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
                        <div class="email-verify-row" id="emailVerifyRow" style="display:none">
                            <span class="email-badge unverified" id="emailBadge">Belum diverifikasi</span>
                            <button type="button" class="btn-verify" id="btnVerifyEmail" onclick="verifyEmail()">Verifikasi Email</button>
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
                        <div class="skill-add-form" id="skillAddForm">
                            <div class="skill-add-row">
                                <select id="skillSelect" class="skill-select">
                                    <option value="">Pilih skill...</option>
                                </select>
                                <select id="skillLevel" class="skill-level-select">
                                    <option value="pemula">Pemula</option>
                                    <option value="menengah">Menengah</option>
                                    <option value="mahir">Mahir</option>
                                </select>
                                <button type="button" class="btn-add-skill" id="btnAddSkill" onclick="addSkill()">Tambah</button>
                            </div>
                            <span class="save-msg" id="msg-skill"></span>
                        </div>
                    </section>
                </div>

                <!-- Right: Completion Tracker (compact side widget) -->
                <aside class="detail-aside">
                    <div class="completion-widget" id="completionCard">
                        <div class="cw-header">
                            <span class="cw-title">Kelengkapan Profil</span>
                            <span class="cw-percent" id="completionPercent">0%</span>
                        </div>
                        <div class="cw-bar-wrap">
                            <div class="cw-bar" id="completionBar" style="width:0%"></div>
                        </div>
                        <p class="cw-sub" id="completionSub">Memuat data...</p>
                        <div class="cw-steps" id="completionSteps"></div>
                    </div>
                </aside>
            </div>

        </div>

        <!-- ── TAB: PENGATURAN AKUN ── -->
        <div class="tab-panel" id="tab-pengaturan">

            <!-- CV Upload Zone -->
            <section class="card">
                <h2>Curriculum Vitae</h2>
                <div class="cv-upload-zone" id="cvUploadZone">
                    <div class="cv-drop-area" id="cvDropArea">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                        <p class="cv-title">Upload atau taruh file CV kamu di sini</p>
                        <p class="cv-subtitle">Format: DOCX / PDF &middot; Maksimal 5 MB</p>
                        <label class="btn-upload-cv">
                            <input type="file" id="cvFileInput" accept=".pdf,.docx,.doc" style="display:none">
                            Pilih File
                        </label>
                    </div>
                    <div class="cv-uploaded" id="cvUploaded" style="display:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <div class="cv-file-info">
                            <strong id="cvFileName">CV.pdf</strong>
                            <span>CV berhasil diunggah</span>
                        </div>
                        <div class="cv-actions">
                            <a href="#" id="cvDownloadLink" class="btn-cv-action btn-cv-download" title="Download">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Download</span>
                            </a>
                            <label class="btn-cv-action btn-cv-replace" title="Ganti">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                                <span>Ganti</span>
                                <input type="file" id="cvReplaceInput" accept=".pdf,.docx,.doc" style="display:none">
                            </label>
                            <button type="button" class="btn-cv-action btn-cv-delete" id="btnDeleteCV" title="Hapus" onclick="deleteCV()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

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

<footer><p>© 2026 DoleKerjo</p></footer>

<script src="{{ asset('js/dark-mode.js') }}"></script>
<script src="{{ asset('js/profil.js') }}"></script>
</body>
</html>