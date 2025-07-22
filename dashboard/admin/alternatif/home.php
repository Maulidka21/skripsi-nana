<?php
require_once '../../../config/config.php';
require_once '../../../config/koneksi.php';



$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksidb, $_GET['search']) : '';
$query = "SELECT * FROM alternatives";

if (!empty($search)) {
    $query .= " WHERE name LIKE '%$search%' OR code LIKE '%$search%'";
}

$alternatif = mysqli_query($koneksidb, $query);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="create.php" class="btn btn-primary mb-3">Tambah Jasa Pengiriman</a>
        <form class="form-inline" method="GET">
            <input type="text" name="search" class="form-control" placeholder="Cari Ekspedisi/Kode..."
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit" class="btn btn-secondary ml-2">Cari</button>
        </form>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Kode</th>
                <th>Nama Jasa Pengiriman</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($alternatif)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['code']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id'] ?>)">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Kode</th>
                <th>Nama Jasa Pengiriman</th>
                <th>Created At</th>
                <th>Aksi</th>
            </tr>
        </tfoot>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        const baseUrl = "<?= base_url('dashboard/admin/alternatif/delete.php') ?>";
        Swal.fire({
            title: 'Yakin ingin menghapus ekspedisi ini?',
            text: "Data ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = baseUrl + '?id=' + id;
            }
        })
    }
</script>
