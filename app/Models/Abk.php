<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abk extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan_id',
        'unit_kerja_id',
        'abk',
        'eksisting',
        'kebutuhan_pegawai'
    ];

    protected $table = 'abk';

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }
}
