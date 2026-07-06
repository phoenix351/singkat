<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class HistoryWilayah extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'history_wilayah';
    protected $fillable = [
        'subsls_code',
        'open',
        'draft',
        'submitted_p',
        'approved',
        'rejected',
        'submitted_r',
        'revoked',
        'completed',
    ];
    public $timestamps = true;

    public function subsls()
    {
        return $this->belongsTo(MasterSubSls::class, 'subsls_code', 'code');
    }
}
