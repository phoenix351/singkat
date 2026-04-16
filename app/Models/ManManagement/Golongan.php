<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    //
    protected $connection = 'sulutweb_man_management';
    protected $table = 'golongan';

    public $timestamps = false;

    protected $fillable = [
        'kelompok_asn',
        'golongan',
        'pangkat',
    ];
}
