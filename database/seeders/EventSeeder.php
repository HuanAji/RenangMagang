<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            'Gaya Bebas 50m',
            'Gaya Bebas 100m',
            'Gaya Bebas 200m',
            'Gaya Dada 50m',
            'Gaya Dada 100m',
            'Gaya Dada 200m',
            'Gaya Punggung 50m',
            'Gaya Punggung 100m',
            'Gaya Punggung 200m',
            'Gaya Kupu-kupu 50m',
            'Gaya Kupu-kupu 100m',
        ];

        foreach ($events as $event) {
            DB::table('events')->insert([
                'nama_event' => $event,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
