<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class HistoryPpl extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'history_ppl';
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
        //baru
        'edited_a',
        'rejected_a',
    ];
    public $timestamps = true;
}
