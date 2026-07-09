<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sulutweb_simple';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->foreignId('tim_penanggung_jawab_id')
                ->nullable()
                ->after('tim_id')
                ->constrained('sulutweb_man_management.timkerja')
                ->onUpdate('restrict')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembur', function (Blueprint $table) {
            $table->dropForeign(['tim_penanggung_jawab_id']);
            $table->dropColumn('tim_penanggung_jawab_id');
        });
    }
};
