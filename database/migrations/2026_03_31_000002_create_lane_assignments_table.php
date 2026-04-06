<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lane_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heat_id')->constrained('heats')->cascadeOnDelete();
            $table->integer('lane_number'); // 1-8, matches IoT "player"
            $table->foreignId('athlete_id')->constrained('athletes')->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->string('result_time', 50)->nullable(); // filled from IoT data
            $table->timestamps();
            $table->unique(['heat_id', 'lane_number']);
            $table->unique(['heat_id', 'athlete_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lane_assignments');
    }
};
