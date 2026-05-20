<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
Route::get('/profil/data', [ProfilController::class, 'getData']);
Route::post('/profil/update-info', [ProfilController::class, 'updateInfo']);
Route::post('/profil/update-password', [ProfilController::class, 'updatePassword']);
Route::post('/profil/hapus', [ProfilController::class, 'hapusAkun']);