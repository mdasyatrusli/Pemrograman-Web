<?php
/**
 * =========================================================
 * FILE: index.php
 * FUNGSI: Pintu masuk utama aplikasi. Mengecek status login,
 *         lalu redirect otomatis ke halaman yang sesuai.
 * =========================================================
 */
session_start();

if (isset($_SESSION['id_user'])) {
    // Sudah login -> langsung ke dashboard
    header("Location: dashboard.php");
} else {
    // Belum login -> ke halaman login
    header("Location: auth/login.php");
}
exit;
