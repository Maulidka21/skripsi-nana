<?php
$destination = $_GET['tujuan_kecamatan'];
$weight = $_GET['berat'] * 1000; // kg ke gram
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/cost",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => http_build_query([
    "origin" => "501", // contoh: Semarang
    "origin_type" => "subdistrict",
    "destination" => $destination,
    "destination_type" => "subdistrict",
    "weight" => $weight,
    "courier" => "jnt"
  ]),
  CURLOPT_HTTPHEADER => ["key: 3j3U53Vc8e1df33c547f9d38s1Bv0sAj"],
]);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response, true);

$results = $data['data']['results'][0]['costs'] ?? [];
?>

<h3>📦 Hasil Rekomendasi Ekspedisi J&T</h3>

<table border="1" cellpadding="10">
  <tr>
    <th>Layanan</th>
    <th>Harga</th>
    <th>Estimasi (hari)</th>
  </tr>
  <?php foreach ($results as $r): ?>
    <tr>
      <td><?= $r['service'] ?></td>
      <td>Rp <?= number_format($r['cost'][0]['value']) ?></td>
      <td><?= $r['cost'][0]['etd'] ?></td>
    </tr>
  <?php endforeach ?>
</table>
<a href="index.php">← Kembali</a>
