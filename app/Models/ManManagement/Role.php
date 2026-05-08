<?php

namespace App\Models\ManManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    public static function currentRole()
    {
        $prefix = Route::current()->getPrefix();
        $current_app = AppManagement::where('prefix', $prefix)->value('id');
        $roles = 'viewer';

        if ($current_app) {
            $roles_on_app = Role::where('app_id', $current_app)->get();

            $keanggotaan = AnggotaTimKerja::where('pegawai_id', Auth::user()->id)->pluck('tim_id')->toArray();

            $tim_roles = $roles_on_app->where('type', 'tim')->whereIn('to_role_id', $keanggotaan)->pluck('roles')->toArray();
            $unit_roles = $roles_on_app->where('type', 'unit')->where('to_role_id', Auth::user()->id)->pluck('roles')->toArray();
            $all_type_roles = $roles_on_app->where('type', 'all')->pluck('roles')->toArray();

            $all_user_roles = array_merge($tim_roles, $unit_roles, $all_type_roles);

            if (in_array('admin', $all_user_roles)) {
                $roles = 'admin';
            } elseif (in_array('operator', $all_user_roles)) {
                $roles = 'operator';
            }
        }

        return $roles;
    }
}
