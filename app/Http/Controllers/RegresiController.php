<?php

namespace App\Http\Controllers;

use App\Models\LampData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegresiController extends Controller
{
    public function index()
    {
        $data = LampData::orderBy('tanggal', 'asc')->take(7)->get();

        $n = count($data);
        if ($n < 2) {
            return view('grafik', ['message' => 'Data belum cukup untuk analisis']);
        }

        $sum_x = $sum_y = $sum_xy = $sum_x2 = 0;

        foreach ($data as $d) {
            $x = $d->jam_pemakaian;
            $y = $d->lux;

            $sum_x += $x;
            $sum_y += $y;
            $sum_xy += $x * $y;
            $sum_x2 += $x * $x;
        }

        $b = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x2 - $sum_x * $sum_x);
        $a = ($sum_y - $b * $sum_x) / $n;

        $lux_threshold = 40000;
        $predicted_x = ($lux_threshold - $a) / $b;

        return view('grafik', [
            'data' => $data,
            'a' => round($a, 2),
            'b' => round($b, 2),
            'prediksi_jam' => round($predicted_x, 2),
            'prediksi_minggu' => ceil($predicted_x / 12 / 7), // anggap lampu 12 jam per hari
        ]);
    }

    public function hitungRegresi()
    {
    $data = DB::table('sensor_harian')->get();

    $n = count($data);
    if ($n == 0) {
        return response()->json(['message' => 'Data kosong']);
    }

    $sumX = $sumY = $sumXY = $sumX2 = 0;

    foreach ($data as $row) {
        $x = $row->jam_pemakaian;
        $y = $row->lux;

        $sumX += $x;
        $sumY += $y;
        $sumXY += $x * $y;
        $sumX2 += $x * $x;
    }

    // Hitung koefisien regresi linear
    $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    $intercept = ($sumY - $slope * $sumX) / $n;

    // Prediksi kapan lux di bawah 40000
    $lux_threshold = 40000;
    $prediksi_jam = ($lux_threshold - $intercept) / $slope;

    return view('grafik', [
        'data' => $data,
        'slope' => round($slope, 2),
        'intercept' => round($intercept, 2),
        'prediksi_jam' => round($prediksi_jam, 2),
        'lux_threshold' => $lux_threshold,
        ]);
    }
}
