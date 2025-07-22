<?php
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/province",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => ["key: 3j3U53Vc8e1df33c547f9d38s1Bv0sAj"],
]);
$response = curl_exec($curl);
curl_close($curl);
$data = json_decode($response, true);
?>

<div class="container mt-4">
  <h3>Form Rekomendasi Pengiriman</h3>

  <form method="GET" action="hasil.php">
    <div class="form-group">
      <label>Tujuan Provinsi</label>
      <select name="tujuan_provinsi" id="provinsi" class="form-control" required>
        <option value="">-- Pilih Provinsi --</option>
        <?php foreach ($data['data'] as $prov): ?>
          <option value="<?= $prov['id'] ?>"><?= $prov['name'] ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="form-group">
      <label>Tujuan Kota</label>
      <select name="tujuan_kota" id="kota" class="form-control" required>
        <option value="">-- Pilih Kota --</option>
      </select>
    </div>

    <div class="form-group">
      <label>Tujuan Kecamatan</label>
      <select name="tujuan_kecamatan" id="kecamatan" class="form-control" required>
        <option value="">-- Pilih Kecamatan --</option>
      </select>
    </div>

    <div class="form-group">
      <label>Berat Barang (kg)</label>
      <input type="number" name="berat" class="form-control" min="1" required>
    </div>

    <button type="submit" class="btn btn-primary">Lihat Rekomendasi</button>
  </form>
</div>

<!-- JQuery & Ajax -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#provinsi').on('change', function () {
    let prov = $(this).val();
    $('#kota').html('<option value="">Memuat...</option>');
    $.get('get_kota.php', { provinsi: prov }, function (data) {
      $('#kota').html(data);
      $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
    });
  });

  $('#kota').on('change', function () {
    let kota = $(this).val();
    $('#kecamatan').html('<option value="">Memuat...</option>');
    $.get('get_kecamatan.php', { kota: kota }, function (data) {
      $('#kecamatan').html(data);
    });
  });
</script>