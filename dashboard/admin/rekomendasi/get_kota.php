<?php
$prov_id = $_GET['provinsi'];
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/city?province_id=$prov_id",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => ["key: 3j3U53Vc8e1df33c547f9d38s1Bv0sAj"],
]);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response, true);

echo '<option value="">-- Pilih Kota --</option>';
foreach ($data['data'] as $city) {
  echo '<option value="' . $city['id'] . '">' . $city['name'] . '</option>';
}