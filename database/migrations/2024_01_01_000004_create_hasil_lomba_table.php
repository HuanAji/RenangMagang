<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_lomba', function (Blueprint $table) {
            $table->id();
            $table->string('player', 100);
            $table->integer('waktu_ms')->default(0);
            $table->integer('waktu_detik')->default(0);
            $table->integer('waktu_menit')->default(0);
            $table->string('waktu_format', 50);
            $table->timestamp('timestamp')->useCurrent();
            $table->index('player');
            $table->index('timestamp');
            $table->index('waktu_format');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_lomba');
    }
};
