<?php

use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Date;
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
            Schema::table('pegawai', function (Blueprint $table) {
                $table->dropColumn('usia');
                $table->dropColumn('tmt_pensiun');
                $table->dropColumn('tanggal_lahir');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->date('tanggal_lahir')->default(Date::now());
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
        $pegawais = Pegawai::get();
        foreach ($pegawais as $pegawai) {
            # code...
            $pegawai->tanggal_lahir = $this->calculateTanggalLahir($pegawai->nip);
            $pegawai->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
    private function calculateTanggalLahir($nip)
    {
        $year = substr($nip, 0, 4);
        $month = substr($nip, 4, 2);
        $day = substr($nip, 6, 2);
        $birthDate = Carbon::createFromDate($year, $month, $day);


        return $birthDate;
    }
};