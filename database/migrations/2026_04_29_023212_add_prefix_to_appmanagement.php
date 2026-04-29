<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'sulutweb_man_management';
    public function up(): void
    {
        Schema::table('application_management', function (Blueprint $table) {
            //
            $table->string('prefix', 30)->after('label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_management', function (Blueprint $table) {
            //
            $table->dropColumn('prefix');
        });
    }
};
