<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    protected $connection = 'sulutweb_man_management';
    protected $table = 'roles';
    public $timestamps = true;
    protected $fillable = [
        'type',
        'to_role_id',
        'roles',
        'app_id'
    ];
}
