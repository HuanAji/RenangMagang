<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('id_card_path', 500)->nullable();
            $table->string('asal_club_sekolah')->nullable();
            $table->timestamps();
            $table->index('nama');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
