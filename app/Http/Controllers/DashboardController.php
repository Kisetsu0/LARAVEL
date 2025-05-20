<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\RiwayatPrediksi;

class DashboardController extends Controller
{
    public function index()
    {
        $dataSensor = DB::table('sensor_data')
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get();

        $prediksiTerbaru = RiwayatPrediksi::latest('waktu_analisis')->first();
        $data = \App\Models\SensorData::orderBy('tanggal', 'desc')->take(7)->get();
        return view('dashboard', compact('dataSensor', 'prediksiTerbaru'));
    }
}
