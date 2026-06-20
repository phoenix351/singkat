<?php

namespace App\Models\Se2026;

use Illuminate\Database\Eloquent\Model;

class Pml extends Model
{
    //
    protected $connection = 'sulutweb_se2026';
    protected $table = 'pml';
    protected $fillable = [
        'email',
        'nama'
    ];

    public $timestamps = false;
    public function fasih()
    {
        return $this->hasMany(DataFasihPml::class, 'email', 'email');
    }
}
