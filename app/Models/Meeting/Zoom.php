<?php

namespace App\Models\Meeting;

use App\Models\ManManagement\Pegawai;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zoom extends Model
{
    //
    use HasFactory;
    protected $fillable = ['user_id'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }
}
