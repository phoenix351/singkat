<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class MasterKec extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'master_kec';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'label',
        'kabkot_code'
    ];

    public function desa()
    {
        return $this->hasMany(MasterDesa::class, 'kec_code', 'code');
    }

    public function kabkot()
    {
        return $this->belongsTo(MasterKabkot::class, 'kabkot_code', 'code');
    }
}
