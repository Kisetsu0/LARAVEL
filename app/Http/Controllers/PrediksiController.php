<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RiwayatPrediksi;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    public function analisisRegresi()
    {
        // Ambil 7 data terakhir dari tabel sensor_data
        $data = DB::table('sensor_data')
                    ->orderBy('tanggal', 'desc')
                    ->limit(7)
                    ->get()
                    ->reverse(); // supaya urut dari lama ke baru

        if (count($data) < 7) {
            return response()->json(['message' => 'Data kurang dari 7 hari.'], 400);
        }

        // Ambil array jam dan lux
        $x = $data->pluck('jam_pemakaian')->toArray(); // misalnya: [1, 2, 3, ...]
        $y = $data->pluck('lux')->toArray();           // misalnya: [70000, 68000, ...]

        // Hitung regresi linear
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn($xi, $yi) => $xi * $yi, $x, $y));
        $sumX2 = array_sum(array_map(fn($xi) => $xi * $xi, $x));

        $a = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $b = ($sumY - $a * $sumX) / $n;

        // Prediksi kapan lux < 40000 → 40000 = a*x + b => x = (40000 - b) / a
        $prediksi_jam = ($a != 0) ? (40000 - $b) / $a : null;
        $prediksi_minggu = ceil($prediksi_jam / 24 / 7); // diasumsikan 1 hari = 1 jam nyala

        // Simpan ke database
        RiwayatPrediksi::create([
            'a' => $a,
            'b' => $b,
            'prediksi_jam' => $prediksi_jam,
            'prediksi_minggu' => $prediksi_minggu,
            'waktu_analisis' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Analisis berhasil',
            'a' => $a,
            'b' => $b,
            'prediksi_jam' => $prediksi_jam,
            'prediksi_minggu' => $prediksi_minggu,
        ]);
    }
}

