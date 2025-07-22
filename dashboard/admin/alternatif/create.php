<?php
include '../../../config/config.php';

$page_title = 'Tambah Jasa Ekspedisi';
$breadcrumbs = [
    ['title' => 'Home', 'link' => base_url('dashboard/admin')],
    ['title' => 'Manajemen Jasa Ekspedisi', 'link' => base_url('dashboard/admin/alternatif/index.php')],
    ['title' => 'Tambah Jasa Ekspedisi']
];

$content = base_path('dashboard/admin/alternatif/create_content.php');
include base_path('layout/main.php');
