<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class DataFasih extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'data_fasih';
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
        //baru
        'edited_a',
        'rejected_a',
    ];

    public function subsls()
    {
        return $this->belongsTo(MasterSubSls::class, 'subsls_code', 'code');
    }

    public function petugas()
    {
        return $this->belongsTo(Ppl::class, 'email', 'email');
    }
}
