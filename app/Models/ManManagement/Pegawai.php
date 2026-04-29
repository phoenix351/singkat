<?php

namespace App\Models\ManManagement;

use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;

class Pegawai extends User
{
    use Notifiable;
    //
    protected $connection = 'sulutweb_man_management';
    protected $table = 'pegawai';

    public $timestamps = false;

    protected $fillable = [
        'nip_lama',
        'nip',
        'username', 
        'email',
        'name', 
        'golongan', 
        'jabatan', 
        'organisasi', 
        'provinsi', 
        'kabupaten', 
        'foto',
    ];
}
