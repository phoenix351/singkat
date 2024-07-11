<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AngkaKreditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'capaian_id',
        'akumulasi_ak',
    ];


    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
    public function capaian(): BelongsTo
    {
        return $this->belongsTo(Capaian::class);
    }
}
