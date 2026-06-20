<?php

namespace App\Models\Se2026;

use App\Models\Se2026\MasterSubSls;
use Illuminate\Database\Eloquent\Model;

class DataFasihPml extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'data_fasih_pml';
    protected $fillable = [
        'email',
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

    public function subsls()
    {
        return $this->belongsTo(MasterSubSls::class, 'subsls_code', 'code');
    }

    public function petugas()
    {
        return $this->belongsTo(Pml::class, 'email', 'email');
    }
}
