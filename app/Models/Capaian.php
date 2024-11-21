<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Capaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'predikat_id',
        'nomor_sk',
        'tmt_sk',
        'jenis_sk',
        'bulan_mulai',
        'bulan_selesai',
        'path',
        'angka_kredit',
        'angka_kredit_akumulasi'
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($capaian) {
            $capaian->update_ak();
        });
    }


    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
    public function history(): HasOne
    {
        return $this->HasOne(AngkaKreditHistory::class);
    }
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class,'jabatan_id','id');
    }

    public function predikat(): BelongsTo
    {
        return $this->belongsTo(Predikat::class);
    }
    public function jenis_sk(): HasOne
    {
        return $this->hasOne(JenisSK::class,'id','jenis_sk');
    }

    public function update_ak()
    {
        // $angka_kredit_dasar = (float)$this->pegawai->jabatan->angka_kredit;
        // $nilai_ak = (float) $this->predikat->nilai;

        // $month_counts = $this->bulan_akhir-$this->bulan_mulai+1;


        // $this->angka_kredit = $nilai_ak * $angka_kredit_dasar*($month_counts/12);
    }
}
