<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSK extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'jenis_sk';
    protected $fillable = ['nama','id'];
}
