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
            $table->string('surat_keterangan_path')->nullable()->after('kelengkapan_dokumen');
            $table->string('akta_kelahiran_path')->nullable()->after('surat_keterangan_path');
            $table->string('ktp_path')->nullable()->after('akta_kelahiran_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn(['surat_keterangan_path', 'akta_kelahiran_path', 'ktp_path']);
        });
    }
};
