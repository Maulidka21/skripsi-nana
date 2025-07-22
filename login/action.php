<?php
session_start();
include '../config/config.php';
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil user berdasarkan username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $koneksidb->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                $_SESSION['login_success'] = 'Selamat datang, ' . $user['username'] . '!';
                header("Location: " . base_url('dashboard/admin/'));
                exit;
            } else {
                $_SESSION['login_error'] = 'Role tidak dikenali!';
                header("Location: " . base_url('login/login.php'));
                exit;
            }
        } else {
            $_SESSION['login_error'] = 'Password salah!';
            header("Location: " . base_url('login/login.php'));
            exit;
        }
    } else {
        $_SESSION['login_error'] = 'Username tidak ditemukan!';
        header("Location: " . base_url('login/login.php'));
        exit;
    }
}