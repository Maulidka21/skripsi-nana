<?php
require_once '../../../config/config.php';
require_once '../../../config/koneksi.php';

if (!isset($_GET['batch'])) {
    header('Location: index.php');
    exit;
}

$batch_id = $_GET['batch'];

// Hapus dari score_histories
mysqli_query($koneksidb, "DELETE FROM score_histories WHERE batch_id = '$batch_id'");

header("Location: index.php?deleted=1");
exit;
?>