<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngkaKreditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'akumulasi_ak',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
