<?php

namespace App\Models\Simple;

use App\Models\ManManagement\TimKerja;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    //
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
}
