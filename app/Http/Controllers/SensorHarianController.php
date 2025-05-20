<?php

namespace App\Http\Controllers;

use App\Models\SensorHarian;
use App\Models\RiwayatPrediksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SensorHarianController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'lux' => 'required|numeric',
            'jam_pemakaian' => 'required|numeric',
        ]);

        // Simpan ke database
        $sensor = new SensorHarian();
        $sensor->tanggal = Carbon::now()->format('Y-m-d');
        $sensor->lux = $validated['lux'];
        $sensor->jam_pemakaian = $validated['jam_pemakaian'];
        $sensor->save();

        // Jalankan regresi jika sudah ada 7 data
        $this->cekDanAnalisa();

        return response()->json(['message' => 'Data tersimpan dan dicek.']);
    }

    private function cekDanAnalisa()
    {
        $data = SensorHarian::orderBy('tanggal', 'asc')->take(7)->get();

        if ($data->count() < 7) {
            return; // Belum cukup data
        }

        // Regresi linear: x = hari ke-, y = lux
        $x = range(1, 7);
        $y = $data->pluck('lux')->toArray();

        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn($i) => $x[$i] * $y[$i], array_keys($x)));
        $sumX2 = array_sum(array_map(fn($i) => $x[$i] * $x[$i], array_keys($x)));

        // Rumus regresi linear: y = a + bx
        $b = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $a = ($sumY - $b * $sumX) / $n;

        // Tentukan hari ke berapa nilai lux menyentuh ambang batas
        $batas_kuning = 40000;
        $batas_merah = 20000;

        $x_kuning = ceil(($batas_kuning - $a) / $b); // prediksi hari ke-
        $x_merah = ceil(($batas_merah - $a) / $b);

        // Tanggal prediksi
        $tanggal_prediksi_kuning = Carbon::now()->addDays($x_kuning - 7)->format('Y-m-d');
        $tanggal_prediksi_merah = Carbon::now()->addDays($x_merah - 7)->format('Y-m-d');

        // Simpan ke riwayat
        RiwayatPrediksi::create([
            'minggu_ke' => ceil($data->count() / 7),
            'tanggal_analisis' => now()->format('Y-m-d'),
            'prediksi_maintenance' => $tanggal_prediksi_kuning,
            'prediksi_penggantian' => $tanggal_prediksi_merah,
            'catatan' => "a=$a, b=$b",
        ]);
    }
}
