<?php

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
        Schema::table('athletes', function (Blueprint $table) {
            $table->integer('umur')->nullable()->after('nama');
            $table->string('provinsi')->nullable()->after('asal_club_sekolah');
            $table->string('kabupaten_kota')->nullable()->after('provinsi');
            $table->string('kelengkapan_dokumen')->nullable()->default('Belum Lengkap')->after('id_card_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn(['umur', 'provinsi', 'kabupaten_kota', 'kelengkapan_dokumen']);
        });
    }
};
