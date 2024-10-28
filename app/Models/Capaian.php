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

    protected $fillable = ['pegawai_id', 'predikat_id', 'periode', 'tahun','bulan', 'angka_kredit'];

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

    public function predikat(): BelongsTo
    {
        return $this->belongsTo(Predikat::class);
    }

    public function update_ak()
    {
        $angka_kredit_dasar = (float)$this->pegawai->jabatan->angka_kredit;
        $nilai_ak = (float) $this->predikat->nilai;

        if (strlen($this->periode) > 7) {
            $nilai_ak = $nilai_ak / 2;
        } elseif ($this->periode=="Bulanan") {
            $nilai_ak = $nilai_ak / 12;
        }

        $this->angka_kredit = $nilai_ak * $angka_kredit_dasar;
    }
}
