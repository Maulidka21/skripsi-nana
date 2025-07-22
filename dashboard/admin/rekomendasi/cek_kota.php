<?php
$province_id = 6; // ID DKI Jakarta
$api_key = "3j3U53Vc8e1df33c547f9d38s1Bv0sAj";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.rajaongkir.com/starter/city?province=$province_id",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => array(
    "key: $api_key"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo "<pre>";
  print_r(json_decode($response, true));
  echo "</pre>";
}
