<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class HistoryPml extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'history_pml';
    protected $fillable = [
        'email',
        'kabkot_code',
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
}
