<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensorHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $tanggal = Carbon::now()->subDays(14); // 14 hari ke belakang

    for ($i = 0; $i < 14; $i++) {
        DB::table('sensor_harian')->insert([
            'tanggal' => $tanggal->copy()->addDays($i)->toDateString(),
            'jam_pemakaian' => rand(6, 12), // pemakaian antara 6-12 jam
            'lux' => 50000 - ($i * rand(500, 1000)), // lux menurun per hari
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
}
