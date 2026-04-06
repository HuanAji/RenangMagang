<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('kategori_umur', 50)->nullable();
            $table->string('seed_time', 50)->nullable();
            $table->timestamps();
            $table->unique(['athlete_id', 'event_id']);
            $table->index('event_id');
            $table->index('kategori_umur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
