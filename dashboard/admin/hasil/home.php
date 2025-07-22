<?php
require_once '../../../config/config.php';
require_once '../../../config/koneksi.php';

// Ambil semua riwayat unik
$riwayatList = mysqli_query($koneksidb, "SELECT DISTINCT batch_id, created_at FROM score_histories ORDER BY created_at DESC");

// Ambil data alternatif dan kriteria
$alternatif = mysqli_query($koneksidb, "SELECT * FROM alternatives ORDER BY code ASC");
$criteria = mysqli_query($koneksidb, "SELECT * FROM criteria ORDER BY id ASC");

// Simpan ke array
$alts = [];
while ($a = mysqli_fetch_assoc($alternatif)) {
    $alts[$a['id']] = $a;
}

$krit = [];
while ($k = mysqli_fetch_assoc($criteria)) {
    $krit[$k['id']] = $k;
}

$filter = $_GET['filter'] ?? 'all';
$whereFilter = "";
if ($filter === 'today') {
    $whereFilter = "AND DATE(created_at) = CURDATE()";
} elseif ($filter === 'week') {
    $whereFilter = "AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter === 'month') {
    $whereFilter = "AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
}
?>

<!-- Notifikasi jika berhasil hapus -->
<?php if (isset($_GET['deleted'])): ?>
  <script>
    alert("Riwayat penilaian berhasil dihapus.");
  </script>
<?php endif; ?>

<form method="GET" class="form-inline mb-3">
  <label for="filter">Lihat hasil: </label>
  <select name="filter" class="form-control ml-2" onchange="this.form.submit()">
    <option value="today" <?= $filter == 'today' ? 'selected' : '' ?>>Hari ini</option>
    <option value="week" <?= $filter == 'week' ? 'selected' : '' ?>>Minggu ini</option>
    <option value="month" <?= $filter == 'month' ? 'selected' : '' ?>>Bulan ini</option>
    <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>Semua Data</option>
  </select>
</form>

<?php
if (mysqli_num_rows($riwayatList) === 0) {
    echo "<p>Belum ada data penilaian.</p>";
} else {
    while ($riwayat = mysqli_fetch_assoc($riwayatList)) {
        $batch_id = $riwayat['batch_id'];
        echo "<h5 class='mt-4 d-flex justify-content-between align-items-center'>
                <span>Ranking Penilaian: " . date('d-m-Y H:i', strtotime($riwayat['created_at'])) . "</span>
                <span>
                    <a href='edit.php?batch=<?= $batch_id ?>' class='btn btn-sm btn-warning ml-2'>Edit Riwayat Ini</a>
                    <a href='delete.php?batch=$batch_id' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus riwayat ini?')\">Hapus Riwayat Ini</a>
                </span>
              </h5>";

        // Ambil semua nilai berdasarkan riwayat
        $matrix = [];
        foreach ($alts as $alt_id => $alt) {
            foreach ($krit as $k_id => $k) {
                $score = mysqli_query($koneksidb, "SELECT * FROM score_histories WHERE alternative_id = $alt_id AND criterion_id = $k_id AND batch_id = '$batch_id' $whereFilter");
                $row = mysqli_fetch_assoc($score);
                if ($row) {
                    if ($row['sub_criterion_id']) {
                        $sub = mysqli_fetch_assoc(mysqli_query($koneksidb, "SELECT value FROM sub_criteria WHERE id = " . $row['sub_criterion_id']));
                        $value = $sub['value'];
                    } else {
                        $value = $row['score'];
                    }
                } else {
                    $value = 0;
                }
                $matrix[$alt_id][$k_id] = $value;
            }
        }

        // Normalisasi
        $normalized = [];
        $sums = [];
        foreach ($krit as $k_id => $k) {
            $sum = 0;
            foreach ($matrix as $alt) {
                $sum += $alt[$k_id];
            }
            $sums[$k_id] = $sum;
        }
        foreach ($matrix as $alt_id => $nilai) {
            foreach ($nilai as $k_id => $val) {
                $normalized[$alt_id][$k_id] = $sums[$k_id] != 0 ? $val / $sums[$k_id] : 0;
            }
        }

        // Normalisasi Terbobot
        $weighted = [];
        foreach ($normalized as $alt_id => $nilai) {
            foreach ($nilai as $k_id => $val) {
                $weighted[$alt_id][$k_id] = $val * $krit[$k_id]['weight'];
            }
        }

        // Preferensi
        $preferensi = [];
        foreach ($weighted as $alt_id => $nilai) {
            $preferensi[$alt_id] = round(array_sum($nilai), 4);
        }

        arsort($preferensi);
        $ranking_array = [];
        $rank = 1;
        foreach ($preferensi as $aid => $val) {
            $ranking_array[] = [
                'ranking' => $rank,
                'code' => $alts[$aid]['code'],
                'name' => $alts[$aid]['name'],
                'preference_value' => $val
            ];
            $rank++;
        }

        $top = $ranking_array[0];
?>

<div class="card p-3 mb-3">
  <strong>📌 <?= $top['code'] ?> - <?= $top['name'] ?> (<?= round($top['preference_value'] * 100, 2) ?>% cocok)</strong>
  <button class="btn btn-sm btn-link mt-2" onclick="document.getElementById('ranking-list-<?= $batch_id ?>').classList.toggle('d-none')">
    Lihat Semua Ranking ▼
  </button>
  <div id="ranking-list-<?= $batch_id ?>" class="d-none mt-2">
    <table class='table table-bordered'>
    <thead><tr><th>Ranking</th><th>Kode</th><th>Nama</th><th>Skor ARAS</th></tr></thead>
    <tbody>
    <?php foreach ($ranking_array as $row): ?>
      <tr>
        <td><?= $row['ranking'] ?></td>
        <td><?= $row['code'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= round($row['preference_value'] * 100, 2) ?>%</td>
      </tr>
    <?php endforeach ?>
    </tbody></table>
  </div>
</div>

<?php
    }
}
?>