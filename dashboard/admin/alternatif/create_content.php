<?php
include '../../../config/koneksi.php';
cek_admin();

$alert = ''; // digunakan untuk menyimpan skrip SweetAlert

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);

    if ($code === '' || $name === '') {
        $alert = "Swal.fire('Gagal!', 'Kode dan Nama tidak boleh kosong.', 'error');";
    } else {
        // Cek duplikat kode
        $cek = mysqli_query($koneksidb, "SELECT * FROM alternatives WHERE code = '$code'");
        if (mysqli_num_rows($cek) > 0) {
            $alert = "Swal.fire('Gagal!', 'Kode Ekspedisi sudah digunakan.', 'error');";
        } else {
            $query = mysqli_query($koneksidb, "INSERT INTO alternatives (code, name) VALUES ('$code', '$name')");
            if ($query) {
                header('Location: ' . base_url('dashboard/admin/alternatif/index.php?success=simpan'));
                exit;
            } else {
                $alert = "Swal.fire('Gagal!', 'Gagal menambahkan data alternatif.', 'error');";
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Alternatif Ekspedisi</h3>
        <div class="card-tools">
            <a href="<?= base_url('dashboard/admin/alternatif/index.php') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Kode Ekspedisi</label>
                <input type="text" name="code" class="form-control" required placeholder="Contoh: A01">
            </div>

            <div class="form-group">
                <label>Nama Ekspedisi</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (!empty($alert)): ?>
    <script><?= $alert ?></script>
<?php endif; ?>