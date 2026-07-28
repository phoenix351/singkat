<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove duplicates from data_fasih
        DB::connection('sulutweb_se2026')->statement('
            DELETE t1 FROM data_fasih t1
            INNER JOIN data_fasih t2 
            WHERE 
                t1.id < t2.id AND 
                t1.email = t2.email AND 
                t1.subsls_code = t2.subsls_code
        ');

        Schema::connection('sulutweb_se2026')->table('data_fasih', function (Blueprint $table) {
            $table->unique(['email', 'subsls_code'], 'data_fasih_email_subsls_code_unique');
        });

        // Remove duplicates from data_fasih_pml
        DB::connection('sulutweb_se2026')->statement('
            DELETE t1 FROM data_fasih_pml t1
            INNER JOIN data_fasih_pml t2 
            WHERE 
                t1.id < t2.id AND 
                t1.email = t2.email AND 
                t1.subsls_code = t2.subsls_code
        ');

        Schema::connection('sulutweb_se2026')->table('data_fasih_pml', function (Blueprint $table) {
            $table->unique(['email', 'subsls_code'], 'data_fasih_pml_email_subsls_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sulutweb_se2026')->table('data_fasih', function (Blueprint $table) {
            $table->dropUnique('data_fasih_email_subsls_code_unique');
        });

        Schema::connection('sulutweb_se2026')->table('data_fasih_pml', function (Blueprint $table) {
            $table->dropUnique('data_fasih_pml_email_subsls_code_unique');
        });
    }
};
