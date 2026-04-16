<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;

class Satker extends Model
{
    //
    protected $connection = 'man_management';
    protected $table = 'satker';

    public $timestamps = false;

    protected $fillable = [
        'kode_satker',
        'nama_satker',
    ];
}
