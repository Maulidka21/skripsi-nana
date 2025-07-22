<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/province",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => array(
    "key: 3j3U53Vc8e1df33c547f9d38s1Bv0sAj"
  ),
));

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

// Debug tampilkan semua isi response
echo "<pre>";
print_r($data);
echo "</pre>";

// Tampilkan provinsi
if (isset($data['data'])) {
    echo "<h2>Daftar Provinsi</h2><ul>";
    foreach ($data['data'] as $provinsi) {
        echo "<li>{$provinsi['id']} - {$provinsi['name']}</li>";
    }
    echo "</ul>";
} else {
    echo "Gagal mengambil data provinsi.";
}
?>
