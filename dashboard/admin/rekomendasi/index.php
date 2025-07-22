<?php
include '../../../config/config.php';
include '../../../config/koneksi.php';
session_start();
cek_admin();

$page_title = 'Rekomendasi Ekspedisi';
$breadcrumbs = [
    ['title' => 'Home', 'link' => base_url('dashboard/admin')],
    ['title' => 'Rekomendasi Ekspedisi']
];

$content = base_path('dashboard/admin/rekomendasi/home.php');
include base_path('layout/main.php');
