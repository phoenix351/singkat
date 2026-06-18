<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class MasterDesa extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'master_desa';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'label',
        'kec_code',
    ];

    public function subSls()
    {
        return $this->hasMany(MasterSubSls::class, 'desa_code', 'code');
    }

    public function kec()
    {
        return $this->belongsTo(MasterKec::class, 'kec_code', 'code');
    }
}
