<?php
include '../../../config/config.php';

$page_title = 'Edit Jasa Ekspedisi';
$breadcrumbs = [
    ['title' => 'Home', 'link' => base_url('dashboard/admin')],
    ['title' => 'Manajemen Jasa Ekspedisi', 'link' => base_url('dashboard/admin/alternatif/index.php')],
    ['title' => 'Edit Jasa Ekspedisi']
];

$content = base_path('dashboard/admin/alternatif/edit_content.php');
include base_path('layout/main.php');
