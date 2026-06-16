<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RecruiterController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register/recruiter', [AuthController::class, 'showRegisterRecruiter'])->name('register.recruiter');
Route::post('/register/recruiter', [AuthController::class, 'registerRecruiter']);
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
Route::get('/profil/data', [ProfilController::class, 'getData']);
Route::post('/profil/update-info', [ProfilController::class, 'updateInfo']);
Route::post('/profil/update-password', [ProfilController::class, 'updatePassword']);
Route::post('/profil/hapus', [ProfilController::class, 'hapusAkun']);
Route::post('/profil/upload-cv', [ProfilController::class, 'uploadCV'])->name('profil.uploadCV');
Route::delete('/profil/delete-cv', [ProfilController::class, 'deleteCV'])->name('profil.deleteCV');
Route::post('/profil/upload-photo', [ProfilController::class, 'uploadPhoto'])->name('profil.uploadPhoto');
Route::post('/profil/upload-portfolio', [ProfilController::class, 'uploadPortfolio'])->name('profil.uploadPortfolio');
Route::get('/profil/skills-list', [ProfilController::class, 'skillsList']);
Route::post('/profil/add-skill', [ProfilController::class, 'addSkill'])->name('profil.addSkill');
Route::delete('/profil/remove-skill/{skillId}', [ProfilController::class, 'removeSkill'])->name('profil.removeSkill');
Route::post('/profil/verify-email', [ProfilController::class, 'verifyEmail'])->name('profil.verifyEmail');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/jobs/{id}/data', [JobController::class, 'data']);
Route::get('/matching', [MatchingController::class, 'index'])->name('matching');
Route::post('/hasil', [MatchingController::class, 'hasil'])->name('hasil');

// Perusahaan
Route::get('/perusahaan', [PerusahaanController::class, 'index'])->name('perusahaan');
Route::get('/perusahaan/{id}', [PerusahaanController::class, 'show'])->name('perusahaan.show');
Route::get('/perusahaan/{id}/overview', [PerusahaanController::class, 'getOverview']);
Route::get('/perusahaan/{id}/reviews', [PerusahaanController::class, 'getReviews']);
Route::get('/perusahaan/{id}/lamaran', [PerusahaanController::class, 'getLamaran']);
Route::get('/perusahaan/{id}/connections', [PerusahaanController::class, 'getConnections']);
Route::post('/perusahaan/apply', [PerusahaanController::class, 'applyJob'])->name('perusahaan.apply');
Route::post('/perusahaan/review', [PerusahaanController::class, 'storeReview'])->name('perusahaan.review');

// Forgot Password
Route::post('/forgot-password/check-email', [AuthController::class, 'checkEmail']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);

// ─── Admin Routes ───────────────────────────────────────────────────
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/jobs',      [AdminController::class, 'jobs'])->name('admin.jobs');
    Route::delete('/jobs/{id}', [AdminController::class, 'deleteJob'])->name('admin.jobs.delete');
    Route::get('/users',     [AdminController::class, 'users'])->name('admin.users');
    Route::get('/recruiter/{id}', [AdminController::class, 'recruiterDetail'])->name('admin.recruiter.detail');
    Route::post('/users/{id}/verify', [AdminController::class, 'verifyRecruiter'])->name('admin.users.verify');
    Route::post('/users/{id}/verify-email', [AdminController::class, 'verifyUserEmail'])->name('admin.users.verifyEmail');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/skills',    [AdminController::class, 'skills'])->name('admin.skills');
    Route::post('/skills',   [AdminController::class, 'addSkill'])->name('admin.skills.add');
    Route::delete('/skills/{id}', [AdminController::class, 'deleteSkill'])->name('admin.skills.delete');
    Route::post('/kategori', [AdminController::class, 'addKategori'])->name('admin.kategori.add');
    Route::delete('/kategori/{id}', [AdminController::class, 'deleteKategori'])->name('admin.kategori.delete');
});

// ─── Recruiter Routes ──────────────────────────────────────────────
Route::prefix('recruiter')->group(function () {
    Route::get('/dashboard', [RecruiterController::class, 'dashboard'])->name('recruiter.dashboard');
    Route::get('/jobs',      [RecruiterController::class, 'jobs'])->name('recruiter.jobs');
    Route::post('/jobs',     [RecruiterController::class, 'storeJob'])->name('recruiter.jobs.store');
    Route::put('/jobs/{id}', [RecruiterController::class, 'updateJob'])->name('recruiter.jobs.update');
    Route::delete('/jobs/{id}', [RecruiterController::class, 'deleteJob'])->name('recruiter.jobs.delete');
    Route::get('/jobs/{id}/data', [RecruiterController::class, 'getJobData'])->name('recruiter.jobs.data');
    Route::get('/lamaran',  [RecruiterController::class, 'lamaran'])->name('recruiter.lamaran');
    Route::put('/lamaran/{id}/status', [RecruiterController::class, 'updateStatus'])->name('recruiter.lamaran.status');
    Route::get('/profil',   [RecruiterController::class, 'profil'])->name('recruiter.profil');
    Route::post('/profil',  [RecruiterController::class, 'updateProfil'])->name('recruiter.profil.update');
});