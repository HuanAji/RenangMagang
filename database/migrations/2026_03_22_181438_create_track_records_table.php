<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            $table->string('nama_kompetisi');
            $table->string('nomor_lomba');
            $table->string('durasi_renang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_records');
    }
};
