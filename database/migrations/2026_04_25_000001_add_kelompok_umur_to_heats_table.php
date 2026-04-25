<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom hanya jika belum ada
        if (!Schema::hasColumn('heats', 'kelompok_umur')) {
            Schema::table('heats', function (Blueprint $table) {
                $table->string('kelompok_umur', 20)->nullable()->after('jenis_kelamin');
            });
        }

        $newIndex = 'heats_event_heat_gender_ku_unique';
        $oldIndex = 'heats_event_id_heat_number_jenis_kelamin_unique';

        // Buat unique baru jika belum ada
        $newExists = DB::select("SHOW INDEX FROM heats WHERE Key_name = ?", [$newIndex]);
        if (empty($newExists)) {
            DB::statement("ALTER TABLE heats ADD UNIQUE {$newIndex} (event_id, heat_number, jenis_kelamin, kelompok_umur)");
        }

        // Hapus unique lama jika masih ada
        $oldExists = DB::select("SHOW INDEX FROM heats WHERE Key_name = ?", [$oldIndex]);
        if (!empty($oldExists)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement("ALTER TABLE heats DROP INDEX {$oldIndex}");
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function down(): void
    {
        $newIndex = 'heats_event_heat_gender_ku_unique';
        $oldIndex = 'heats_event_id_heat_number_jenis_kelamin_unique';

        $newExists = DB::select("SHOW INDEX FROM heats WHERE Key_name = ?", [$newIndex]);
        if (!empty($newExists)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::statement("ALTER TABLE heats DROP INDEX {$newIndex}");
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $oldExists = DB::select("SHOW INDEX FROM heats WHERE Key_name = ?", [$oldIndex]);
        if (empty($oldExists)) {
            DB::statement("ALTER TABLE heats ADD UNIQUE {$oldIndex} (event_id, heat_number, jenis_kelamin)");
        }

        if (Schema::hasColumn('heats', 'kelompok_umur')) {
            Schema::table('heats', function (Blueprint $table) {
                $table->dropColumn('kelompok_umur');
            });
        }
    }
};
