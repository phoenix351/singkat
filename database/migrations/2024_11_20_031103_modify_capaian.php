<?php

use App\Models\Capaian;
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
        try {
            //code...
            Capaian::truncate();
            Schema::table('capaians', function (Blueprint $table) {
                $table->float('angka_kredit_akumulasi')->nullable();
                $table->date('bulan_mulai')->nullable()->change();
                $table->date('bulan_selesai')->nullable();
                $table->date('tmt_sk')->nullable();
                $table->string('nomor_sk', 255)->nullable();
                $table->string('jenis_sk', 2)->nullable();
                $table->string('predikat_id', 2)->nullable()->change();
                $table->dropColumn('angka_kredit_dasar');  
                $table->dropColumn('bulan_akhir');   
                $table->dropColumn('periode');   
                $table->dropColumn('tahun');   
            });
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capaians', function (Blueprint $table) {
            $table->dropColumn('bulan_mulai')->nullable();
            $table->dropColumn('bulan_selesai')->nullable();
            $table->dropColumn('tmt_sk')->nullable();
            $table->dropColumn('nomor_sk', 255)->nullable();
            $table->dropColumn('jenis_sk', 2)->nullable();
            $table->dropColumn('predikat_id', 2)->nullable();
            $table->float('angka_kredit_dasar');   

        });
        //
    }
};
