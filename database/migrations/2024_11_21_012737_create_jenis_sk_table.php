<?php

use App\Models\JenisSK;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jenis_sk', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
        });
        $jenis = [
            "SK Pengangkatan Pertama",
            "SK Pembebasan Sementara",
            "SK Pengangkatan Kembali",
            "SK Kenaikan Jabatan",
        ];
        foreach ($jenis as $value) {
            $newJenis = new JenisSK(['nama'=>$value]);
            $newJenis->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_sk');
    }
};
