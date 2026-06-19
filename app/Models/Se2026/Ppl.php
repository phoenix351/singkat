<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class Ppl extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'ppl';
    protected $fillable = [
        'email',
        'nama'
    ];

    public $timestamps = false;
    public function fasih()
    {
        return $this->hasMany(DataFasih::class, 'email', 'email');
    }
}
