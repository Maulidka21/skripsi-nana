<?php
$city_id = $_GET['kota'];
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/district?city_id=$city_id",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => ["key: 3j3U53Vc8e1df33c547f9d38s1Bv0sAj"],
]);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response, true);

echo '<option value="">-- Pilih Kecamatan --</option>';
foreach ($data['data'] as $dist) {
  echo '<option value="' . $dist['id'] . '">' . $dist['name'] . '</option>';
}
