<?php

namespace App\Models\Se2026;

use App\Models\ManManagement\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'upload_logs';
    protected $fillable = [
        'pegawai_id',
        'kode'
    ];
    public $timestamps = true;

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function kabkot() {
        return $this->belongsTo(MasterKabkot::class, 'kode', 'code');
    }
}
