<?php

namespace App\Models\Simple;

use App\Simple\HasJobLogs;
use Illuminate\Database\Eloquent\Model;

class LaporanUpload extends Model
{
    //
    use HasJobLogs;
    protected $table = 'sulutweb_simple.laporan_lembur';

    protected $fillable = [
        'lembur_id',
        'file_path',
    ];

    public function lembur()
    {
        return $this->belongsTo(Lembur::class);
    }
}
