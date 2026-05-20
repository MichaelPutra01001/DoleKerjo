<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'nama', 'username', 'email', 'password',
        'telepon', 'lokasi', 'bio', 'foto_profil',
        'tanggal_lahir', 'gender', 'pendidikan',
        'jurusan', 'role',
    ];

    protected $hidden = ['password'];
}