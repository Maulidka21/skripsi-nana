<?php
require_once '../../../config/config.php';
require_once '../../../config/koneksi.php';



// Ambil semua alternatif dan kriteria
$alternatif = mysqli_query($koneksidb, "SELECT * FROM alternatives ORDER BY code ASC");
$kriteria = mysqli_query($koneksidb, "SELECT * FROM criteria ORDER BY code ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = 'BATCH-' . date('YmdHis');
    $user_id = $_SESSION['user']['id'];

    foreach ($_POST['nilai'] as $alt_id => $krit) {
        foreach ($krit as $krit_id => $data) {
            $nilai = isset($data['score']) ? intval($data['score']) : null;
            $sub_id = isset($data['sub_criterion_id']) ? (int) $data['sub_criterion_id'] : null;

            mysqli_query($koneksidb, "INSERT INTO score_histories 
                (batch_id, alternative_id, criterion_id, score, sub_criterion_id, user_id, created_at) 
                VALUES (
                    '$batch_id', $alt_id, $krit_id, 
                    " . ($nilai ?? 'NULL') . ", " . ($sub_id ?: 'NULL') . ", 
                    $user_id, NOW()
                )");
        }
    }

    header("Location: index.php?success=tambah");
    exit;
}
?>

<?php if (isset($_GET['success'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($_GET['success'] == 'update'): ?>
        Swal.fire('Disimpan!', 'Penilaian berhasil diperbarui.', 'success');
        <?php elseif ($_GET['success'] == 'tambah'): ?>
        Swal.fire('Berhasil!', 'Penilaian baru berhasil ditambahkan ke histori.', 'success');
        <?php endif; ?>
    </script>
<?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire('Berhasil!', 'Data penilaian berhasil disimpan.', 'success');
    </script>
    
<div class="container mt-4">
    <form method="POST">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Alternatif \ Kriteria</th>
                    <?php
                    mysqli_data_seek($kriteria, 0);
                    while ($k = mysqli_fetch_assoc($kriteria)): ?>
                        <th><?= htmlspecialchars($k['code']) ?><br><small><?= htmlspecialchars($k['name']) ?></small></th>
                    <?php endwhile; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($alternatif, 0);
                mysqli_data_seek($kriteria, 0);
                while ($a = mysqli_fetch_assoc($alternatif)):
                    ?>
                    <tr>
                        <th><?= htmlspecialchars($a['code']) ?><br><small><?= htmlspecialchars($a['name']) ?></small></th>
                        <?php
                        mysqli_data_seek($kriteria, 0);
                        while ($k = mysqli_fetch_assoc($kriteria)):
                            $score_query = mysqli_query($koneksidb, "SELECT * FROM scores WHERE alternative_id = {$a['id']} AND criterion_id = {$k['id']}");
                            $score = mysqli_fetch_assoc($score_query);

                            $sub = mysqli_query($koneksidb, "SELECT * FROM sub_criteria WHERE criterion_id = {$k['id']}");
                            if (mysqli_num_rows($sub) > 0): ?>
                                <td>
                                    <select name="nilai[<?= $a['id'] ?>][<?= $k['id'] ?>][sub_criterion_id]" class="form-control" required>
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
                                    <input type="number" name="nilai[<?= $a['id'] ?>][<?= $k['id'] ?>][score]" 
                                           class="form-control" min="0" step="1" 
                                           value="<?= $score ? (int)$score['score'] : '' ?>" required>
                                </td>
                            <?php endif;
                        endwhile; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit" name="mode" value="tambah" class="btn btn-success">Tambah Penilaian Baru</button>
    </form>
</div>
