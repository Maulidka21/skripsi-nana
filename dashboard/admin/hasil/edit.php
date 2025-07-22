<?php
require_once '../../../config/config.php';
require_once '../../../config/koneksi.php';

$batch_id = $_GET['batch'] ?? null;
if (!$batch_id) {
    echo "Batch ID tidak ditemukan.";
    exit;
}

$alternatif = mysqli_query($koneksidb, "SELECT * FROM alternatives ORDER BY code ASC");
$kriteria = mysqli_query($koneksidb, "SELECT * FROM criteria ORDER BY code ASC");

// Simpan ke array
$alts = [];
while ($a = mysqli_fetch_assoc($alternatif)) {
    $alts[$a['id']] = $a;
}

$krit = [];
while ($k = mysqli_fetch_assoc($kriteria)) {
    $krit[$k['id']] = $k;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['nilai'] as $alt_id => $krit) {
        foreach ($krit as $krit_id => $data) {
            $nilai = isset($data['score']) ? intval($data['score']) : null;
            $sub_id = isset($data['sub_criterion_id']) ? (int) $data['sub_criterion_id'] : null;

            // Update data
            mysqli_query($koneksidb, "UPDATE score_histories SET 
                score = " . ($nilai ?? 'NULL') . ", 
                sub_criterion_id = " . ($sub_id ?: 'NULL') . " 
                WHERE batch_id = '$batch_id' AND alternative_id = $alt_id AND criterion_id = $krit_id
            ");
        }
    }

    header("Location: index.php?updated=1");
    exit;
}
?>

<div class="container mt-4">
    <h4>Edit Penilaian - <?= htmlspecialchars($batch_id) ?></h4>
    <form method="POST">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Alternatif \ Kriteria</th>
                    <?php foreach ($krit as $k): ?>
                        <th><?= htmlspecialchars($k['code']) ?><br><small><?= htmlspecialchars($k['name']) ?></small></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alts as $alt_id => $alt): ?>
                    <tr>
                        <th><?= htmlspecialchars($alt['code']) ?><br><small><?= htmlspecialchars($alt['name']) ?></small></th>
                        <?php foreach ($krit as $k_id => $k): ?>
                            <?php
                            $score_query = mysqli_query($koneksidb, "SELECT * FROM score_histories WHERE batch_id = '$batch_id' AND alternative_id = $alt_id AND criterion_id = $k_id");
                            $score = mysqli_fetch_assoc($score_query);

                            $sub = mysqli_query($koneksidb, "SELECT * FROM sub_criteria WHERE criterion_id = $k_id");
                            ?>
                            <?php if (mysqli_num_rows($sub) > 0): ?>
                                <td>
                                    <select name="nilai[<?= $alt_id ?>][<?= $k_id ?>][sub_criterion_id]" class="form-control" required>
                                        <option value="">-- Pilih --</option>
                                        <?php while ($s = mysqli_fetch_assoc($sub)): ?>
                                            <option value="<?= $s['id'] ?>" <?= $score && $score['sub_criterion_id'] == $s['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s['name']) ?> (<?= (int)$s['value'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                            <?php else: ?>
                                <td>
                                    <input type="number" name="nilai[<?= $alt_id ?>][<?= $k_id ?>][score]"
                                           class="form-control" min="0" step="1"
                                           value="<?= $score ? (int)$score['score'] : '' ?>" required>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
    </form>
</div>