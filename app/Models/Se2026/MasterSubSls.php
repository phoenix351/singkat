<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class MasterSubSls extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'master_subsls';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'label',
        'desa_code',
    ];

    public function desa()
    {
        return $this->belongsTo(MasterDesa::class, 'desa_code', 'code');
    }

    public function dataFasih()
    {
        return $this->hasMany(DataFasih::class, 'subsls_code', 'code');
    }
}
