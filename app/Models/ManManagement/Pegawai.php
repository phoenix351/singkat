<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    //
    protected $connection = 'man_management';
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
