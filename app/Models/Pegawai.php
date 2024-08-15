<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Pegawai extends Model
{
    use HasFactory;
    protected $table = "pegawai";

    protected $guarded = [];

    public function capaian(): HasMany
    {
        return $this->hasMany(Capaian::class);
    }
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
