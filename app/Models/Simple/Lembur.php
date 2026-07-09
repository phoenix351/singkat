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
        'spkl_id',
        'tim_id',
        'tim_penanggung_jawab_id',
        'link_dokumentasi',
    ];
    public function tim()
    {
        return $this->belongsTo(TimKerja::class);
    }
    public function timPenanggungJawab()
    {
        return $this->belongsTo(TimKerja::class, 'tim_penanggung_jawab_id');
    }
    public function pegawai()
    {
        return $this->hasMany(LemburPegawai::class, 'lembur_id', 'id');
    }
    public function spkl()
    {
        return $this->belongsTo(Spkl::class);
    }
}
