<?php

namespace App\Models\Simple;

use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    //
    protected $connection = 'sulutweb_simple';
    public $timestamps = true;
    protected $table = 'lembur';
    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'nomor_spkl',
        'maksud_lembur',
        'created_by',
    ];
}
