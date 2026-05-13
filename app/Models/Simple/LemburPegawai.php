<?php

namespace App\Models\Simple;

use App\Models\ManManagement\Pegawai;
use App\Simple\HasJobLogs;
use Illuminate\Database\Eloquent\Model;

class LemburPegawai extends Model
{
    //
    use HasJobLogs;
    protected $connection = 'sulutweb_simple';
    public $timestamps = true;
    protected $table = 'lembur_pegawai';
    protected $fillable = [
        'lembur_id',
        'pegawai_id',
        'tanggal',
        'jumlah_jam',
        'status',
        'catatan',
        'created_by',
        'edited_by',
        'jam_berangkat',
        'jam_pulang'
    ];

    protected $appends = ['status_detail'];

    public function getStatusDetailAttribute()
    {
        $statuses = [
            '1' => 'Pending',
            '2' => 'Disetujui Katim',
            '3' => 'Ditolak Katim',
            '4' => 'Disetujui Kabag',
            '5' => 'Ditolak Kabag',
        ];

        return $statuses[(string) $this->status] ?? 'Pending';
    }
    public function lembur()
    {
        return $this->belongsTo(Lembur::class);
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
    public function edited()
    {
        return $this->belongsTo(Pegawai::class, 'edited_by');
    }
}

