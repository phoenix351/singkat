<?php

namespace App\Models\Simple;

use App\Models\ManManagement\Pegawai;
use Illuminate\Database\Eloquent\Model;

class LemburPegawai extends Model
{
    //
    protected $connection = 'sulutweb_simple';
    public $timestamps = true;
    protected $table = 'lembur_pegawai';
    protected $fillable = [
        'lembur_id',
        'pegawai_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
    ];
    public function lembur()
    {
        return $this->belongsTo(Lembur::class);
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}

