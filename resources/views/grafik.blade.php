<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Regresi Linear</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 40px;
            color: #333;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        canvas {
            display: block;
            margin: 0 auto 30px;
            max-width: 100%;
            min-height: 400px;
        }
        .info {
            background: #ecf0f1;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .info p {
            margin: 6px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Grafik Prediksi Intensitas Cahaya Lampu Operasi</h2>

        @if(isset($message))
            <p style="color:red;">{{ $message }}</p>
        @else
            <canvas id="grafikLux"></canvas>

            <div class="info">
                <p><strong>Persamaan Regresi:</strong> y = {{ $a }} + {{ $b }}x</p>
                <p><strong>Prediksi Redup:</strong> Setelah <strong>{{ $prediksi_jam }}</strong> jam (~ Minggu ke-<strong>{{ $prediksi_minggu }}</strong>)</p>
            </div>

            <script>
                const xValues = @json($data->pluck('jam_pemakaian'));
                const yActual = @json($data->pluck('lux'));
                const yRegresi = xValues.map(x => {{ $a }} + {{ $b }} * x);

                const data = {
                    labels: xValues,
                    datasets: [
                        {
                            label: 'Lux Harian',
                            data: yActual,
                            borderColor: '#3498db',
                            backgroundColor: '#3498db',
                            fill: false,
                            tension: 0.2
                        },
                        {
                            label: 'Garis Regresi',
                            data: yRegresi,
                            borderColor: '#e74c3c',
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0
                        }
                    ]
                };

                const config = {
                    type: 'line',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: {
                                display: true,
                                text: 'Prediksi Redup Berdasarkan Jam Pemakaian',
                                color: '#34495e',
                                font: { size: 16 }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jam Pemakaian',
                                    color: '#666'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Intensitas Lux',
                                    color: '#666'
                                }
                            }
                        }
                    }
                };

                new Chart(document.getElementById('grafikLux'), config);
            </script>
        @endif

         <div class="text-start mt-3">
            <a href="/data-sensor" class="btn btn-primary">🔙 Kembali</a>
        </div>
    </div>
</body>
</html>
