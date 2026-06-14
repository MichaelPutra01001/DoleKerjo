<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\PerusahaanController;

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
Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
Route::get('/jobs/{id}', [JobController::class, 'show']);
Route::get('/matching', [MatchingController::class, 'index'])->name('matching');
Route::get('/hasil', [MatchingController::class, 'hasil'])->name('hasil');

// Perusahaan
Route::get('/perusahaan', [PerusahaanController::class, 'index'])->name('perusahaan');
Route::get('/perusahaan/{id}', [PerusahaanController::class, 'show'])->name('perusahaan.show');
Route::get('/perusahaan/{id}/overview', [PerusahaanController::class, 'getOverview']);
Route::get('/perusahaan/{id}/reviews', [PerusahaanController::class, 'getReviews']);
Route::get('/perusahaan/{id}/lamaran', [PerusahaanController::class, 'getLamaran']);
Route::get('/perusahaan/{id}/connections', [PerusahaanController::class, 'getConnections']);

// Forgot Password
Route::post('/forgot-password/check-email', [AuthController::class, 'checkEmail']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);