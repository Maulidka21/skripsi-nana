<?php
include '../../../config/koneksi.php';
cek_admin();

// Ambil ID dari parameter
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Cek dan ambil data alternatif
$query = mysqli_query($koneksidb, "SELECT * FROM alternatives WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
        Swal.fire('Error!', 'Data tidak ditemukan.', 'error').then(() => {
            window.location.href = '" . base_url('dashboard/admin/alternatif/index.php') . "';
        });
    </script>";
    exit;
}

// Proses update saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);

    if ($code === '' || $name === '') {
        $alert = "Swal.fire('Gagal!', 'Kode dan Nama tidak boleh kosong.', 'error');";
    } else {
        // Cek kode unik kecuali diri sendiri
        $cek = mysqli_query($koneksidb, "SELECT * FROM alternatives WHERE code = '$code' AND id != $id");
        if (mysqli_num_rows($cek) > 0) {
            $alert = "Swal.fire('Gagal!', 'Kode alternatif sudah digunakan oleh data lain.', 'error');";
        } else {
            $update = mysqli_query($koneksidb, "UPDATE alternatives SET code = '$code', name = '$name' WHERE id = $id");
            if ($update) {
                header('Location: ' . base_url('dashboard/admin/alternatif/index.php?success=update'));
                exit;
            } else {
                $alert = "Swal.fire('Gagal!', 'Gagal menyimpan perubahan.', 'error');";
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Alternatif</h3>
        <div class="card-tools">
            <a href="<?= base_url('dashboard/admin/alternatif/index.php') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Kode Alternatif</label>
                <input type="text" name="code" class="form-control" required value="<?= htmlspecialchars($data['code']) ?>">
            </div>

            <div class="form-group">
                <label>Nama Ekspedisi</label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($data['name']) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($alert)): ?>
<script><?= $alert ?></script>
<?php endif; ?>
