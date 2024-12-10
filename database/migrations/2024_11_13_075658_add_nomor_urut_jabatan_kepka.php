<?php

use App\Models\Jabatan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->integer('nomor_urut_kepka')->default(999);
        });
        $jabatans = Jabatan::get();
        foreach ($jabatans as $jabatan) {
            # code...
            $jabatan->nomor_urut_kepka = $jabatan->id;
            $jabatan->save();    
        }
        
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropColumn('nomor_urut_kepka');
        });
        //
    }
};
