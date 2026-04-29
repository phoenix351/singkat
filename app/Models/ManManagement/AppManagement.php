<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;

class AppManagement extends Model
{
    //
    protected $connection = 'sulutweb_man_management';
    protected $table = 'application_management';
    public $timestamps = true;
    protected $fillable = [
        'label',
        'prefix',
        'deskripsi',
        'route_link',
        'navigation',
        'maintenance',
        'maintenance_message',
        'image_path',
    ];
}
