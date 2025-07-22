<?php
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/koneksi.php';

$mpdf = new \Mpdf\Mpdf();

$filter = $_GET['filter'] ?? 'all';
$whereFilter = '';

if ($filter === 'today') {
    $whereFilter = "AND DATE(s.created_at) = CURDATE()";
} elseif ($filter === 'week') {
    $whereFilter = "AND YEARWEEK(s.created_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $whereFilter = "AND MONTH(s.created_at) = MONTH(CURDATE()) AND YEAR(s.created_at) = YEAR(CURDATE())";
}

// Ambil data ranking dari database
$query = mysqli_query($koneksidb, "
    SELECT r.ranking, a.code, a.name, r.preference_value
    FROM results r
    JOIN alternatives a ON r.alternative_id = a.id
    JOIN scores s ON s.alternative_id = a.id
    WHERE 1=1 $whereFilter
    GROUP BY r.ranking, a.code, a.name, r.preference_value
    ORDER BY r.ranking ASC
");

$filterLabel = [
    'today' => 'Hari Ini',
    'week' => 'Minggu Ini',
    'month' => 'Bulan Ini',
    'all' => 'Semua Data'
][$filter];

$html = "
<h2 style='text-align:center;'>Laporan Pemilihan Jasa Pengiriman (Metode ARAS)</h2>
<p><strong>Filter Waktu:</strong> {$filterLabel}</p>
<table border='1' cellpadding='8' cellspacing='0' width='100%'>
    <thead>
        <tr>
            <th style='text-align:center;'>Peringkat</th>
            <th style='text-align:center;'>Kode</th>
            <th>Nama Ekspedisi</th>
            <th style='text-align:center;'>Nilai Preferensi (S<sub>i</sub>)</th>
        </tr>
    </thead>
    <tbody>
";

while ($row = mysqli_fetch_assoc($query)) {
    $html .= "
        <tr>
            <td align='center'>{$row['ranking']}</td>
            <td align='center'>{$row['code']}</td>
            <td>{$row['name']}</td>
            <td align='center'>{$row['preference_value']}</td>
        </tr>
    ";
}

$html .= '
    </tbody>
</table>
<br><p style="text-align:right;"><em>Dicetak pada: ' . date("d-m-Y H:i") . '</em></p>
';

$mpdf->WriteHTML($html);
$mpdf->Output('laporan_aras.pdf', 'I'); // 'I' untuk tampilkan di browser
?>