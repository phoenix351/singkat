<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory;
    protected $table = "pegawai";

    protected $guarded = [];

    public function capaian(): HasMany
    {
        return $this->hasMany(Capaian::class);
    }
}
