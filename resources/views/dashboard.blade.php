<?php
@if($prediksiTerbaru)
    <div class="alert alert-info rounded shadow p-3 mb-4 bg-white border-start border-4 border-info">
        <strong>Notifikasi:</strong><br>
        Berdasarkan analisis terakhir ({{ $prediksiTerbaru->waktu_analisis }}), lampu diprediksi mulai redup pada
        <strong>minggu ke-{{ $prediksiTerbaru->prediksi_minggu }}</strong>
        (perkiraan jam ke-{{ number_format($prediksiTerbaru->prediksi_jam, 2) }}).
    </div>
@endif
