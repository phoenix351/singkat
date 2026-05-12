<?php

namespace App\Models\Simple;

use App\Simple\HasJobLogs;
use Illuminate\Database\Eloquent\Model;

class Spkl extends Model
{
    //
    use HasJobLogs;
    protected $connection = 'sulutweb_simple';
    public $timestamps = true;
    protected $table = 'spkl';
    protected $fillable = [
        'nomor_spkl',
        'bulan',
        'tahun',
        'tahun_dipa',
        'tanggal_pengajuan'
    ];
}
