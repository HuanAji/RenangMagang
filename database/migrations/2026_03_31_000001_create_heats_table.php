<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->integer('heat_number');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('status', ['pending', 'active', 'completed'])->default('pending');
            $table->timestamps();
            $table->unique(['event_id', 'heat_number', 'jenis_kelamin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heats');
    }
};
