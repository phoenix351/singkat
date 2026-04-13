<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimKerja extends Model
{
    //
    protected $connection = 'man_management';
    protected $table = 'timkerja';
    public $timestamps = true;
    protected $fillable = [
        'tahun',
        'label'
    ];
}
