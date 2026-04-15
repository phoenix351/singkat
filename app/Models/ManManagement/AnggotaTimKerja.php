<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaTimKerja extends Model
{
    //
    protected $connection = 'man_management';
    protected $table = 'keanggotaan_timkerja';

    public $timestamps = true;

    protected $fillable = [
        'tim_id',
        'pegawai_id',
        'keanggotaan'
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function tim(): BelongsTo
    {
        return $this->belongsTo(TimKerja::class);
    }
}
