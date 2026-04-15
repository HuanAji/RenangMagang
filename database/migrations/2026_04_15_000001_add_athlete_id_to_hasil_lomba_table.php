<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_lomba', function (Blueprint $table) {
            // Tambah kolom athlete_id (nullable agar data lama tidak error)
            $table->unsignedBigInteger('athlete_id')->nullable()->after('player');
            $table->string('athlete_name', 150)->nullable()->after('athlete_id'); // Cache nama agar tetap muncul walau data atlet dihapus
            $table->foreign('athlete_id')->references('id')->on('athletes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hasil_lomba', function (Blueprint $table) {
            $table->dropForeign(['athlete_id']);
            $table->dropColumn(['athlete_id', 'athlete_name']);
        });
    }
};
