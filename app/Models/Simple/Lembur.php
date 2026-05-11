<?php

namespace App\Models\Simple;

use App\Models\ManManagement\TimKerja;
use App\Simple\HasJobLogs;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    //
    use HasJobLogs;
    protected $connection = 'sulutweb_simple';
    public $timestamps = true;
    protected $table = 'lembur';
    protected $fillable = [
        'maksud_lembur',
        'nomor_spkl',
        'tim_id',
    ];
    public function tim()
    {
        return $this->belongsTo(TimKerja::class);
    }
    public function pegawai()
    {
        return $this->hasMany(LemburPegawai::class, 'lembur_id', 'id');
    }
}
