<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class MasterKabkot extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'master_kabkot';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'label',
        'provinsi_code'
    ];

    public function kec()
    {
        return $this->hasMany(MasterKec::class, 'kabkot_code', 'code');
    }
}
